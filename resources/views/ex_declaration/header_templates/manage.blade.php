@extends('layouts.app')

@section('page-title', 'Layout Designer: ' . $headerTemplate->header_template_name . ' - ' . config('app.name', 'Print Form'))

@push('styles')
    @include('ex_declaration.header_templates.partials.styles')
@endpush

@section('content')
<div class="header-template-manager">
    <!-- Header Controls -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('header-template') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-xs" id="btnBack">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <div>
                    @php
                        $currentUserProfile = Auth::user()->profile_id ?? 'ZZ00';
                        $isReadOnly = ($headerTemplate->profile_id === 'ZZ00' && $currentUserProfile !== 'ZZ00');
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0">Layout Designer: {{ $headerTemplate->header_template_name }}</h5>
                        @if($isReadOnly)
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                                <i class="bi bi-shield-lock me-1"></i> ส่วนกลาง (Read-Only)
                            </span>
                        @endif
                        <span id="dirtyIndicator" class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 d-none" style="font-size: 11px;">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> มีการเปลี่ยนแปลงที่ยังไม่ได้บันทึก
                        </span>
                    </div>
                    <small class="text-muted">กำหนดตำแหน่งและฟิลด์ข้อมูลบนแบบฟอร์มส่วนหัวใบขนสินค้าขาออก</small>
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
                    <i class="bi bi-aspect-ratio"></i> 10.3 CM : 8.7 CM
                </div>

                @if(!$isReadOnly)
                    <button type="button" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm" id="btnSaveLayout">
                        <i class="bi bi-save me-1"></i> Save Layout
                    </button>
                @else
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill shadow-sm opacity-75" disabled title="เทมเพลตมาตรฐานส่วนกลางเปิดให้อ่านได้อย่างเดียว กรุณาคัดลอกเทมเพลตเพื่อปรับแต่ง">
                        <i class="bi bi-lock-fill me-1"></i> Read Only
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Workspace -->
    <div class="design-workspace">
        <!-- 1. Left Design Grid Layout (18x12 Customs Form) -->
        @include('ex_declaration.header_templates.partials.grid_layout')

        <!-- 2. Right Control Sidebar (Field Selection & Actions) -->
        @include('ex_declaration.header_templates.partials.sidebar_fields')
    </div>
</div>

<!-- 3. Modals & Notifications -->
@include('ex_declaration.header_templates.partials.modals')
@endsection

@push('scripts')
    @include('ex_declaration.header_templates.partials.scripts')
@endpush
