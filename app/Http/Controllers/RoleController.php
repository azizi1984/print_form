<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::get();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions'));

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required|array',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permissions'));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}