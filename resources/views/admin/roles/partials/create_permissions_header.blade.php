{{-- Header Banner & Action Controls for Assign Permissions --}}
<div class="role-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="d-flex align-items-center">
            <i class="bi bi-key-fill text-warning me-2" style="font-size: 20px;"></i>
            <h5 class="role-title-text mb-0">
                จัดสรรสิทธิ์การใช้งาน (Assign Permissions)
            </h5>
        </div>
        <p class="role-subtitle-text mb-0 mt-0.5">
            เลือกสิทธิ์ที่ต้องการมอบให้กับบทบาทใหม่ตามโครงสร้างกลุ่มเมนู (Menu-based Permission Catalog)
        </p>
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
