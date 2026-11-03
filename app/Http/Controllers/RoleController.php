<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
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
        $allPermissions = Permission::with('parent')->withCount('roles')->orderBy('menu_id', 'ASC')->orderBy('order', 'ASC')->get();
        
        // แยก permissions ที่มี menu_id เพื่อสร้าง Tree ตามโครงสร้าง Role Management
        $permissionsWithMenu = $allPermissions->filter(function ($p) {
            return !empty($p->menu_id);
        });

        $unassignedPermissions = $allPermissions->filter(function ($p) {
            return empty($p->menu_id);
        });

        $permissionTree = $this->buildPermissionTreeByMenu($permissionsWithMenu);

        // รวมกลุ่มเมนูที่มีอยู่สำหรับ Dropdown ในฟอร์มเพิ่ม/แก้ไข Permission
        $existingMenus = [];
        foreach ($permissionTree as $menuId => $menuData) {
            $existingMenus[$menuId] = [
                'id' => $menuId,
                'name' => $menuData['group_name'],
                'count' => $menuData['total_count'],
            ];
        }

        $groupedPermissions = $this->groupPermissions($allPermissions);

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissions' => $allPermissions,
            'permissionTree' => $permissionTree,
            'existingMenus' => $existingMenus,
            'unassignedPermissions' => $unassignedPermissions,
            'groupedPermissions' => $groupedPermissions,
            'tab' => $tab,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        // ดึงเฉพาะข้อมูลที่มี menu_id และเรียงตาม menu_id, order เหมือนหน้า roles?tab=permissions และ roles/{id}/edit
        $permissions = Permission::whereNotNull('menu_id')
            ->where('menu_id', '!=', '')
            ->orderBy('menu_id', 'ASC')
            ->orderBy('order', 'ASC')
            ->get();

        $selectedPermissions = old('permissions', []);
        $permissionTree = $this->buildPermissionTreeByMenu($permissions, $selectedPermissions);
        $existingRoles = Role::pluck('name')->toArray();

        return view('admin.roles.create', compact('permissions', 'permissionTree', 'existingRoles', 'selectedPermissions'));
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
        ], [
            'name.required' => 'กรุณากรอกชื่อบทบาท (Role Name)',
            'name.unique' => 'ชื่อบทบาทนี้มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น',
            'name.max' => 'ชื่อบทบาทต้องมีความยาวไม่เกิน 255 ตัวอักษร',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->input('permissions', []));
        }

        // Reset permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'สร้างบทบาทใหม่เรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        // ดึงเฉพาะข้อมูลที่มี menu_id และเรียงตาม menu_id, order
        $permissions = Permission::whereNotNull('menu_id')
            ->where('menu_id', '!=', '')
            ->orderBy('menu_id', 'ASC')
            ->orderBy('order', 'ASC')
            ->get();

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissionTree = $this->buildPermissionTreeByMenu($permissions, $rolePermissions);

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'permissionTree'));
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

        // เก็บสิทธิ์เดิมของ role ที่ไม่มี menu_id ไว้ (เพื่อไม่ให้ถูกลบเมื่อ sync สิทธิ์ที่มี menu_id)
        $nonMenuPermissions = $role->permissions()
            ->where(function ($query) {
                $query->whereNull('menu_id')->orWhere('menu_id', '');
            })
            ->pluck('name')
            ->toArray();

        $submittedPermissions = $request->input('permissions', []);
        $allPermissions = array_unique(array_merge($submittedPermissions, $nonMenuPermissions));

        $role->syncPermissions($allPermissions);

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
        $allNames = $permissions->pluck('name')->toArray();
        $grouped = [];

        foreach ($permissions as $permission) {
            $groupName = $this->resolveGroupName($permission->name, $allNames);
            $grouped[$groupName][] = $permission;
        }

        ksort($grouped);
        return $grouped;
    }

    /**
     * Resolve the group/module name for a given permission name.
     */
    protected function resolveGroupName(string $name, array $allNames = []): string
    {
        if (str_contains($name, '.') || str_contains($name, ':')) {
            $parts = preg_split('/[\.\:]/', $name);
            return ucwords(str_replace(['_', '-'], ' ', trim($parts[0])));
        }

        $words = preg_split('/\s+/', trim($name));
        if (count($words) >= 2) {
            $twoWords = $words[0] . ' ' . $words[1];
            $matchesTwo = array_filter($allNames, function ($n) use ($twoWords) {
                return str_starts_with(strtolower($n), strtolower($twoWords));
            });

            if (count($matchesTwo) > 1 || in_array(strtolower($words[1]), ['invoice', 'config', 'cafe', 'setting', 'settings'])) {
                return ucfirst($twoWords);
            }

            $matchesOne = array_filter($allNames, function ($n) use ($words) {
                return str_starts_with(strtolower($n), strtolower($words[0]));
            });

            if (count($matchesOne) > 1) {
                return ucfirst($words[0]);
            }

            return ucfirst($name);
        }

        return ucfirst($name);
    }

    /**
     * Build hierarchical tree data for permissions grouped by menu_id.
     * Condition:
     * 1. Group การแสดงตาม menu_id เดียวกัน
     * 2. จัดกลุ่มว่าในแต่ละ permission อยู่ใต้ parent_id (permission id) อะไร
     * 3. เรียงตาม order ที่กำหนดที่ field order
     */
    protected function buildPermissionTreeByMenu($permissions, array $rolePermissions = []): array
    {
        // 1. จัด Group การแสดงตาม menu_id เดียวกัน
        $groupedByMenu = [];
        foreach ($permissions as $permission) {
            $groupedByMenu[$permission->menu_id][] = $permission;
        }

        $tree = [];
        foreach ($groupedByMenu as $menuId => $perms) {
            $totalCount = count($perms);
            $checkedCount = 0;
            foreach ($perms as $p) {
                if (in_array($p->name, $rolePermissions)) {
                    $checkedCount++;
                }
            }

            // กำหนดชื่อ Group Title จาก permission ที่เป็น Root (parent_id ว่าง) หรือ menuId
            $groupTitle = $this->resolveMenuGroupTitle($perms, $menuId);
            $slug = 'menu-' . \Illuminate\Support\Str::slug($menuId . '-' . $groupTitle);

            // สร้างโครงสร้าง Tree ตาม parent_id และเรียงตาม order (คำนวณ depth อัตโนมัติโดยไม่ต้องใช้ field level)
            $nodes = $this->buildTreeNodesWithParentId($perms, $rolePermissions);

            $tree[$menuId] = [
                'menu_id' => $menuId,
                'group_name' => $groupTitle,
                'slug' => $slug,
                'total_count' => $totalCount,
                'checked_count' => $checkedCount,
                'nodes' => $nodes,
            ];
        }

        return $tree;
    }

    /**
     * Resolve group title from root permission (parent_id is null/empty) or menuId.
     */
    protected function resolveMenuGroupTitle(array $perms, $menuId): string
    {
        foreach ($perms as $p) {
            if (empty($p->parent_id)) {
                $name = $this->formatPascalCase($p->name);
                $trimmed = preg_replace('/\s+Menu$/i', '', $name);
                return !empty($trimmed) ? $trimmed : $name;
            }
        }

        if (!empty($perms)) {
            $name = $this->formatPascalCase($perms[0]->name);
            $trimmed = preg_replace('/\s+Menu$/i', '', $name);
            return !empty($trimmed) ? $trimmed : "Menu $menuId";
        }

        return "Menu $menuId";
    }

    /**
     * Build nested tree nodes using parent_id hierarchy and sorted by order.
     * Computes level (depth) dynamically without relying on a database level field.
     */
    protected function buildTreeNodesWithParentId(array $perms, array $rolePermissions): array
    {
        $itemsById = [];
        $childrenByParent = [];

        foreach ($perms as $p) {
            $itemsById[$p->id] = [
                'permission' => $p,
                'id' => $p->id,
                'name' => $p->name,
                'parent_id' => $p->parent_id ?? null,
                'order' => (int) ($p->order ?? 1),
                'title' => $this->formatPascalCase($p->name),
                'is_checked' => in_array($p->name, $rolePermissions),
                'children' => [],
            ];

            $pid = !empty($p->parent_id) ? $p->parent_id : 0;
            $childrenByParent[$pid][] = $p->id;
        }

        // เรียงลำดับโหนดย่อยตาม order
        foreach ($childrenByParent as $pid => &$childIds) {
            usort($childIds, function ($a, $b) use ($itemsById) {
                $orderA = $itemsById[$a]['order'];
                $orderB = $itemsById[$b]['order'];
                if ($orderA !== $orderB) {
                    return $orderA <=> $orderB;
                }
                return $a <=> $b;
            });
        }
        unset($childIds);

        // ประกอบโครงสร้าง Tree แบบ Recursive ตาม parent_id พร้อมคำนวณ depth อัตโนมัติ
        $assemble = function ($id, $depth = 1) use (&$assemble, &$itemsById, &$childrenByParent) {
            $node = $itemsById[$id];
            $node['level'] = $depth; // คำนวณความลึกของ Tree อัตโนมัติ (ไม่ต้องพึ่งพาฟิลด์ level ในฐานข้อมูล)
            if (isset($childrenByParent[$id])) {
                foreach ($childrenByParent[$id] as $childId) {
                    $node['children'][] = $assemble($childId, $depth + 1);
                }
            }
            return $node;
        };

        // หา Root nodes (parent_id เป็น null / 0 หรือไม่มี parent_id อยู่ในกลุ่มนี้)
        $rootIds = $childrenByParent[0] ?? [];
        if (empty($rootIds)) {
            foreach ($itemsById as $id => $item) {
                if (empty($item['parent_id']) || !isset($itemsById[$item['parent_id']])) {
                    $rootIds[] = $id;
                }
            }
        }

        // เรียง root nodes ตาม order
        usort($rootIds, function ($a, $b) use ($itemsById) {
            $orderA = $itemsById[$a]['order'];
            $orderB = $itemsById[$b]['order'];
            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }
            return $a <=> $b;
        });

        $rootNodes = [];
        foreach ($rootIds as $rid) {
            $rootNodes[] = $assemble($rid, 1);
        }

        return $rootNodes;
    }

    /**
     * Format camelCase or PascalCase string to readable words.
     */
    protected function formatPascalCase(string $name): string
    {
        $spaced = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $name);
        $spaced = preg_replace('/(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $spaced);
        $spaced = str_replace(['_', '-', '.'], ' ', $spaced);
        return trim(preg_replace('/\s+/', ' ', $spaced));
    }
}