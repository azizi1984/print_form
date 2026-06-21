<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Header Template - {{ config('app.name', 'Laravel') }}</title>

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
            grid-template-columns: repeat(6, calc(54.2% / 6)) repeat(6, calc(45.8% / 6));
            grid-template-rows: repeat(19, 40px) 30px;
            background: #ffffff;
            border: 2px solid #000000 !important;
            border-radius: 8px;
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
            margin-top: 15px;
            flex: 1;
            overflow-y: auto;
            max-height: 450px;
        }

        .field-item {
            padding: 10px 14px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin-bottom: 8px;
            cursor: pointer;
            font-size: 13px;
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
    </style>
</head>
<body class="@yield('body-class') bg-light">
    @include('layouts.topbar')

    <div class="container-fluid px-4 mt-4 mb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('header-template') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <span class="fs-4 fw-bold text-dark">Layout Designer: {{ $headerTemplate->header_template_name }}</span>
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
                        <div class="grid-cell" data-col="left" data-row="1" id="cell-left-1" style="grid-column: 1 / span 3; grid-row: 1; border-bottom: none !important;">
                            <span class="cell-info-popover">ผู้ส่งของออก (ชื่อ ที่อยู่ โทรศัพท์)</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="1" id="cell-left-1-tax-header" style="grid-column: 4 / span 2; grid-row: 1; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">เลขประจำตัวผู้เสียภาษีอากร</span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="1" id="cell-left-2-branch" style="grid-column: 6 / span 1; grid-row: 1; border-top: none !important; border-bottom: none !important;">
                            <span class="cell-info-popover position-center">สาขา</span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="1" id="cell-right-1" style="grid-column: 7 / span 3; grid-row: 1; border-bottom: none !important;">
                            <span class="cell-info-popover">ประเภทใบขนฯ</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="1" id="cell-right-1-page" style="grid-column: 10 / span 3; grid-row: 1; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">เลขที่ใบขนฯ</span>
                            <span class="target-plus pos-top-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid-cell" data-col="left" data-row="2" id="cell-left-2" style="grid-column: 1 / span 3; grid-row: 2; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="2" id="cell-left-2-tax" style="grid-column: 4 / span 2; grid-row: 2; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="2" id="cell-left-2-branch" style="grid-column: 6 / span 1; grid-row: 2; border-top: none !important;">
                            <span class="target-plus pos-center"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="2" id="cell-right-2" style="grid-column: 7 / span 3; grid-row: 2; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="2" id="cell-right-2-no" style="grid-column: 10 / span 3; grid-row: 2; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 3 -->
                        <div class="grid-cell" data-col="left" data-row="3" id="cell-left-3" style="grid-column: 1 / span 6; grid-row: 3; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="3" id="cell-right-3" style="grid-column: 7 / span 6; grid-row: 3; border-top: none !important; border-bottom: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        {{-- <div class="grid-cell cell-last" data-col="right" data-row="3" id="cell-right-3-blank" style="grid-column: 10 / span 3; grid-row: 3 / span 4; border-right: none !important;">
                            <!-- Spacing -->
                        </div> --}}

                        <!-- Row 4 -->
                        <div class="grid-cell" data-col="left" data-row="4" id="cell-left-4" style="grid-column: 1 / span 6; grid-row: 4; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="4" id="cell-right-4" style="grid-column: 7 / span 3; grid-row: 4; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 5 -->
                        <div class="grid-cell" data-col="left" data-row="5" id="cell-left-5" style="grid-column: 1 / span 6; grid-row: 5; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="5" id="cell-right-5" style="grid-column: 7 / span 3; grid-row: 5; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 6 -->
                        <div class="grid-cell" data-col="left" data-row="6" id="cell-left-6" style="grid-column: 1 / span 6; grid-row: 6; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="6" id="cell-right-6" style="grid-column: 7 / span 3; grid-row: 6; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 7 -->
                        <div class="grid-cell" data-col="left" data-row="7" id="cell-left-7" style="grid-column: 1 / span 6; grid-row: 7;">
                            <span class="cell-info-popover">ชื่อและเลขที่บัตรผ่านพิธีการ</span>
                            <span class="target-plus pos-inline"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="7" id="cell-right-7" style="grid-column: 7 / span 6; grid-row: 7; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">สั่งการตรวจ</span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 8 -->
                        <div class="grid-cell" data-col="left" data-row="8" id="cell-left-8" style="grid-column: 1 / span 6; grid-row: 8; border-bottom: none !important;">
                            <span class="cell-info-popover">ตัวแทนออกของ</span>
                            <span class="target-plus pos-inline"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="8" id="cell-right-8" style="grid-column: 7 / span 6; grid-row: 8; border-top: none !important; border-bottom: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 9 -->
                        <div class="grid-cell" data-col="left" data-row="9" id="cell-left-9-broker-left" style="grid-column: 1 / span 3; grid-row: 9; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="9" id="cell-left-9-broker-right" style="grid-column: 4 / span 3; grid-row: 9; border-top: none !important;">
                            <span class="target-plus pos-bottom-right"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="9" id="cell-right-9" style="grid-column: 7 / span 6; grid-row: 9; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 10 -->
                        <div class="grid-cell" data-col="left" data-row="10" id="cell-left-10" style="grid-column: 1 / span 6; grid-row: 10; border-bottom: none !important;">
                            <span class="cell-info-popover">ชื่อยานพาหนะ</span>
                            <span class="target-plus pos-inline"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell cell-last" data-col="right" data-row="10" id="cell-right-10-tax-header" style="grid-column: 7 / span 2; grid-row: 10; border-bottom: none !important;">
                            <span class="cell-info-popover" style="color: #000;">ภาษีอากรที่ต้องเสีย</span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="10" id="cell-right-10-tax-val" style="grid-column: 9 / span 2; grid-row: 10 / span 2;">
                            <span class="cell-info-popover">ค่าภาษีอากร (บาท)</span>
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="10" id="cell-right-10-deposit" style="grid-column: 11 / span 2; grid-row: 10 / span 2; border-right: none !important;">
                            <span class="cell-info-popover">เงินประกัน (บาท)</span>
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 11 -->
                        <div class="grid-cell" data-col="left" data-row="11" id="cell-left-11" style="grid-column: 1 / span 6; grid-row: 11; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="11" id="cell-right-11" style="grid-column: 7 / span 2; grid-row: 11; border-top: none !important;">
                            <span class="cell-info-popover">อากรขาออก</span>
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 12 -->
                        <div class="grid-cell" data-col="left" data-row="12" id="cell-left-12-export-header" style="grid-column: 1 / span 3; grid-row: 12; border-bottom: none !important;">
                            <span class="cell-info-popover">ส่งออกโดยทาง</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="12" id="cell-left-12-date-header" style="grid-column: 4 / span 3; grid-row: 12; border-bottom: none !important;">
                            <span class="cell-info-popover">วันที่ส่งออก</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="12" id="cell-right-12-tax-header" style="grid-column: 7 / span 6; grid-row: 12; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">เลขที่ผู้ชำระภาษีอากร / ประกัน</span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 13 -->
                        <div class="grid-cell" data-col="left" data-row="13" id="cell-left-13" style="grid-column: 1 / span 3; grid-row: 13; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="13" id="cell-left-13-date" style="grid-column: 4 / span 3; grid-row: 13; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="13" id="cell-right-13" style="grid-column: 7 / span 6; grid-row: 13; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 14 -->
                        <div class="grid-cell" data-col="left" data-row="14" id="cell-left-14-discharge-header" style="grid-column: 1 / span 3; grid-row: 14; border-bottom: none !important;">
                            <span class="cell-info-popover">ท่าหรือที่ตรวจปล่อยของ รหัส</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="14" id="cell-left-14-loading-header" style="grid-column: 4 / span 3; grid-row: 14; border-bottom: none !important;">
                            <span class="cell-info-popover">ท่าหรือที่รับบรรทุกของ รหัส</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="14" id="cell-right-14-sold-header" style="grid-column: 7 / span 3; grid-row: 14; border-bottom: none !important;">
                            <span class="cell-info-popover">ขายไปยังประเทศ รหัส</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="14" id="cell-right-14-dest-header" style="grid-column: 10 / span 3; grid-row: 14; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">ประเทศปลายทาง รหัส</span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 15 -->
                        <div class="grid-cell" data-col="left" data-row="15" id="cell-left-15-discharge" style="grid-column: 1 / span 3; grid-row: 15; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="15" id="cell-left-15-loading" style="grid-column: 4 / span 3; grid-row: 15; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="15" id="cell-right-15-sold" style="grid-column: 7 / span 3; grid-row: 15; border-top: none !important; border-bottom: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="15" id="cell-right-15-dest" style="grid-column: 10 / span 3; grid-row: 15; border-top: none !important; border-bottom: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 16 -->
                        <div class="grid-cell" data-col="left" data-row="16" id="cell-left-16-discharge-code" style="grid-column: 1 / span 3; grid-row: 16; border-top: none !important;">
                            <span class="target-plus pos-bottom-right"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="16" id="cell-left-16-loading-code" style="grid-column: 4 / span 3; grid-row: 16; border-top: none !important;">
                            <span class="target-plus pos-bottom-right"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="16" id="cell-right-16-sold-code" style="grid-column: 7 / span 3; grid-row: 16; border-top: none !important;">
                            <span class="target-plus pos-bottom-right"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="16" id="cell-right-16-dest-code" style="grid-column: 10 / span 3; grid-row: 16; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-right"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 17 -->
                        <div class="grid-cell" data-col="left" data-row="17" id="cell-left-17-qty-header" style="grid-column: 1 / span 3; grid-row: 17; border-bottom: none !important;">
                            <span class="cell-info-popover">จำนวนหีบห่อ (ตัวเลข)</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="17" id="cell-left-17-text-header" style="grid-column: 4 / span 3; grid-row: 17; border-bottom: none !important;">
                            <span class="cell-info-popover">(ตัวอักษร)</span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="17" id="cell-right-17-rate-header" style="grid-column: 7 / span 6; grid-row: 17; border-bottom: none !important; border-right: none !important;">
                            <span class="cell-info-popover">อัตราแลกเปลี่ยน</span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 18 -->
                        <div class="grid-cell" data-col="left" data-row="18" id="cell-left-18" style="grid-column: 1 / span 3; grid-row: 18; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="left" data-row="18" id="cell-left-18-text" style="grid-column: 4 / span 3; grid-row: 18; border-top: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>
                        <div class="grid-cell" data-col="right" data-row="18" id="cell-right-18" style="grid-column: 7 / span 6; grid-row: 18; border-top: none !important; border-right: none !important;">
                            <span class="target-plus pos-bottom-left"></span>
                            <span class="cell-content"></span>
                        </div>

                        <!-- Row 19 -->
                        <div class="grid-cell cell-last" data-col="left" data-row="19" style="grid-column: 1 / span 6; grid-row: 19;">
                            <span class="cell-info-popover" style="color:#6c757d;">Row 19 (Left - Spacing)</span>
                        </div>
                        <div class="grid-cell cell-last" data-col="right" data-row="19" style="grid-column: 7 / span 6; grid-row: 19; border-right: none !important;">
                            <span class="cell-info-popover" style="color:#6c757d;">Row 19 (Right - Spacing)</span>
                        </div>

                        <!-- Row 20 (Buffer Row) -->
                        <div class="grid-cell cell-last" data-col="left" data-row="20" style="grid-column: 1 / span 6; grid-row: 20; border-bottom: none !important;">
                            <span class="cell-info-popover" style="color:#6c757d;">Row 20 (Left - Buffer)</span>
                        </div>
                        <div class="grid-cell cell-last" data-col="right" data-row="20" style="grid-column: 7 / span 6; grid-row: 20; border-right: none !important; border-bottom: none !important;">
                            <span class="cell-info-popover" style="color:#6c757d;">Row 20 (Right - Buffer)</span>
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
                
                <div class="field-list">
                    <div class="field-item" data-field="exporter_name">
                        <span><i class="bi bi-building me-2"></i> Exporter Name</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="exporter_tax">
                        <span><i class="bi bi-card-text me-2"></i> Exporter Tax ID</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="invoice_no">
                        <span><i class="bi bi-receipt me-2"></i> Invoice No.</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="invoice_date">
                        <span><i class="bi bi-calendar-event me-2"></i> Invoice Date</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="declaration_no">
                        <span><i class="bi bi-file-earmark-text me-2"></i> Declaration No.</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="page_no">
                        <span><i class="bi bi-hash me-2"></i> Page Number</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="package_qty">
                        <span><i class="bi bi-box-seam me-2"></i> Total Package</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="field-item" data-field="customs_office">
                        <span><i class="bi bi-geo-alt me-2"></i> Customs Office</span>
                        <i class="bi bi-plus-circle"></i>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-grid gap-2">
                    <button class="btn btn-outline-danger btn-sm" id="btnClearCell">
                        <i class="bi bi-eraser me-1"></i> Clear Selected Cell
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="btnResetAll">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Grid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-Side Designer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activeCell = null;
            const cells = document.querySelectorAll('.grid-cell:not(.cell-last)');
            const fields = document.querySelectorAll('.field-item');
            const btnClear = document.getElementById('btnClearCell');
            const btnReset = document.getElementById('btnResetAll');
            const btnSave = document.getElementById('btnSaveLayout');

            // Load saved layout from localStorage if it exists
            const layoutKey = 'header_layout_config_' + '{{ $headerTemplate->header_template_id }}';
            let savedLayout = JSON.parse(localStorage.getItem(layoutKey)) || {};

            // Initialize Grid with saved config
            Object.keys(savedLayout).forEach(cellId => {
                const cell = document.getElementById(cellId);
                if (cell) {
                    cell.classList.add('cell-filled');
                    cell.querySelector('.cell-content').innerText = savedLayout[cellId].fieldName;
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

            // Field item click handler
            fields.forEach(field => {
                field.addEventListener('click', function() {
                    if (!activeCell) {
                        alert('Please click on a grid cell in the sheet layout first!');
                        return;
                    }
                    const fieldId = this.getAttribute('data-field');
                    const fieldName = this.querySelector('span').innerText.trim();

                    activeCell.querySelector('.cell-content').innerText = fieldName;
                    activeCell.classList.add('cell-filled');

                    const cellId = activeCell.id;
                    savedLayout[cellId] = {
                        fieldId: fieldId,
                        fieldName: fieldName
                    };

                    activeCell.classList.remove('active');
                    activeCell = null;
                });
            });

            // Clear cell
            btnClear.addEventListener('click', function() {
                if (!activeCell) {
                    alert('Please select a cell to clear.');
                    return;
                }
                const cellId = activeCell.id;
                delete savedLayout[cellId];
                activeCell.querySelector('.cell-content').innerText = '';
                activeCell.classList.remove('cell-filled');
                activeCell.classList.remove('active');
                activeCell = null;
            });

            // Reset grid
            btnReset.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all configurations?')) {
                    cells.forEach(cell => {
                        cell.querySelector('.cell-content').innerText = '';
                        cell.classList.remove('cell-filled');
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
                const templateId = '{{ $headerTemplate->header_template_id }}';
                
                // We will update the template description with the JSON configuration
                $.ajax({
                    url: `/ex-declaration/header-template/${templateId}`,
                    type: "POST",
                    data: {
                        _method: 'PUT',
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        header_template_name: '{{ $headerTemplate->header_template_name }}',
                        status: '{{ $headerTemplate->status }}',
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
                        alert('Failed to save layout configurations.');
                        console.error(xhr.responseText);
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                });
            });
        });
    </script>
</body>
</html>
