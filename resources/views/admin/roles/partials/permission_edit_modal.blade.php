<!-- Modal: Edit Dynamic Permission -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Modal Header --}}
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-25 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-pencil-square fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editPermissionModalLabel">แก้ไขสิทธิ์การใช้งาน (Edit Permission)</h5>
                        <small class="text-white-50">ปรับปรุงข้อมูลสิทธิ์ กลุ่มเมนู หรือตำแหน่งความสัมพันธ์ในโครงสร้าง Role</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editPermissionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light-subtle">
                    <div class="row g-4">
                        {{-- Left Column: Form Controls --}}
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="bi bi-diagram-3-fill text-primary me-2"></i>1. ตำแหน่งในโครงสร้างเมนู (Menu & Hierarchy Placement)
                                </h6>

                                {{-- Menu Group Selection --}}
                                <div class="mb-3">
                                    <label for="edit_perm_menu_id" class="form-label fw-semibold small text-secondary">
                                        กลุ่มเมนูหลัก (Menu Group) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-folder2-open"></i>
                                        </span>
                                        <select class="form-select border-start-0" id="edit_perm_menu_id" name="menu_id" required>
                                            <option value="" disabled>-- เลือกกลุ่มเมนู --</option>
                                            @foreach($existingMenus ?? [] as $mId => $mInfo)
                                                <option value="{{ $mId }}" data-menu-name="{{ $mInfo['name'] }}">
                                                    กลุ่มที่ {{ $mId }}: {{ $mInfo['name'] }} ({{ $mInfo['count'] }} สิทธิ์)
                                                </option>
                                            @endforeach
                                            <option value="NEW_GROUP" class="text-primary fw-semibold">+ สร้างกลุ่มเมนูใหม่ (New Menu Group ID)...</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Input for New Menu ID (Hidden by default) --}}
                                <div class="mb-3 d-none" id="editNewMenuGroupInputWrapper">
                                    <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle">
                                        <label for="edit_custom_menu_id" class="form-label fw-semibold small text-primary mb-1">
                                            <i class="bi bi-plus-circle me-1"></i>กำหนดรหัสกลุ่มเมนูใหม่ (New Menu Group ID) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control form-control-sm" id="edit_custom_menu_id" placeholder="เช่น 3, 4, 5" min="1">
                                    </div>
                                </div>

                                {{-- Parent Permission Selection --}}
                                <div class="mb-3">
                                    <label for="edit_perm_parent_id" class="form-label fw-semibold small text-secondary">
                                        สิทธิ์แม่ / เมนูหลัก (Parent Permission)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-diagram-2"></i>
                                        </span>
                                        <select class="form-select border-start-0" id="edit_perm_parent_id" name="parent_id">
                                            <option value="">(ไม่มี - เป็นสิทธิ์ระดับบนสุด / Root Menu ของกลุ่มนี้)</option>
                                            {{-- Dynamic Options Populated by JS based on menu_id --}}
                                        </select>
                                    </div>
                                </div>

                                {{-- Display Order --}}
                                <div class="mb-2">
                                    <label for="edit_perm_order" class="form-label fw-semibold small text-secondary">
                                        ลำดับการแสดงผล (Display Order) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group" style="max-width: 200px;">
                                        <span class="input-group-text bg-light text-muted border-end-0">
                                            <i class="bi bi-sort-numeric-down"></i>
                                        </span>
                                        <input type="number" class="form-control border-start-0" id="edit_perm_order" name="order" value="1" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-xs rounded-3 p-3 bg-white">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="bi bi-card-text text-primary me-2"></i>2. ข้อมูลสิทธิ์ (Permission Details)
                                </h6>

                                {{-- Permission Name --}}
                                <div class="mb-3">
                                    <label for="edit_perm_name" class="form-label fw-semibold small text-secondary">
                                        ชื่อสิทธิ์ภาษาอังกฤษ (Permission Technical Name) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control font-monospace" id="edit_perm_name" name="name" required autocomplete="off">
                                    
                                    {{-- Quick Action Prefixes --}}
                                    <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
                                        <span class="text-muted small me-1" style="font-size: 11px;">ตัวช่วยเติม Action:</span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill edit-quick-prefix-btn" data-prefix="Create">Create</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill edit-quick-prefix-btn" data-prefix="Edit">Edit</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill edit-quick-prefix-btn" data-prefix="Delete">Delete</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill edit-quick-prefix-btn" data-prefix="View">View</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill edit-quick-prefix-btn" data-prefix="Manage">Manage</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill quick-prefix-btn" data-prefix="Export">Export</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill quick-prefix-btn" data-prefix="Print">Print</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill quick-prefix-btn" data-prefix="Copy">Copy</button>
                                    </div>
                                </div>

                                {{-- Guard Name --}}
                                <div class="mb-1">
                                    <label for="edit_perm_guard" class="form-label fw-semibold small text-secondary">Guard Name</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_perm_guard" name="guard_name" value="web">
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Interactive Live Role Preview --}}
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-xs rounded-3 p-3 bg-white h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="bi bi-eye-fill text-success me-2"></i>ตัวอย่างในหน้าจัดการ Role
                                    </h6>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2" style="font-size: 10.5px;">
                                        Live Role Preview
                                    </span>
                                </div>

                                <p class="text-muted small mb-3" style="font-size: 12px;">
                                    จำลองรูปแบบที่สิทธิ์นี้จะแสดงในหน้า <strong>กำหนดสิทธิ์บทบาท (Assign Role Permissions)</strong>:
                                </p>

                                {{-- Simulated Role Group Accordion --}}
                                <div class="simulated-role-box border rounded-3 p-3 bg-light flex-grow-1">
                                    <div class="simulated-accordion-header p-2 bg-white rounded border d-flex justify-content-between align-items-center mb-3 shadow-2xs">
                                        <div class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-folder2 text-primary me-2 fs-5"></i>
                                            <strong class="text-dark small text-truncate" id="editPreviewGroupName">
                                                Ex Declaration
                                            </strong>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 11px;">
                                            Menu #<span id="editPreviewMenuIdBadge">1</span>
                                        </span>
                                    </div>

                                    {{-- Simulated Tree Container --}}
                                    <div class="simulated-tree-container p-2 bg-white rounded border">
                                        {{-- Simulated Parent Node (If any) --}}
                                        <div id="editPreviewParentWrapper" class="d-none mb-2 pb-2 border-bottom">
                                            <div class="d-flex align-items-center text-secondary small">
                                                <i class="bi bi-diagram-2 me-1 text-muted"></i>
                                                <span class="text-muted me-1">อยู่ใต้:</span>
                                                <strong class="text-dark" id="editPreviewParentName">Profile Template Menu</strong>
                                            </div>
                                        </div>

                                        {{-- Target Node Preview with Switch --}}
                                        <div class="preview-target-node p-2 rounded border bg-light-subtle d-flex align-items-center justify-content-between" id="editPreviewNodeCard">
                                            <div class="d-flex align-items-center gap-2 text-truncate" id="editPreviewIndentWrapper">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" checked disabled style="cursor: default;">
                                                </div>
                                                <div class="text-truncate">
                                                    <div class="fw-bold text-dark small text-truncate" id="editPreviewFriendlyTitle">
                                                        ชื่อสิทธิ์
                                                    </div>
                                                    <code class="text-muted small" style="font-size: 11px;" id="editPreviewTechnicalCode">
                                                        (PermissionTechnicalName)
                                                    </code>
                                                </div>
                                            </div>
                                            <div class="text-end ps-2">
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 10px;" id="editPreviewOrderBadge">
                                                    Order #1
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-2 px-4 border-top">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-xs" id="btnSubmitEditPermission">
                        <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
