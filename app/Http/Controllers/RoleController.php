<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    /**
     * Display a listing of roles and permissions.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'roles');
        
        $roles = Role::with('permissions')->withCount('users')->orderBy('id', 'DESC')->get();
        $permissions = Permission::withCount('roles')->orderBy('name', 'ASC')->get();
        
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.roles.index', compact('roles', 'permissions', 'groupedPermissions', 'tab'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name', 'ASC')->get();
        $groupedPermissions = $this->groupPermissions($permissions);

        return view('admin.roles.create', compact('permissions', 'groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('name', 'ASC')->get();
        $groupedPermissions = $this->groupPermissions($permissions);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->name = trim($request->name);
        $role->save();

        $role->syncPermissions($request->input('permissions', []));

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Group permissions by prefix / module name.
     */
    protected function groupPermissions($permissions)
    {
        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = preg_split('/[\.\:]/', $permission->name);
            $groupName = count($parts) > 1 ? ucwords(str_replace('_', ' ', $parts[0])) : 'General System';
            $grouped[$groupName][] = $permission;
        }
        ksort($grouped);
        return $grouped;
    }
}