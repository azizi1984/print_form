{{-- Top Header Row / Breadcrumbs --}}
<div class="d-flex justify-content-between align-items-center role-page-header">
    <div class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.3px;">
        กำหนดสิทธิ์: {{ strtoupper($role->name) }}
    </div>
    <div class="role-breadcrumb">
        <a href="{{ route('roles.index') }}">Role And Permission</a>
        <span class="mx-1.5 text-muted">&gt;</span>
        <span class="text-secondary">กำหนดสิทธิ์: {{ ucfirst($role->name) }}</span>
    </div>
</div>

{{-- Header Banner & Action Controls --}}
<div class="role-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-check role-title-icon me-2"></i>
            <h5 class="role-title-text mb-0">
                จัดการสิทธิ์การใช้งานสำหรับบทบาท: 
                <span class="text-primary fw-bold" id="roleNameDisplay">{{ $role->name }}</span>
                <button type="button" class="btn btn-link btn-sm text-muted p-0 ms-1.5" id="btnEditRoleName" title="แก้ไขชื่อ Role" style="font-size: 13px;">
                    <i class="bi bi-pencil"></i>
                </button>
            </h5>
        </div>
        <p class="role-subtitle-text mb-0 mt-0.5">
            เลือกสิทธิ์ที่ต้องการเปิดใช้งานสำหรับบทบาทนี้ตามโครงสร้างเมนู
        </p>

        {{-- Hidden / Inline Rename Box --}}
        <div id="inlineRenameBox" class="d-none mt-2 d-flex align-items-center gap-2">
            <input type="text" id="roleNameInlineInput" class="form-control form-control-sm" style="max-width: 250px;" value="{{ old('name', $role->name) }}" placeholder="ระบุชื่อ Role">
            <button type="button" class="btn btn-sm btn-primary py-0 px-2" id="btnSaveRoleName" style="height: 28px;">ตกลง</button>
            <button type="button" class="btn btn-sm btn-light py-0 px-2" id="btnCancelRoleName" style="height: 28px;">ยกเลิก</button>
        </div>
    </div>

    {{-- Right Action Toolbar --}}
    <div class="role-toolbar">
        {{-- Search Input --}}
        <div class="search-input-box">
            <i class="bi bi-search search-icon"></i>
            <input type="text" 
                   id="permissionSearch" 
                   class="form-control shadow-none" 
                   placeholder="ค้นหาสิทธิ์การใช้งาน..." 
                   autocomplete="off">
        </div>

        {{-- Select All --}}
        <button type="button" class="btn-toolbar-select-all" id="btnSelectAll" title="เลือกสิทธิ์ทั้งหมด">
            <i class="bi bi-check2-all" style="font-size: 14px;"></i>
            <span>เลือกทั้งหมด</span>
        </button>

        {{-- Deselect All --}}
        <button type="button" class="btn-toolbar-deselect-all" id="btnDeselectAll" title="ยกเลิกสิทธิ์ทั้งหมด">
            <i class="bi bi-slash-circle" style="font-size: 13px;"></i>
            <span>ยกเลิกทั้งหมด</span>
        </button>

        {{-- Expand All (+) --}}
        <button type="button" class="btn-toolbar-icon" id="btnExpandAll" title="ขยายทั้งหมด">
            <i class="bi bi-plus-circle"></i>
        </button>

        {{-- Collapse All (-) --}}
        <button type="button" class="btn-toolbar-icon" id="btnCollapseAll" title="ย่อทั้งหมด">
            <i class="bi bi-dash-circle"></i>
        </button>
    </div>
</div>
