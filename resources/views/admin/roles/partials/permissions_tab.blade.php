{{-- Top Statistics & Action Toolbar --}}
<div class="p-3 bg-white border-bottom">
    <div class="row g-3 align-items-center justify-content-between">
        <div class="col-md-7">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <span class="avatar-sm rounded-circle bg-primary-subtle text-primary p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </span>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14px;">โครงสร้างสิทธิ์ตามกลุ่มเมนู (Menu-based Permission Catalog)</div>
                        <small class="text-muted" style="font-size: 12px;">
                            {{ count($permissionTree ?? []) }} กลุ่มเมนู | {{ $permissions->count() }} สิทธิ์ทั้งหมด
                        </small>
                    </div>
                </div>

                @if(isset($unassignedPermissions) && $unassignedPermissions->count() > 0)
                    <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 d-flex align-items-center" style="font-size: 12px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        {{ $unassignedPermissions->count() }} สิทธิ์ยังไม่ได้ผูกกลุ่มเมนู
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-5">
            <div class="d-flex justify-content-md-end align-items-center gap-2">
                {{-- Expand / Collapse All Button --}}
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-xs" id="btnToggleAllMenuGroups">
                    <i class="bi bi-arrows-expand me-1" id="iconToggleAll"></i> <span id="textToggleAll">ขยายทั้งหมด</span>
                </button>

                {{-- Search Box --}}
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="filterPermissionInput" placeholder="ค้นหาชื่อสิทธิ์...">
                </div>

                {{-- Add Permission Button --}}
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#createPermissionModal" id="btnOpenCreatePermModal">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่ม Permission
                </button>
            </div>
        </div>
    </div>
</div>

<div class="p-4 bg-light-subtle">

    {{-- Alert: Unassigned Permissions (Permissions without menu_id) --}}
    @if(isset($unassignedPermissions) && $unassignedPermissions->count() > 0)
        <div class="alert alert-warning border-0 shadow-xs rounded-3 mb-4 p-3" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-circle-fill text-warning fs-4 me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-1">พบสิทธิ์ที่ยังไม่ได้ระบุกลุ่มเมนู (Unassigned Permissions)</h6>
                    <p class="mb-2 small">
                        สิทธิ์รายการต่อไปนี้ไม่มีค่า <code>menu_id</code> ทำให้ <strong>จะไม่ปรากฏในหน้าจัดการ Role (Assign Role Permissions)</strong> กรุณากดแก้ไขเพื่อระบุกลุ่มเมนูและระดับชั้นสิทธิ์:
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($unassignedPermissions as $uPerm)
                            <div class="badge bg-white text-dark border p-2 rounded d-flex align-items-center gap-2 shadow-2xs">
                                <code class="fw-bold">{{ $uPerm->name }}</code>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill edit-perm-btn"
                                        data-id="{{ $uPerm->id }}"
                                        data-name="{{ $uPerm->name }}"
                                        data-guard="{{ $uPerm->guard_name }}"
                                        data-menu-id=""
                                        data-parent-id=""
                                        data-order="{{ $uPerm->order ?? 1 }}"
                                        title="กำหนดกลุ่มเมนูให้สิทธิ์นี้">
                                    <i class="bi bi-pencil me-1"></i>กำหนดกลุ่มเมนู
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Menu Groups --}}
    @if(isset($permissionTree) && count($permissionTree) > 0)
        <div class="row g-4" id="permissionGroupsContainer">
            @foreach($permissionTree as $menuId => $group)
                <div class="col-12 perm-group-card-wrapper" data-menu-id="{{ $menuId }}" data-menu-name="{{ strtolower($group['group_name']) }}">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        {{-- Group Header --}}
                        <div class="card-header bg-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <span class="rounded-circle bg-primary-subtle text-primary p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-folder2-open fs-5"></i>
                                </span>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $group['group_name'] }}</h6>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 11px;">
                                            Menu #{{ $menuId }}
                                        </span>
                                    </div>
                                    <small class="text-muted" style="font-size: 12px;">
                                        มีสิทธิ์ในกลุ่มนี้ทั้งหมด <span class="fw-bold text-dark">{{ $group['total_count'] }}</span> รายการ
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                {{-- Quick Add to this menu group --}}
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 quick-add-to-group-btn" 
                                        data-menu-id="{{ $menuId }}" 
                                        data-menu-name="{{ $group['group_name'] }}"
                                        title="เพิ่มสิทธิ์ใหม่ในกลุ่มเมนูนี้">
                                    <i class="bi bi-plus-circle me-1"></i> เพิ่มสิทธิ์ในกลุ่มนี้
                                </button>

                                {{-- Toggle collapse --}}
                                <button class="btn btn-light btn-sm rounded-circle p-1 text-muted collapsed collapse-toggle-btn" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseMenu_{{ $menuId }}" 
                                        aria-expanded="false" 
                                        aria-controls="collapseMenu_{{ $menuId }}">
                                    <i class="bi bi-chevron-down transition-transform"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Group Body: Tree Table (เริ่มต้นพับเก็บไว้ ไม่ขยายออกมา) --}}
                        <div class="collapse" id="collapseMenu_{{ $menuId }}">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light-subtle text-secondary small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th class="ps-4 py-2" style="width: 45%;">สิทธิ์การใช้งาน (Permission Title & Code)</th>
                                            <th class="text-center py-2" style="width: 15%;">ตำแหน่ง / ระดับ</th>
                                            <th class="text-center py-2" style="width: 10%;">ลำดับ (Order)</th>
                                            <th class="text-center py-2" style="width: 15%;">บทบาทที่ใช้งาน</th>
                                            <th class="text-end pe-4 py-2" style="width: 15%;">การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        @foreach($group['nodes'] as $rootNode)
                                            @include('admin.roles.partials.permission_table_row', [
                                                'node' => $rootNode,
                                                'menuId' => $menuId,
                                                'menuName' => $group['group_name']
                                            ])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-3 py-5 text-center bg-white">
            <div class="py-4">
                <i class="bi bi-shield-lock text-secondary d-block mb-3" style="font-size: 3rem;"></i>
                <h6 class="fw-bold text-dark">ยังไม่มีข้อมูล Permission ในระบบ</h6>
                <p class="text-muted small mb-4">เริ่มต้นสร้างสิทธิ์การใช้งานเพื่อนำไปจัดสรรในบทบาทต่างๆ</p>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                    <i class="bi bi-plus-circle me-1"></i> เพิ่ม Permission แรก
                </button>
            </div>
        </div>
    @endif

</div>

<style>
    .collapse-toggle-btn .bi-chevron-down {
        transition: transform 0.2s ease-in-out;
        display: inline-block;
    }
    .collapse-toggle-btn.collapsed .bi-chevron-down {
        transform: rotate(-90deg);
    }
</style>
