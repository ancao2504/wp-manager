<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        // Apply authentication middleware to all methods
        $this->middleware('auth');
        
        // Apply permission middleware to specific methods
        $this->middleware('permission:view users')->only(['index', 'show']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:delete users')->only(['destroy', 'bulkDelete']);
        $this->middleware('permission:manage roles')->only(['roles', 'createRole', 'storeRole', 'editRole', 'updateRole', 'destroyRole']);
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Lọc theo vai trò nếu có
        if ($request->has('role') && $request->role != '') {
            $query->role($request->role);
        }

        // Lọc theo trạng thái nếu có
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->with('roles')->latest()->paginate(15);
        $roles = Role::all();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();

        return view('users.index', compact('users', 'roles', 'totalUsers', 'activeUsers', 'inactiveUsers'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        $loginHistory = $user->loginHistory ?? collect();
        
        return view('users.show', compact('user', 'loginHistory'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Prevent editing super admin if not super admin
        if ($user->hasRole('super admin') && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to edit a super admin.');
        }

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating super admin if not super admin
        if ($user->hasRole('super admin') && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.index')
                ->with('error', 'You do not have permission to update a super admin.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // If changing super admin role, validate permissions
        if ($user->hasRole('super admin') && $request->role !== 'super admin' && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.edit', $user)
                ->with('error', 'Only super admin can change the role of another super admin.');
        }

        // Xóa tất cả vai trò hiện tại và gán vai trò mới
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Kiểm tra không cho phép xóa chính mình
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        // Prevent deleting super admin if not super admin
        if ($user->hasRole('super admin') && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Only a super admin can delete another super admin.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $userIds = $request->user_ids;
        
        // Check if trying to delete self
        if (in_array(Auth::id(), $userIds)) {
            return response()->json(['error' => 'You cannot delete yourself.'], 422);
        }

        // Check for super admin users
        $superAdminCount = User::whereIn('id', $userIds)
            ->whereHas('roles', function($q) {
                $q->where('name', 'super admin');
            })->count();

        // Only super admin can delete super admin
        if ($superAdminCount > 0 && !Auth::user()->hasRole('super admin')) {
            return response()->json(['error' => 'Only a super admin can delete super admin users.'], 422);
        }

        User::whereIn('id', $userIds)->delete();

        return response()->json(['success' => 'Users deleted successfully.']);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Check if trying to toggle self
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot change your own status.'], 422);
        }

        // Prevent toggling super admin if not super admin
        if ($user->hasRole('super admin') && !Auth::user()->hasRole('super admin')) {
            return response()->json(['error' => 'Only a super admin can change the status of another super admin.'], 422);
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'success' => 'User status updated successfully.',
            'status' => $user->status
        ]);
    }

    /**
     * Show the roles management page.
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('users.roles', compact('roles', 'permissions'));
    }

    /**
     * Show form for creating a new role
     */
    public function createRole()
    {
        $permissions = Permission::all();
        return view('users.create_role', compact('permissions'));
    }

    /**
     * Store a new role
     */
    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create_role')
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('users.roles')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form to edit a specific role.
     */
    public function editRole(Role $role)
    {
        // Prevent editing super admin role if not super admin
        if ($role->name === 'super admin' && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.roles')
                ->with('error', 'Only a super admin can edit the super admin role.');
        }

        $permissions = Permission::all();
        return view('users.edit_role', compact('role', 'permissions'));
    }

    /**
     * Update the permissions for a role.
     */
    public function updateRole(Request $request, Role $role)
    {
        // Prevent updating super admin role if not super admin
        if ($role->name === 'super admin' && !Auth::user()->hasRole('super admin')) {
            return redirect()->route('users.roles')
                ->with('error', 'Only a super admin can update the super admin role.');
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit_role', $role)
                ->withErrors($validator)
                ->withInput();
        }

        // Xóa tất cả quyền hiện tại và gán quyền mới
        $role->syncPermissions($request->permissions);

        return redirect()->route('users.roles')
            ->with('success', 'Role permissions updated successfully.');
    }

    /**
     * Delete a role
     */
    public function destroyRole(Role $role)
    {
        // Prevent deleting super admin role
        if ($role->name === 'super admin') {
            return redirect()->route('users.roles')
                ->with('error', 'The super admin role cannot be deleted.');
        }

        // Check if role is assigned to users
        $usersWithRole = User::role($role->name)->count();
        if ($usersWithRole > 0) {
            return redirect()->route('users.roles')
                ->with('error', "This role is assigned to {$usersWithRole} users and cannot be deleted.");
        }

        $role->delete();

        return redirect()->route('users.roles')
            ->with('success', 'Role deleted successfully.');
    }
}