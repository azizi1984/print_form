@extends('layouts.app')

@section('page-title', 'Layout Designer: ' . $detailTemplate->detail_template_name . ' - ' . config('app.name', 'Print Form'))

@push('styles')
<style>
    .design-workspace {
        display: flex;
        gap: 24px;
        margin-top: 15px;
    }

    .designer-container {
        flex: 1;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .design-sheet-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #design-table-layout {
        display: grid;
        grid-template-columns: 32px calc(34% - 20px) calc(18% - 12px) 14% 19% 15%;
        grid-template-rows: repeat(4, 42px) 35px;
        background: #ffffff;
        border: 2px solid #000000 !important;
        border-radius: 4px;
        overflow: hidden;
        width: 100%;
        min-width: 900px;
        transition: all 0.2s ease;
    }

    .grid-cell {
        box-sizing: border-box;
        border-right: 1px solid #000000 !important;
        border-bottom: 1px solid #000000 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #000000;
        cursor: pointer;
        transition: all 0.15s;
        user-select: none;
        position: relative;
        background-color: #ffffff;
        padding: 0 6px;
        text-align: center;
    }

    .grid-cell:hover {
        background-color: rgba(85, 110, 230, 0.08);
    }

    .grid-cell.active {
        background-color: rgba(85, 110, 230, 0.15);
        box-shadow: inset 0 0 0 2px #556ee6;
        font-weight: bold;
    }

    .cell-last {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .cell-filled {
        background-color: rgba(52, 195, 143, 0.08);
        color: #000000;
        box-shadow: inset 0 0 0 1.5px #34c38f;
    }

    .cell-info-popover {
        position: absolute;
        font-size: 9px;
        top: 2px;
        left: 6px;
        color: #495057;
        white-space: pre-wrap;
        line-height: 1.1;
        pointer-events: none;
    }

    .target-plus {
        position: absolute;
        cursor: pointer;
        z-index: 10;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #198754;
        color: #ffffff;
        border: 1px solid #146c43;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        transition: all 0.2s ease-in-out;
    }

    .target-plus::before {
        content: "+";
        font-size: 13px;
        font-weight: bold;
        line-height: 1;
        margin-top: -1px;
    }

    .target-plus:hover {
        background-color: #157347;
        transform: scale(1.2);
        box-shadow: 0 4px 8px rgba(0,0,0,0.25);
    }

    .target-plus.pos-bottom-left {
        top: calc(50% - 8px);
        left: 8px;
    }

    .target-plus.pos-bottom-right {
        top: calc(50% - 8px);
        right: 8px;
    }

    .target-plus.pos-top-right {
        top: calc(50% - 8px);
        right: 8px;
    }

    .target-plus.pos-inline {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-left: 8px;
        top: -1px;
    }

    .target-plus.pos-center {
        left: calc(50% - 8px);
        top: calc(50% - 8px);
    }

    /* Sidebar panel styling */
    .sidebar-panel {
        width: 320px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }

    .field-list {
        margin-top: 10px;
        flex: 1;
        overflow-y: auto;
        max-height: 520px;
    }

    .field-item {
        padding: 6px 10px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        margin-bottom: 5px;
        cursor: pointer;
        font-size: 11.5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
    }

    .field-item:hover {
        background: #556ee6;
        color: white;
        border-color: #556ee6;
        transform: translateY(-1px);
    }

    .field-item i {
        font-size: 14px;
    }

    .dimension-info-badge {
        background: #e1e6fc;
        color: #556ee6;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .position-center {
        left: 0; right: 0; text-align: center;
    }

    .tp-badge {
        background-color: #34c38f;
        border: 1px solid #2ca579;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 10.5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #ffffff;
        font-weight: bold;
        white-space: nowrap;
        vertical-align: middle;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .tp-badge-label {
        color: #ffffff;
        font-weight: bold;
    }

    .tp-badge-delete {
        color: #ff3d60;
        cursor: pointer;
        font-size: 12px;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        transition: color 0.15s;
    }

    .tp-badge-delete:hover {
        color: #ff1f48;
    }

    .grid-cell.drag-over {
        background-color: rgba(85, 110, 230, 0.15) !important;
        border: 2px dashed #556ee6 !important;
    }

    .target-plus.drag-over {
        transform: scale(1.3);
        background-color: #157347 !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
    }

    /* Preview mode styles */
    .preview-mode .target-plus {
        display: none !important;
    }
    .preview-mode .cell-info-popover {
        opacity: 0.35;
    }
    .preview-mode .grid-cell {
        cursor: default;
    }
    .preview-mode .grid-cell:hover {
        background-color: #ffffff !important;
    }
    .preview-mode .cell-filled {
        background-color: #ffffff !important;
        box-shadow: none !important;
    }
    .preview-value {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 12px;
        color: #0b1a30;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        max-width: 100%;
        letter-spacing: 0.3px;
    }

    .mode-btn-active {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
        border-color: #4f46e5 !important;
    }
</style>
@endpush

@section('content')
<div class="detail-template-manager">
    <!-- Header Controls -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('detail-template') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-xs" id="btnBack">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0">Layout Designer: {{ $detailTemplate->detail_template_name }}</h5>
                        <span id="dirtyIndicator" class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 d-none" style="font-size: 11px;">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก
                        </span>
                    </div>
                    <small class="text-muted">กำหนดตำแหน่งและฟิลด์ข้อมูลบนแบบฟอร์มตารางรายการสินค้า (Detail Items)</small>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Mode Switcher -->
                <div class="btn-group btn-group-sm rounded-pill p-1 bg-light border shadow-xs" role="group">
                    <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold mode-btn-active" id="btnModeDesign">
                        <i class="bi bi-pencil-square me-1"></i> Design Mode
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold text-secondary" id="btnModePreview">
                        <i class="bi bi-eye me-1"></i> Preview Data
                    </button>
                </div>

                <div class="dimension-info-badge shadow-xs">
                    <i class="bi bi-aspect-ratio"></i> Detail Table: 19.0 CM
                </div>

                <button type="button" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm" id="btnSaveLayout">
                    <i class="bi bi-save me-1"></i> Save Layout
                </button>
            </div>
        </div>
    </div>

        <div class="design-workspace">
            <!-- Left Design Grid -->
            <div class="designer-container">
                <div class="text-muted mb-3 text-center" style="font-size: 13px;">
                    <i class="bi bi-info-circle me-1"></i> Click a cell in the template sheet below, then choose a field from the right sidebar to place it.
                </div>

                <div class="design-sheet-wrapper" style="border: 1px solid #000000ff;">
                    <!-- Full-Width HTML Table Grid (No For Loop) -->
                    <div id="design-table-layout">
                        <!-- Row 1 -->
                        <div class="grid-cell" data-col="left" data-row="1" id="HL-margin-top" style="grid-column: 1; grid-row: 1 / span 4; border-right: 1px solid #000000 !important;">
                            <span class="cell-info-popover position-center" style="font-size: 9.5px; font-weight: 500; line-height: 1.25; text-align: center; top: 4px; left: 0; right: 0;">ราย<br>การ<br>ที่</span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="1" id="HL-1" style="grid-column: 2; grid-row: 1; border-right: 1px solid #000000 !important;">
                            <span class="cell-info-popover position-center">เครื่องหมายและหมายเลขหีบห่อ</span>
                            <span class="target-plus pos-bottom-left" id="tp-1-left-c2-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="1" id="HL-2" style="grid-column: 3; grid-row: 1; border-right: 2px solid #000000 !important;">
                            <span class="cell-info-popover position-center">จำนวนและลักษณะหีบห่อ</span>
                            <span class="target-plus pos-bottom-left" id="tp-1-left-c3-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="1" id="HR-1" style="grid-column: 4; grid-row: 1;">
                            <span class="cell-info-popover position-center">น้ำหนักสุทธิ</span>
                            <span class="target-plus pos-bottom-left" id="tp-1-right-c4-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="1" id="HR-2" style="grid-column: 5; grid-row: 1 / span 2;">
                            <span class="cell-info-popover position-center">ราคาของ FOB<br>(เงินต่างประเทศ)</span>
                            <span class="target-plus pos-bottom-left" id="tp-1-right-c5-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="1" id="HR-3" style="grid-column: 6; grid-row: 1 / span 2; border-right: none !important;">
                            <span class="cell-info-popover position-center">ใช้สิทธิพิเศษ</span>
                            <span class="target-plus pos-bottom-left" id="tp-1-right-c6-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid-cell" data-col="left" data-row="2" id="HL-3" style="grid-column: 2 / span 2; grid-row: 2 / span 3; border-right: 2px solid #000000 !important;">
                            <span class="cell-info-popover position-center">ชนิดของ</span>
                            <span class="target-plus pos-bottom-left" id="tp-2-left-c2-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="2" id="HR-4" style="grid-column: 4; grid-row: 2;">
                            <span class="cell-info-popover position-center">ปริมาณ</span>
                            <span class="target-plus pos-bottom-left" id="tp-2-right-c4-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 3 -->
                        <div class="grid-cell" data-col="right" data-row="3" id="HR-5" style="grid-column: 4; grid-row: 3;">
                            <span class="cell-info-popover position-center">ประเภทพิกัด</span>
                            <span class="target-plus pos-bottom-left" id="tp-3-right-c4-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="3" id="HR-6" style="grid-column: 5; grid-row: 3;">
                            <span class="cell-info-popover position-center">ราคาของ FOB (บาท)</span>
                            <span class="target-plus pos-bottom-left" id="tp-3-right-c5-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="3" id="HR-7" style="grid-column: 6; grid-row: 3; border-right: none !important;">
                            <span class="cell-info-popover position-center">อัตราอากร</span>
                            <span class="target-plus pos-bottom-left" id="tp-3-right-c6-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 4 -->
                        <div class="grid-cell" data-col="right" data-row="4" id="HR-8" style="grid-column: 4; grid-row: 4;">
                            <span class="cell-info-popover position-center">รหัสสถิติหน่วย</span>
                            <span class="target-plus pos-bottom-left" id="tp-4-right-c4-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="4" id="HR-9" style="grid-column: 5; grid-row: 4;">
                            <span class="cell-info-popover position-center">ราคาประเมินอากร</span>
                            <span class="target-plus pos-bottom-left" id="tp-4-right-c5-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="4" id="HR-10" style="grid-column: 6; grid-row: 4; border-right: none !important;">
                            <span class="cell-info-popover position-center">อากรขาออก</span>
                            <span class="target-plus pos-bottom-left" id="tp-4-right-c6-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 5 -->
                        <div class="grid-cell" data-col="left" data-row="5" id="HL-margin-bottom" style="grid-column: 1; grid-row: 5; border-top: 1px solid #c2b59b !important; border-right: 1px solid #000000 !important; border-bottom: none !important;">
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="5" id="HL-bottom" style="grid-column: 2 / span 2; grid-row: 5; border-top: 1px solid #c2b59b !important; border-right: 2px solid #000000 !important; border-bottom: none !important; justify-content: flex-start; padding-left: 6px;">
                            <span style="font-size: 11px; color: #2b2b2b; font-weight: 500; white-space: nowrap; user-select: none;" class="me-2">เจ้าหน้าที่ ───►</span>
                            <span class="target-plus pos-inline" id="tp-5-left-c2-inline"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="5" id="HR-11" style="grid-column: 4 / span 3; grid-row: 5; border-top: 1px solid #c2b59b !important; border-right: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left" id="tp-5-right-c4-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Control Sidebar -->
            <div class="sidebar-panel">
                <h5 class="fw-bold mb-3"><i class="bi bi-tags me-1"></i> Data Fields</h5>
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
        </div>
    </div>

    <!-- Bootstrap Modal for Data Fields -->
    <div class="modal fade" id="fieldSelectorModal" tabindex="-1" aria-labelledby="fieldSelectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="fieldSelectorModalLabel"><i class="bi bi-tags me-1 text-primary"></i> Select Data Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="text-muted mb-3" style="font-size: 13px;">
                        Select a field to bind to this position:
                    </div>
                    <!-- Modal Search Box -->
                    <div class="mb-3 position-relative">
                        <input type="text" id="modalFieldSearchInput" class="form-control form-control-sm" placeholder="Search fields..." style="padding-left: 30px; font-size: 13px; border-radius: 6px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
                    </div>
                    
                    <!-- Modal Fields List -->
                    <div class="modal-field-list" style="max-height: 380px; overflow-y: auto;">
                        <!-- Special Custom Text Field -->
                        <div class="modal-field-item p-2 mb-2 border rounded d-flex justify-content-between align-items-center" 
                             data-field="custom_text" 
                             data-label="Custom Text (ข้อความทั่วไป)"
                             style="cursor: pointer; font-size: 12.5px; transition: all 0.2s; background-color: #fff3cd; border-color: #ffe69c;">
                            <span class="field-label-text">
                                <i class="bi bi-fonts me-2 text-warning"></i>
                                <strong>Custom Text (ข้อความทั่วไป)</strong>
                            </span>
                            <button type="button" class="btn btn-warning btn-sm px-2 py-1 select-field-btn" style="font-size: 11px; border-radius: 4px;">
                                <i class="bi bi-plus-circle me-1"></i> Add
                            </button>
                        </div>

                        @foreach($fields as $field)
                            @php
                                $displayLabel = (!empty($field->app_showe) && !empty($field->app_showt)) 
                                    ? $field->app_showe . ' (' . $field->app_showt . ')' 
                                    : ($field->app_showe ?? $field->app_showt ?? $field->field_name);
                            @endphp
                            <div class="modal-field-item p-2 mb-2 border rounded d-flex justify-content-between align-items-center" 
                                 data-field="{{ $field->field_name }}" 
                                 data-label="{{ $displayLabel }}"
                                 style="cursor: pointer; font-size: 12.5px; transition: all 0.2s;">
                                <span class="field-label-text">
                                    <i class="bi bi-tag me-2 text-muted"></i>
                                    {{ $displayLabel }}
                                </span>
                                <button type="button" class="btn btn-primary btn-sm px-2 py-1 select-field-btn" style="font-size: 11px; border-radius: 4px;">
                                    <i class="bi bi-plus-circle me-1"></i> Add
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-field-item:hover {
            background-color: rgba(85, 110, 230, 0.05);
            border-color: #556ee6 !important;
        }
        .modal-field-item:hover .field-label-text {
            color: #556ee6;
            font-weight: 500;
        }
        .modal-field-item:hover .bi-tag {
            color: #556ee6 !important;
        }
    </style>

    <!-- Custom Text Modal (Replaces window.prompt) -->
    <div class="modal fade" id="customTextModal" tabindex="-1" aria-labelledby="customTextModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4 bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-fonts fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="customTextModalLabel">ระบุข้อความทั่วไป (Custom Text)</h6>
                            <small class="text-muted">ข้อความคงที่ที่จะพิมพ์ลงในช่องนี้</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <label for="customTextInput" class="form-label fw-semibold text-dark mb-1">ข้อความ:</label>
                    <input type="text" class="form-control" id="customTextInput" placeholder="เช่น NO COMMERCIAL VALUE, หมายเหตุ..." autocomplete="off">
                    <div class="text-danger small mt-2 d-none" id="customTextError">
                        <i class="bi bi-exclamation-circle me-1"></i> กรุณาระบุข้อความก่อนยืนยัน
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-warning btn-sm px-4 rounded-pill fw-semibold text-dark" id="btnConfirmCustomText">
                        <i class="bi bi-check-lg me-1"></i> ยืนยัน
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Unsaved Changes Warning Modal -->
    <div class="modal fade" id="unsavedWarningModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4 bg-white">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-exclamation-triangle fs-5"></i>
                        </div>
                        <h6 class="modal-title fw-bold text-dark mb-0">มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก</h6>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-secondary">
                    คุณมีการแก้ไขเลย์เอาต์ที่ยังไม่ได้กด <strong>Save Layout</strong> หากออกจากหน้านี้ การเปลี่ยนแปลงจะไม่ถูกบันทึก คุณแน่ใจหรือไม่ว่าต้องการออกจากหน้านี้?
                </div>
                <div class="modal-footer border-top py-2 px-4 bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">อยู่หน้านี้ต่อ</button>
                    <a href="{{ route('detail-template') }}" class="btn btn-danger btn-sm px-4 rounded-pill fw-semibold" id="btnConfirmLeave">
                        ออกจากหน้านี้
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="actionToast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 fs-6" id="actionToastBody">
                    <!-- Message here -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activeCell = null;
        let activeTargetPlus = null;
        let pendingCustomTextCell = null;
        let isDirty = false;
        let currentMode = 'design'; // 'design' or 'preview'

        const cells = document.querySelectorAll('.grid-cell:not(.cell-last)');
        const fields = document.querySelectorAll('.field-item');
        const targetPluses = document.querySelectorAll('.target-plus');
        const btnClear = document.getElementById('btnClearCell');
        const btnReset = document.getElementById('btnResetAll');
        const btnSave = document.getElementById('btnSaveLayout');
        const btnBack = document.getElementById('btnBack');
        const btnModeDesign = document.getElementById('btnModeDesign');
        const btnModePreview = document.getElementById('btnModePreview');
        const dirtyIndicator = document.getElementById('dirtyIndicator');
        const designTableLayout = document.getElementById('design-table-layout');

        // Load saved layout from Database or fallback to localStorage
        const layoutKey = 'detail_layout_config_' + '{{ $detailTemplate->detail_template_id }}';
        const dbLayout = {!! json_encode($layoutConfig ?? []) !!};
        
        let savedLayout = (dbLayout && Object.keys(dbLayout).length > 0)
            ? dbLayout
            : (JSON.parse(localStorage.getItem(layoutKey)) || {});

        // Map of all fields for lookup
        const fieldsMap = {
            @foreach($fields as $field)
                {!! json_encode($field->field_name) !!}: {
                    field_name: {!! json_encode($field->field_name) !!},
                    app_showe: {!! json_encode($field->app_showe) !!},
                    app_showt: {!! json_encode($field->app_showt ? $field->app_showt : ($field->app_showe ? $field->app_showe : $field->field_name)) !!}
                },
            @endforeach
        };

        // Helper to update dirty state
        function setDirty(state) {
            isDirty = state;
            if (dirtyIndicator) {
                if (isDirty) {
                    dirtyIndicator.classList.remove('d-none');
                } else {
                    dirtyIndicator.classList.add('d-none');
                }
            }
        }

        // Helper for Toast notification
        function showToast(type, message) {
            const toastEl = document.getElementById('actionToast');
            const toastBody = document.getElementById('actionToastBody');
            if (!toastEl || !toastBody) return;

            toastEl.className = 'toast align-items-center text-white border-0 shadow-lg rounded-3';
            if (type === 'success') {
                toastEl.classList.add('bg-success');
                toastBody.innerHTML = '<i class="bi bi-check-circle-fill fs-5"></i> ' + message;
            } else if (type === 'danger') {
                toastEl.classList.add('bg-danger');
                toastBody.innerHTML = '<i class="bi bi-exclamation-triangle-fill fs-5"></i> ' + message;
            } else {
                toastEl.classList.add('bg-primary');
                toastBody.innerHTML = '<i class="bi bi-info-circle-fill fs-5"></i> ' + message;
            }

            const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
            toast.show();
        }

        // Helper to get display label (app_showt) for a field
        function getFieldDisplayLabel(fieldId, fallbackLabel, customText) {
            if (fieldId === 'custom_text') {
                return `Custom Text: ${customText || ''}`;
            }
            if (fieldsMap[fieldId]) {
                return fieldsMap[fieldId].app_showt;
            }
            if (fallbackLabel && fallbackLabel.includes('(')) {
                const startIdx = fallbackLabel.indexOf('(');
                const endIdx = fallbackLabel.lastIndexOf(')');
                if (startIdx !== -1 && endIdx !== -1 && endIdx > startIdx) {
                    return fallbackLabel.substring(startIdx + 1, endIdx).trim();
                }
            }
            return fallbackLabel || fieldId;
        }

        // Helper to get simulated realistic mock data for preview mode (Goods Items)
        function getSampleValue(fieldId, customText) {
            if (fieldId === 'custom_text') {
                return customText || '';
            }
            const lower = (fieldId || '').toLowerCase();
            if (lower.includes('item_no') || lower.includes('itemno') || lower.includes('line_no') || lower.includes('seq')) return '1';
            if (lower.includes('mark') || lower.includes('shipping_mark')) return 'ABC / BKK / 001-100';
            if (lower.includes('package_desc') || lower.includes('pack_desc')) return '100 CARTONS';
            if (lower.includes('desc') || lower.includes('goods') || lower.includes('item_name')) return 'PARTS OF INTEGRATED CIRCUITS';
            if (lower.includes('weight') || lower.includes('net_weight')) return '1,250.00 KGM';
            if (lower.includes('tariff') || lower.includes('hs_code') || lower.includes('tariff_code')) return '8542.31.00';
            if (lower.includes('stat') || lower.includes('statistic')) return '000';
            if (lower.includes('qty') || lower.includes('quantity')) return '50,000.00 C62';
            if (lower.includes('fob_foreign') || lower.includes('fob_fc') || lower.includes('fob_amt_foreign') || (lower.includes('fob') && lower.includes('cur'))) return 'USD 45,000.00';
            if (lower.includes('fob_thb') || lower.includes('fob_amt_thb') || lower.includes('fob_baht')) return '1,575,000.00';
            if (lower.includes('fob')) return 'USD 45,000.00';
            if (lower.includes('customs_value') || lower.includes('assess_amt') || lower.includes('assessment')) return '1,575,000.00';
            if (lower.includes('privilege') || lower.includes('priv')) return '001 (BOI)';
            if (lower.includes('duty_rate') || lower.includes('tax_rate') || lower.includes('rate')) return '0.00 %';
            if (lower.includes('duty_amt') || lower.includes('export_duty') || lower.includes('duty')) return '0.00';
            if (lower.includes('officer') || lower.includes('customs_officer')) return 'ตรวจสอบถูกต้อง';
            
            return getFieldDisplayLabel(fieldId, '');
        }

        // Normalize savedLayout entries for backward compatibility
        Object.keys(savedLayout).forEach(key => {
            const data = savedLayout[key];
            if (data && !Array.isArray(data)) {
                if (data.fieldId) {
                    savedLayout[key] = [data];
                } else {
                    savedLayout[key] = [];
                }
            }
        });

        // Merge any legacy target-plus configurations (tp-*) into parent cell
        Object.keys(savedLayout).forEach(key => {
            if (key.startsWith('tp-')) {
                const tpEl = document.getElementById(key);
                if (tpEl) {
                    const cellEl = tpEl.closest('.grid-cell');
                    if (cellEl) {
                        const cellId = cellEl.id;
                        if (!savedLayout[cellId]) {
                            savedLayout[cellId] = [];
                        }
                        const tpDataList = savedLayout[key];
                        if (Array.isArray(tpDataList)) {
                            tpDataList.forEach(item => {
                                if (!savedLayout[cellId].some(x => x.fieldId === item.fieldId)) {
                                    savedLayout[cellId].push(item);
                                }
                            });
                        }
                    }
                }
                delete savedLayout[key];
            }
        });

        // Custom Text Modal trigger
        function requestCustomText(cellEl) {
            pendingCustomTextCell = cellEl;
            const input = document.getElementById('customTextInput');
            const error = document.getElementById('customTextError');
            if (input) input.value = '';
            if (error) error.classList.add('d-none');

            const modalEl = document.getElementById('customTextModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

            setTimeout(() => {
                if (input) input.focus();
            }, 300);
        }

        // Confirm Custom Text button handler
        const btnConfirmCustom = document.getElementById('btnConfirmCustomText');
        if (btnConfirmCustom) {
            btnConfirmCustom.addEventListener('click', function() {
                const input = document.getElementById('customTextInput');
                const error = document.getElementById('customTextError');
                const val = input ? input.value.trim() : '';

                if (!val) {
                    if (error) error.classList.remove('d-none');
                    if (input) input.focus();
                    return;
                }

                if (pendingCustomTextCell) {
                    const cellId = pendingCustomTextCell.id;
                    if (!savedLayout[cellId] || !Array.isArray(savedLayout[cellId])) {
                        savedLayout[cellId] = [];
                    }

                    savedLayout[cellId].push({
                        fieldId: 'custom_text',
                        fieldName: `Custom Text: ${val}`,
                        customText: val
                    });

                    renderCellFields(pendingCustomTextCell);
                    setDirty(true);
                }

                const modalEl = document.getElementById('customTextModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();
                pendingCustomTextCell = null;
            });
        }

        // Helper to bind field to grid-cell
        function bindFieldToCell(cellEl, fieldId, fieldLabel) {
            if (fieldId === 'custom_text') {
                requestCustomText(cellEl);
                return;
            }

            const cellId = cellEl.id;
            if (!savedLayout[cellId] || !Array.isArray(savedLayout[cellId])) {
                savedLayout[cellId] = [];
            }

            // Check if already contains this field to prevent duplicates
            const exists = savedLayout[cellId].some(item => item.fieldId === fieldId);
            if (exists) {
                showToast('danger', 'ฟิลด์นี้ถูกเลือกในช่องนี้แล้ว');
                return;
            }

            savedLayout[cellId].push({
                fieldId: fieldId,
                fieldName: fieldLabel,
                customText: null
            });

            renderCellFields(cellEl);
            setDirty(true);
        }

        // Helper to render grid-cell fields (Design vs Preview mode)
        function renderCellFields(cellEl) {
            const cellId = cellEl.id;
            const fieldsList = savedLayout[cellId] || [];
            const contentEl = cellEl.querySelector('.cell-content');
            if (!contentEl) return;

            if (fieldsList.length === 0) {
                cellEl.classList.remove('cell-filled');
                contentEl.innerHTML = '';
                return;
            }

            cellEl.classList.add('cell-filled');

            if (currentMode === 'preview') {
                // Render realistic mock data for preview
                let previewHtml = '<div class="d-flex flex-wrap align-items-center justify-content-center gap-1 w-100">';
                fieldsList.forEach(field => {
                    const val = getSampleValue(field.fieldId, field.customText);
                    previewHtml += `<span class="preview-value" title="${val}">${val}</span>`;
                });
                previewHtml += '</div>';
                contentEl.innerHTML = previewHtml;
            } else {
                // Render design badges with delete button
                let html = '<div class="cell-fields-list d-flex flex-wrap align-items-center justify-content-center gap-1" style="pointer-events: auto;">';
                fieldsList.forEach((field, index) => {
                    const displayLabel = getFieldDisplayLabel(field.fieldId, field.fieldName, field.customText);
                    const badgeClass = field.fieldId === 'custom_text' ? 'tp-badge bg-warning border-warning text-dark' : 'tp-badge';
                    const badgeStyle = field.fieldId === 'custom_text' ? 'color: #000 !important;' : '';
                    html += `
                        <span class="${badgeClass}" data-field-id="${field.fieldId}" style="${badgeStyle}">
                            <span class="tp-badge-label" title="${displayLabel}" style="max-width: 95px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; ${badgeStyle}">${displayLabel}</span>
                            <i class="bi bi-x-circle-fill tp-badge-delete" data-index="${index}" title="Remove field"></i>
                        </span>
                    `;
                });
                html += '</div>';

                contentEl.innerHTML = html;

                // Bind delete button listeners
                const deleteBtns = contentEl.querySelectorAll('.tp-badge-delete');
                deleteBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const index = parseInt(this.getAttribute('data-index'), 10);
                        removeFieldFromCell(cellEl, index);
                    });
                });
            }
        }

        // Helper to remove single field from grid-cell
        function removeFieldFromCell(cellEl, index) {
            const cellId = cellEl.id;
            if (savedLayout[cellId] && Array.isArray(savedLayout[cellId])) {
                savedLayout[cellId].splice(index, 1);
                if (savedLayout[cellId].length === 0) {
                    delete savedLayout[cellId];
                }
                setDirty(true);
            }
            renderCellFields(cellEl);
        }

        // Helper to unbind field from grid-cell
        function unbindFieldFromCell(cellEl) {
            const cellId = cellEl.id;
            delete savedLayout[cellId];
            setDirty(true);
            renderCellFields(cellEl);
        }

        // Re-render all cells
        function renderAllCells() {
            cells.forEach(cell => {
                renderCellFields(cell);
            });
        }

        // Initialize Grid with saved config
        renderAllCells();

        // Mode Switching Handlers
        if (btnModeDesign && btnModePreview) {
            btnModeDesign.addEventListener('click', function() {
                currentMode = 'design';
                btnModeDesign.classList.add('mode-btn-active', 'text-white');
                btnModeDesign.classList.remove('text-secondary');
                btnModePreview.classList.remove('mode-btn-active', 'text-white');
                btnModePreview.classList.add('text-secondary');
                designTableLayout.classList.remove('preview-mode');
                renderAllCells();
            });

            btnModePreview.addEventListener('click', function() {
                currentMode = 'preview';
                btnModePreview.classList.add('mode-btn-active', 'text-white');
                btnModePreview.classList.remove('text-secondary');
                btnModeDesign.classList.remove('mode-btn-active', 'text-white');
                btnModeDesign.classList.add('text-secondary');
                designTableLayout.classList.add('preview-mode');
                if (activeCell) {
                    activeCell.classList.remove('active');
                    activeCell = null;
                }
                renderAllCells();
            });
        }

        // Cell click handler
        cells.forEach(cell => {
            cell.addEventListener('click', function() {
                if (currentMode === 'preview') return;
                if (activeCell) {
                    activeCell.classList.remove('active');
                }
                activeCell = this;
                activeCell.classList.add('active');
            });
        });

        // Field item click handler (right sidebar)
        fields.forEach(field => {
            field.addEventListener('click', function() {
                if (currentMode === 'preview') {
                    showToast('primary', 'กรุณาสลับกลับมาที่ Design Mode ก่อนทำการเลือกฟิลด์');
                    return;
                }
                if (!activeCell) {
                    showToast('danger', 'กรุณาคลิกเลือกช่องตาราง (Grid Cell) บนฟอร์มก่อน');
                    return;
                }
                const contentEl = activeCell.querySelector('.cell-content');
                if (!contentEl) {
                    showToast('danger', 'ช่องนี้ไม่สามารถผูกฟิลด์ข้อมูลได้');
                    activeCell.classList.remove('active');
                    activeCell = null;
                    return;
                }
                const fieldId = this.getAttribute('data-field');
                const fieldName = this.querySelector('span').innerText.trim();

                bindFieldToCell(activeCell, fieldId, fieldName);

                activeCell.classList.remove('active');
                activeCell = null;
            });
        });

        // Target-plus click handler (opens modal)
        targetPluses.forEach(tp => {
            tp.addEventListener('click', function(e) {
                e.stopPropagation();
                if (currentMode === 'preview') return;
                
                activeTargetPlus = this;

                const modalSearch = document.getElementById('modalFieldSearchInput');
                if (modalSearch) {
                    modalSearch.value = '';
                    modalSearch.dispatchEvent(new Event('input'));
                }

                const modalEl = document.getElementById('fieldSelectorModal');
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            });
        });

        // Modal field selection handler
        const modalFieldItems = document.querySelectorAll('.modal-field-item');
        modalFieldItems.forEach(item => {
            const addBtn = item.querySelector('.select-field-btn');
            
            const selectFn = function(e) {
                e.stopPropagation();
                if (!activeTargetPlus) return;

                const fieldId = item.getAttribute('data-field');
                const fieldLabel = item.getAttribute('data-label');

                const cellEl = activeTargetPlus.closest('.grid-cell');
                if (cellEl) {
                    bindFieldToCell(cellEl, fieldId, fieldLabel);
                }

                const modalEl = document.getElementById('fieldSelectorModal');
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.hide();

                activeTargetPlus = null;
            };

            item.addEventListener('click', selectFn);
            if (addBtn) {
                addBtn.addEventListener('click', selectFn);
            }
        });

        // Modal search filtering
        const modalSearchInput = document.getElementById('modalFieldSearchInput');
        if (modalSearchInput) {
            modalSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                modalFieldItems.forEach(item => {
                    const text = item.innerText.toLowerCase();
                    const fieldId = item.getAttribute('data-field').toLowerCase();
                    if (text.includes(query) || fieldId.includes(query)) {
                        item.style.setProperty('display', 'flex', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }

        // Clear cell
        btnClear.addEventListener('click', function() {
            if (!activeCell) {
                showToast('danger', 'กรุณาคลิกเลือกช่องที่ต้องการล้างข้อมูลก่อน');
                return;
            }
            unbindFieldFromCell(activeCell);
            activeCell.classList.remove('active');
            activeCell = null;
            showToast('primary', 'ล้างข้อมูลในช่องที่เลือกแล้ว');
        });

        // Reset grid
        btnReset.addEventListener('click', function() {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการล้างการจัดวางทั้งหมดของฟอร์มนี้?')) {
                cells.forEach(cell => {
                    unbindFieldFromCell(cell);
                    cell.classList.remove('active');
                });

                savedLayout = {};
                activeCell = null;
                setDirty(true);
                showToast('primary', 'รีเซ็ตแบบฟอร์มเรียบร้อยแล้ว');
            }
        });

        // AJAX Save Layout (Using dedicated save-layout endpoint)
        btnSave.addEventListener('click', function() {
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

            // Cache in LocalStorage
            localStorage.setItem(layoutKey, JSON.stringify(savedLayout));

            const templateId = '{{ $detailTemplate->detail_template_id }}';
            
            $.ajax({
                url: `/ex-declaration/detail-template/${templateId}/save-layout`,
                type: "POST",
                data: {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    layout: savedLayout
                },
                success: (res) => {
                    setDirty(false);
                    showToast('success', res.message || 'บันทึกตำแหน่งเลย์เอาต์เรียบร้อยแล้ว!');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                },
                error: (xhr) => {
                    const errMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                        ? xhr.responseJSON.message 
                        : (xhr.statusText || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                    showToast('danger', errMsg);
                    console.error(xhr.responseText);
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                }
            });
        });

        // Back button navigation protection (Dirty check)
        if (btnBack) {
            btnBack.addEventListener('click', function(e) {
                if (isDirty) {
                    e.preventDefault();
                    const modalEl = document.getElementById('unsavedWarningModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });
        }

        // Browser beforeunload protection
        window.addEventListener('beforeunload', function(e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // --- HTML5 Drag and Drop Handlers ---
        fields.forEach(field => {
            field.addEventListener('dragstart', function(e) {
                if (currentMode === 'preview') {
                    e.preventDefault();
                    return;
                }
                e.dataTransfer.setData('field-id', this.getAttribute('data-field'));
                e.dataTransfer.setData('field-name', this.querySelector('span').innerText.trim());
                this.style.opacity = '0.5';
            });

            field.addEventListener('dragend', function() {
                this.style.opacity = '1.0';
            });
        });

        // Make cells drop targets
        cells.forEach(cell => {
            cell.addEventListener('dragover', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                const contentEl = this.querySelector('.cell-content');
                if (contentEl) {
                    this.classList.add('drag-over');
                }
            });

            cell.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            cell.addEventListener('drop', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                this.classList.remove('drag-over');

                const contentEl = this.querySelector('.cell-content');
                if (!contentEl) return;

                const fieldId = e.dataTransfer.getData('field-id');
                const fieldName = e.dataTransfer.getData('field-name');

                if (!fieldId || !fieldName) return;

                bindFieldToCell(this, fieldId, fieldName);
            });
        });

        // Make target-plus elements drop targets
        targetPluses.forEach(tp => {
            tp.addEventListener('dragover', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('drag-over');
            });

            tp.addEventListener('dragleave', function(e) {
                e.stopPropagation();
                this.classList.remove('drag-over');
            });

            tp.addEventListener('drop', function(e) {
                if (currentMode === 'preview') return;
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drag-over');

                const fieldId = e.dataTransfer.getData('field-id');
                const fieldName = e.dataTransfer.getData('field-name');

                if (!fieldId || !fieldName) return;

                const cellEl = this.closest('.grid-cell');
                if (cellEl) {
                    bindFieldToCell(cellEl, fieldId, fieldName);
                }
            });
        });

        // Search filter for fields (right sidebar)
        const searchInput = document.getElementById('fieldSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                fields.forEach(field => {
                    const text = field.innerText.toLowerCase();
                    const fieldId = field.getAttribute('data-field').toLowerCase();
                    if (text.includes(query) || fieldId.includes(query)) {
                        field.style.setProperty('display', 'flex', 'important');
                    } else {
                        field.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }
    });
</script>
@endpush

