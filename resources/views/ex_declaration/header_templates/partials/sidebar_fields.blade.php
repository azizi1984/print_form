<div class="sidebar-panel">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-tags me-1"></i> Data Fields</h5>
        <a href="{{ route('header-template.show', ['id' => $headerTemplate->header_template_id, 'refresh' => 1]) }}" 
           class="btn btn-outline-primary btn-sm py-1 px-2 rounded-pill shadow-xs" 
           style="font-size: 11px;" 
           title="ดึงข้อมูลฟิลด์สดจากฐานข้อมูลทันที (ล้าง Cache)">
            <i class="bi bi-arrow-clockwise me-1"></i> รีเฟรชฟิลด์
        </a>
    </div>
    <div class="text-muted" style="font-size: 12px; line-height: 1.4;">
        Select an active grid cell, then click any field below to bind it.
    </div>

    <!-- Field Search -->
    <div class="mt-3 position-relative">
        <input type="text" id="fieldSearchInput" class="form-control form-control-sm" placeholder="Search fields..." style="padding-left: 30px; font-size: 12px; border-radius: 6px;">
        <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
    </div>
    
    <div class="field-list">
        <!-- Special Custom Text Field -->
        <div class="field-item" data-field="custom_text" draggable="true" style="background-color: #fff3cd; border-color: #ffe69c;">
            <span>
                <i class="bi bi-fonts me-2 text-warning"></i>
                <strong>Custom Text (ข้อความทั่วไป)</strong>
            </span>
            <i class="bi bi-plus-circle text-warning"></i>
        </div>

        @foreach($fields as $field)
            <div class="field-item" data-field="{{ $field->field_name }}" draggable="true">
                <span>
                    <i class="bi bi-tag me-2"></i>
                    @if(!empty($field->app_showe) && !empty($field->app_showt))
                        {{ $field->app_showe }} ({{ $field->app_showt }})
                    @elseif(!empty($field->app_showe))
                        {{ $field->app_showe }}
                    @elseif(!empty($field->app_showt))
                        {{ $field->app_showt }}
                    @else
                        {{ $field->field_name }}
                    @endif
                </span>
                <i class="bi bi-plus-circle"></i>
            </div>
        @endforeach
    </div>

    <div class="mt-4 pt-3 border-top d-grid gap-2">
        <button class="btn btn-outline-danger btn-sm" id="btnClearCell">
            <i class="bi bi-eraser me-1"></i> Clear Selected Cell
        </button>
        <button class="btn btn-outline-secondary btn-sm" id="btnResetAll">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
        </button>
    </div>
</div>
