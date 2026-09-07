@extends('layouts.app')

@section('page-title', 'Export Declaration Profiles - Print Form')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-file-earmark-person me-2 text-primary"></i> Export Declaration Profiles
            </h5>
            <small class="text-muted">จัดการโปรไฟล์เอกสารใบขนสินค้าขาออกและการเชื่อมโยง Header, Detail, Footer Template</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createOrEditModal">
            <i class="bi bi-plus-lg me-1"></i> Create Profile Template
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="ex_declaration" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No.</th>
                        <th style="width: 22%;">Profile Template Name</th>
                        <th style="width: 15%;">Header Template</th>
                        <th style="width: 15%;">Detail Template</th>
                        <th style="width: 15%;">Footer Template</th>
                        <th class="text-center" style="width: 10%;">Create Date</th>
                        <th class="text-center" style="width: 8%;">Status</th>
                        <th class="text-center" style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profileTemplates as $row)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $row->profile_template_name }}</div>
                            @if(!empty($row->description))
                                <small class="text-secondary text-truncate d-block" style="max-width: 260px;" title="{{ $row->description }}">{{ $row->description }}</small>
                            @endif
                        </td>
                        <td>
                            @if($row->headerTemplate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-layout-text-window me-1"></i>{{ $row->headerTemplate->header_template_name }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->detailTemplate)
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-table me-1"></i>{{ $row->detailTemplate->detail_template_name }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->footerTemplate)
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-layout-text-sidebar-reverse me-1"></i>{{ $row->footerTemplate->footer_template_name }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center text-muted" style="font-size: 13px;">
                            {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            @if($row->status == 1)
                                <span class="badge badge-modern badge-success-modern"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                            @else
                                <span class="badge badge-modern badge-danger-modern"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-1">
                                <a href="#" class="btn-action btn-action-edit" title="แก้ไขข้อมูล"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#createOrEditModal"
                                   data-id="{{ $row->profile_template_id }}"
                                   data-name="{{ $row->profile_template_name }}"
                                   data-desc="{{ $row->description ?? '' }}"
                                   data-status="{{ $row->status }}"
                                   data-header-id="{{ $row->header_template_id ?? '' }}"
                                   data-detail-id="{{ $row->detail_template_id ?? '' }}"
                                   data-footer-id="{{ $row->footer_template_id ?? '' }}"
                                   data-date="{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '' }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn-action btn-action-delete delete-btn" title="ลบ" data-id="{{ $row->profile_template_id }}">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Manage Modal -->
<div class="modal fade" id="createOrEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-bottom py-3 px-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px; font-size: 20px;">
                        <i class="bi bi-file-earmark-person"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center" id="editModalLabel">
                            Create Profile Template
                        </h5>
                        <small class="text-muted" id="editModalSubtitle">
                            กำหนดชื่อและเชื่อมโยงเทมเพลตส่วนหัว รายละเอียด และส่วนท้ายสำหรับใบขนสินค้าขาออก
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- In-modal Error Alert -->
                <div class="alert alert-danger d-none py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2" id="modalErrorAlert">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-5 flex-shrink-0"></i>
                    <span id="modalErrorMessage" class="fw-medium"></span>
                </div>

                <!-- Form -->
                <form id="createOrEditForm">
                    <input type="hidden" id="create_or_edit_profile_template_id" name="profile_template_id">
                    <input type="hidden" id="action_mode" name="action_mode" value="create">
                    
                    <!-- Template Name Field -->
                    <div class="mb-3">
                        <label for="create_or_edit_profile_template_name" class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-tag text-primary me-1"></i> Profile Template Name <span class="text-danger">*</span></span>
                            <span class="text-muted fw-normal" style="font-size: 12px;">จำเป็นต้องระบุ (ห้ามซ้ำใน Profile)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-fonts"></i></span>
                            <input type="text" class="form-control border-start-0 ps-1" id="create_or_edit_profile_template_name" name="profile_template_name" placeholder="เช่น Standard Export Declaration Profile" required autocomplete="off">
                        </div>
                        <div class="form-text text-muted" style="font-size: 12px;">
                            <i class="bi bi-info-circle me-1"></i>กำหนดชื่อ Profile Template เพื่อใช้ในการพิมพ์ใบขนสินค้า (ชื่อต้องไม่ซ้ำกันภายใน Profile เดียวกัน)
                        </div>
                    </div>

                    <!-- Template Mapping Section -->
                    <div class="p-3 bg-light-subtle rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark d-flex align-items-center small">
                                <i class="bi bi-diagram-3-fill text-primary me-2"></i>Template Mapping (ผูกความสัมพันธ์เทมเพลต)
                            </span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 11px;">
                                <i class="bi bi-shield-check me-1"></i>Same Profile ID
                            </span>
                        </div>
                        <div class="form-text text-muted mb-3" style="font-size: 12px;">
                            <i class="bi bi-info-circle me-1"></i>เลือกเทมเพลตที่มีสถานะ Active ใน Profile เดียวกันเพื่อนำมาประกอบเป็นเอกสารฉบับสมบูรณ์
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="create_or_edit_header_template_id" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-layout-text-window text-primary me-1"></i>Header Template
                                </label>
                                <select class="form-select" id="create_or_edit_header_template_id" name="header_template_id">
                                    <option value="">-- ไม่ระบุ (None) --</option>
                                    @foreach($headerTemplates as $ht)
                                        <option value="{{ $ht->header_template_id }}">{{ $ht->header_template_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted" style="font-size: 11px;">ส่วนหัวเอกสารใบขนสินค้า</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_or_edit_detail_template_id" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-table text-info me-1"></i>Detail Template
                                </label>
                                <select class="form-select" id="create_or_edit_detail_template_id" name="detail_template_id">
                                    <option value="">-- ไม่ระบุ (None) --</option>
                                    @foreach($detailTemplates as $dt)
                                        <option value="{{ $dt->detail_template_id }}">{{ $dt->detail_template_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted" style="font-size: 11px;">ตารางรายการสินค้า</div>
                            </div>
                            <div class="col-md-4">
                                <label for="create_or_edit_footer_template_id" class="form-label fw-semibold small text-dark">
                                    <i class="bi bi-layout-text-sidebar-reverse text-secondary me-1"></i>Footer Template
                                </label>
                                <select class="form-select" id="create_or_edit_footer_template_id" name="footer_template_id">
                                    <option value="">-- ไม่ระบุ (None) --</option>
                                    @foreach($footerTemplates as $ft)
                                        <option value="{{ $ft->footer_template_id }}">{{ $ft->footer_template_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted" style="font-size: 11px;">ส่วนท้ายและลายมือชื่อ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="create_or_edit_description" class="form-label fw-semibold text-dark">
                            <i class="bi bi-card-text text-secondary me-1"></i> Description / คำอธิบายเพิ่มเติม
                        </label>
                        <textarea class="form-control" id="create_or_edit_description" name="description" rows="3" placeholder="ระบุวัตถุประสงค์หรือรายละเอียดเพิ่มเติมของโปรไฟล์นี้..."></textarea>
                        <div class="form-text text-muted" style="font-size: 12px;">
                            <i class="bi bi-pencil me-1"></i>ระบุข้อมูลสั้น ๆ เพื่อช่วยจดจำวัตถุประสงค์หรือประเภทเอกสาร
                        </div>
                    </div>

                    <!-- Status & Metadata Section -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create_or_edit_status" class="form-label fw-semibold text-dark">
                                <i class="bi bi-toggle2-on text-success me-1"></i> Status / สถานะการใช้งาน
                            </label>
                            <select class="form-select" id="create_or_edit_status" name="status">
                                <option value="1" selected>🟢 Active (เปิดใช้งาน)</option>
                                <option value="0">⚪ Inactive (ปิดการใช้งาน)</option>
                            </select>
                            <div class="form-text text-muted" style="font-size: 12px;">
                                <i class="bi bi-check2 me-1"></i>เฉพาะโปรไฟล์ที่ Active เท่านั้นที่สามารถนำไปใช้พิมพ์ใบขนสินค้าได้
                            </div>
                        </div>
                        <div class="col-md-6" id="wrapper_created_at" style="display: none;">
                            <label for="create_or_edit_created_at" class="form-label fw-semibold text-dark">
                                <i class="bi bi-calendar3 text-muted me-1"></i> Create Date / วันที่สร้าง
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-clock-history"></i></span>
                                <input type="text" class="form-control border-start-0 ps-1 bg-light text-muted" id="create_or_edit_created_at" name="created_at" readonly disabled>
                            </div>
                            <div class="form-text text-muted" style="font-size: 12px;">
                                <i class="bi bi-lock me-1"></i>บันทึกวันที่สร้างโดยอัตโนมัติจากระบบ
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light-subtle border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small d-none d-sm-flex align-items-center">
                    <i class="bi bi-shield-check text-primary me-1"></i>
                    <span>ระบบตรวจสอบชื่อ Profile ซ้ำอัตโนมัติ</span>
                </div>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" data-bs-dismiss="modal" id="btnCancelcreateOrEdit">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" id="btnSavecreateOrEdit">
                        <i class="bi bi-check2-circle me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/ex_declaration/script.js'])
@endpush