<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions or redirect to combined roles & permissions view.
     */
    public function index()
    {
        return redirect()->route('roles.index', ['tab' => 'permissions']);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        Permission::create([
            'name' => trim($request->name),
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        // Forget cached permissions so changes take effect immediately
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => trim($request->name),
            'guard_name' => $request->guard_name ?? $permission->guard_name,
        ]);

        // Forget cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        // Forget cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'Permission deleted successfully.');
    }
}
