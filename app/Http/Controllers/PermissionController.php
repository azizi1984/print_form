<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
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
            'menu_id' => 'required|integer|min:1',
            'parent_id' => 'nullable|integer|exists:permissions,id',
            'order' => 'required|integer|min:1',
        ], [
            'name.required' => 'กรุณาระบุชื่อสิทธิ์ (Permission Name)',
            'name.unique' => 'ชื่อสิทธิ์นี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'menu_id.required' => 'กรุณาเลือกหรือระบุกลุ่มเมนู (Menu Group)',
            'menu_id.integer' => 'รหัสกลุ่มเมนูต้องเป็นตัวเลขจำนวนเต็ม',
            'order.required' => 'กรุณาระบุลำดับการแสดงผล (Order)',
            'order.integer' => 'ลำดับต้องเป็นตัวเลขจำนวนเต็ม',
            'parent_id.exists' => 'สิทธิ์แม่ (Parent Permission) ที่เลือกไม่ถูกต้อง',
        ]);

        Permission::create([
            'name' => trim($request->name),
            'guard_name' => $request->guard_name ?: 'web',
            'menu_id' => (int) $request->menu_id,
            'parent_id' => $request->filled('parent_id') ? (int) $request->parent_id : null,
            'order' => (int) ($request->order ?? 1),
        ]);

        // Forget cached permissions so changes take effect immediately
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มสิทธิ์การใช้งานใหม่เรียบร้อยแล้ว',
            ]);
        }

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'เพิ่มสิทธิ์การใช้งาน "' . trim($request->name) . '" เรียบร้อยแล้ว');
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
            'menu_id' => 'required|integer|min:1',
            'parent_id' => 'nullable|integer|exists:permissions,id',
            'order' => 'required|integer|min:1',
        ], [
            'name.required' => 'กรุณาระบุชื่อสิทธิ์ (Permission Name)',
            'name.unique' => 'ชื่อสิทธิ์นี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'menu_id.required' => 'กรุณาเลือกหรือระบุกลุ่มเมนู (Menu Group)',
            'order.required' => 'กรุณาระบุลำดับการแสดงผล (Order)',
        ]);

        // ป้องกัน Circular Reference (ไม่ให้เลือกตนเองหรือลูกหลานเป็น Parent)
        if ($request->filled('parent_id')) {
            $newParentId = (int) $request->parent_id;
            if ($newParentId === (int) $permission->id) {
                return back()->withErrors(['parent_id' => 'ไม่สามารถเลือกตนเองเป็น Parent ได้'])->withInput();
            }

            $descendantIds = $this->getDescendantIds((int) $permission->id);
            if (in_array($newParentId, $descendantIds)) {
                return back()->withErrors(['parent_id' => 'ไม่สามารถเลือกสิทธิ์ย่อยในสายของตนเองเป็น Parent ได้'])->withInput();
            }
        }

        $permission->update([
            'name' => trim($request->name),
            'guard_name' => $request->guard_name ?: $permission->guard_name,
            'menu_id' => (int) $request->menu_id,
            'parent_id' => $request->filled('parent_id') ? (int) $request->parent_id : null,
            'order' => (int) ($request->order ?? 1),
        ]);

        // Forget cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกการแก้ไขสิทธิ์เรียบร้อยแล้ว',
            ]);
        }

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'บันทึกการแก้ไขสิทธิ์ "' . $permission->name . '" เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);

        // ย้าย child permissions ให้ไปขึ้นตรงกับ parent ของสิทธิ์ที่กำลังจะลบ
        Permission::where('parent_id', $permission->id)->update([
            'parent_id' => $permission->parent_id,
        ]);

        $permissionName = $permission->name;
        $permission->delete();

        // Forget cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index', ['tab' => 'permissions'])
            ->with('success', 'ลบสิทธิ์ "' . $permissionName . '" เรียบร้อยแล้ว');
    }

    /**
     * Get all descendant IDs of a permission recursively.
     */
    protected function getDescendantIds(int $permissionId): array
    {
        $childIds = Permission::where('parent_id', $permissionId)->pluck('id')->toArray();
        $allDescendants = $childIds;

        foreach ($childIds as $cid) {
            $allDescendants = array_merge($allDescendants, $this->getDescendantIds($cid));
        }

        return $allDescendants;
    }
}
