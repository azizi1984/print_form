<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Detail Template - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .design-workspace {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .designer-container {
            flex: 1;
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .design-sheet-wrapper {
            width: 100%;
        }

        #design-table-layout {
            display: grid;
            grid-template-columns: 32px calc(34% - 20px) calc(18% - 12px) 14% 19% 15%;
            grid-template-rows: repeat(4, 42px) 35px;
            background: #ffffff;
            border: 2px solid #000000 !important;
            border-radius: 0 !important;
            overflow: hidden;
            width: 100%;
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
            padding: 0 10px;
            text-align: center;
        }

        .grid-cell:hover {
            background-color: rgba(85, 110, 230, 0.08);
        }

        .grid-cell.active {
            background-color: rgba(85, 110, 230, 0.15);
            box-shadow: inset 0 0 0 2px #000000;
            font-weight: bold;
        }

        .cell-last {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .cell-filled {
            background-color: rgba(52, 195, 143, 0.1);
            color: #000000;
            font-weight: bold;
            box-shadow: inset 0 0 0 2px #000000;
        }

        .cell-info-popover {
            position: absolute;
            font-size: 9px;
            top: 2px;
            left: 8px;
            color: #000000;
            white-space: pre-wrap;
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
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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

        .target-plus.has-field {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            width: auto !important;
            height: auto !important;
            border-radius: 0 !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .target-plus.has-field::before {
            content: none !important;
        }

        .tp-badge {
            background-color: #34c38f;
            border: 1px solid #2ca579;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #ffffff;
            font-weight: bold;
            white-space: nowrap;
            vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

        .tp-add-btn {
            transition: all 0.2s;
        }

        .tp-add-btn:hover {
            background-color: #157347 !important;
            transform: scale(1.15);
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .grid-cell.drag-over {
            background-color: rgba(85, 110, 230, 0.12) !important;
            border: 2px dashed #556ee6 !important;
        }

        .target-plus.drag-over {
            transform: scale(1.25);
            background-color: #157347 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
        }

        .cell-field-label {
            font-weight: bold;
            font-size: 11px;
            color: #000000;
            display: inline-block;
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }

        .cell-field-delete {
            color: #ff3d60;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
            transition: color 0.15s;
            margin-left: 4px;
        }

        .cell-field-delete:hover {
            color: #ff1f48;
        }
    </style>
</head>
<body class="@yield('body-class') bg-light">
    @include('layouts.topbar')

    <div class="container-fluid px-4 mt-4 mb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('detail-template') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <span class="fs-4 fw-bold text-dark">Layout Designer: {{ $detailTemplate->detail_template_name }}</span>
            </div>
            <div class="d-flex gap-2">
                <div class="dimension-info-badge">
                    <i class="bi bi-aspect-ratio"></i> Column Ratio: 10.3 CM : 8.7 CM
                </div>
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" id="btnSaveLayout">
                    <i class="bi bi-save me-1"></i> Save Layout
                </button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activeCell = null;
            let activeTargetPlus = null;
            const cells = document.querySelectorAll('.grid-cell:not(.cell-last)');
            const fields = document.querySelectorAll('.field-item');
            const targetPluses = document.querySelectorAll('.target-plus');
            const btnClear = document.getElementById('btnClearCell');
            const btnReset = document.getElementById('btnResetAll');
            const btnSave = document.getElementById('btnSaveLayout');

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

            // Helper to get display label (app_showt) for a field
            function getFieldDisplayLabel(fieldId, fallbackLabel, customText) {
                if (fieldId === 'custom_text') {
                    return `Custom Text: ${customText || ''}`;
                }
                if (fieldsMap[fieldId]) {
                    return fieldsMap[fieldId].app_showt;
                }
                // Try to extract Thai text in parenthesis if exists in fallbackLabel
                if (fallbackLabel && fallbackLabel.includes('(')) {
                    const startIdx = fallbackLabel.indexOf('(');
                    const endIdx = fallbackLabel.lastIndexOf(')');
                    if (startIdx !== -1 && endIdx !== -1 && endIdx > startIdx) {
                        return fallbackLabel.substring(startIdx + 1, endIdx).trim();
                    }
                }
                return fallbackLabel || fieldId;
            }

            // Normalize savedLayout entries for backward compatibility (ensure all entries are arrays of objects)
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

            // Merge any legacy target-plus configurations (tp-*) into their parent cell's configuration
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

            // Helper to bind field to grid-cell
            function bindFieldToCell(cellEl, fieldId, fieldLabel) {
                const cellId = cellEl.id;
                if (!savedLayout[cellId] || !Array.isArray(savedLayout[cellId])) {
                    savedLayout[cellId] = [];
                }

                let customTextVal = null;
                if (fieldId === 'custom_text') {
                    customTextVal = prompt("กรุณาระบุข้อความทั่วไป (Custom Text):");
                    if (customTextVal === null) {
                        return; // user cancelled
                    }
                    if (customTextVal.trim() === '') {
                        alert("ข้อความต้องไม่เป็นค่าว่าง");
                        return;
                    }
                    customTextVal = customTextVal.trim();
                } else {
                    // Check if already contains this field to prevent duplicates
                    const exists = savedLayout[cellId].some(item => item.fieldId === fieldId);
                    if (exists) {
                        alert('ฟิลด์นี้ถูกเลือกในช่องนี้แล้ว');
                        return;
                    }
                }

                savedLayout[cellId].push({
                    fieldId: fieldId,
                    fieldName: fieldId === 'custom_text' ? `Custom Text: ${customTextVal}` : fieldLabel,
                    customText: customTextVal
                });

                renderCellFields(cellEl);
            }

            // Helper to render grid-cell fields as green badges
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

                let html = '<div class="cell-fields-list d-flex flex-wrap align-items-center justify-content-center gap-1" style="pointer-events: auto;">';
                fieldsList.forEach((field, index) => {
                    const displayLabel = getFieldDisplayLabel(field.fieldId, field.fieldName, field.customText);
                    const badgeClass = field.fieldId === 'custom_text' ? 'tp-badge bg-warning border-warning text-dark' : 'tp-badge';
                    const badgeStyle = field.fieldId === 'custom_text' ? 'color: #000 !important;' : '';
                    html += `
                        <span class="${badgeClass}" data-field-id="${field.fieldId}" style="${badgeStyle}">
                            <span class="tp-badge-label" title="${displayLabel}" style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; vertical-align: middle; ${badgeStyle}">${displayLabel}</span>
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
                        e.stopPropagation(); // prevent cell selection trigger
                        const index = parseInt(this.getAttribute('data-index'), 10);
                        removeFieldFromCell(cellEl, index);
                    });
                });
            }

            // Helper to remove single field from grid-cell
            function removeFieldFromCell(cellEl, index) {
                const cellId = cellEl.id;
                if (savedLayout[cellId] && Array.isArray(savedLayout[cellId])) {
                    savedLayout[cellId].splice(index, 1);
                    if (savedLayout[cellId].length === 0) {
                        delete savedLayout[cellId];
                    }
                }
                renderCellFields(cellEl);
            }

            // Helper to unbind field from grid-cell
            function unbindFieldFromCell(cellEl) {
                const cellId = cellEl.id;
                delete savedLayout[cellId];
                renderCellFields(cellEl);
            }

            // Initialize Grid with saved config
            Object.keys(savedLayout).forEach(cellId => {
                const cell = document.getElementById(cellId);
                if (cell) {
                    renderCellFields(cell);
                }
            });

            // Cell click handler
            cells.forEach(cell => {
                cell.addEventListener('click', function() {
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
                    if (!activeCell) {
                        alert('Please click on a grid cell in the sheet layout first!');
                        return;
                    }
                    const contentEl = activeCell.querySelector('.cell-content');
                    if (!contentEl) {
                        alert('This cell cannot bind data fields.');
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
                    e.stopPropagation(); // Prevent selecting the parent cell
                    
                    activeTargetPlus = this;

                    // Reset and show modal
                    const modalSearch = document.getElementById('modalFieldSearchInput');
                    if (modalSearch) {
                        modalSearch.value = '';
                        // Trigger input event to show all fields
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
                    alert('Please select a cell to clear.');
                    return;
                }
                unbindFieldFromCell(activeCell);
                activeCell.classList.remove('active');
                activeCell = null;
            });

            // Reset grid
            btnReset.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all configurations?')) {
                    cells.forEach(cell => {
                        unbindFieldFromCell(cell);
                        cell.classList.remove('active');
                    });

                    savedLayout = {};
                    activeCell = null;
                }
            });

            // AJAX Save
            btnSave.addEventListener('click', function() {
                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

                // Save to LocalStorage
                localStorage.setItem(layoutKey, JSON.stringify(savedLayout));

                // Perform AJAX save to database (sending layout configuration as description/metadata JSON)
                const templateId = '{{ $detailTemplate->detail_template_id }}';
                
                // We will update the template description with the JSON configuration
                $.ajax({
                    url: `/ex-declaration/detail-template/${templateId}`,
                    type: "POST",
                    data: {
                        _method: 'PUT',
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        detail_template_name: {!! json_encode($detailTemplate->detail_template_name) !!},
                        status: {!! json_encode($detailTemplate->status) !!},
                        description: JSON.stringify(savedLayout)
                    },
                    success: (res) => {
                        setTimeout(() => {
                            alert('Header Template Layout saved successfully!');
                            this.disabled = false;
                            this.innerHTML = originalHtml;
                        }, 500);
                    },
                    error: (xhr) => {
                        const errMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                            ? xhr.responseJSON.message 
                            : (xhr.statusText || 'Unknown error');
                        alert('Failed to save layout configurations: ' + errMsg);
                        console.error(xhr.responseText);
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                });
            });

            // --- HTML5 Drag and Drop Handlers ---

            // Make the right sidebar fields draggable
            fields.forEach(field => {
                field.addEventListener('dragstart', function(e) {
                    // Set data for dragging
                    e.dataTransfer.setData('field-id', this.getAttribute('data-field'));
                    e.dataTransfer.setData('field-name', this.querySelector('span').innerText.trim());
                    // Visual state
                    this.style.opacity = '0.5';
                });

                field.addEventListener('dragend', function() {
                    this.style.opacity = '1.0';
                });
            });

            // Make cells drop targets
            cells.forEach(cell => {
                cell.addEventListener('dragover', function(e) {
                    e.preventDefault(); // Required to allow drop
                    const contentEl = this.querySelector('.cell-content');
                    if (contentEl) {
                        this.classList.add('drag-over');
                    }
                });

                cell.addEventListener('dragleave', function() {
                    this.classList.remove('drag-over');
                });

                cell.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over');

                    const contentEl = this.querySelector('.cell-content');
                    if (!contentEl) {
                        return;
                    }

                    const fieldId = e.dataTransfer.getData('field-id');
                    const fieldName = e.dataTransfer.getData('field-name');

                    if (!fieldId || !fieldName) return;

                    bindFieldToCell(this, fieldId, fieldName);
                });
            });

            // Make target-plus elements drop targets
            targetPluses.forEach(tp => {
                tp.addEventListener('dragover', function(e) {
                    e.preventDefault(); // Required to allow drop
                    e.stopPropagation(); // Stop bubbling to cell
                    this.classList.add('drag-over');
                });

                tp.addEventListener('dragleave', function(e) {
                    e.stopPropagation();
                    this.classList.remove('drag-over');
                });

                tp.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Stop bubbling to cell
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
</body>
</html>
