@extends('layouts.app')

@section('page-title', 'Export Declaration Detail Templates - Print Form')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-table me-2 text-primary"></i> Export Declaration Detail Templates
            </h5>
            <small class="text-muted">จัดการและกำหนดรูปแบบเทมเพลตรายการสินค้าและตารางรายละเอียด</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createOrEditModal">
            <i class="bi bi-plus-lg me-1"></i> Create Detail Template
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="detail_declaration" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 8%;">No.</th>
                        <th style="width: 25%;">Template Name</th>
                        <th style="width: 35%;">Description</th>
                        <th class="text-center" style="width: 15%;">Create Date</th>
                        <th class="text-center" style="width: 12%;">Status</th>
                        <th class="text-center" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detailTemplates as $row)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $row->detail_template_name ?? $row['detail_template_name'] }}</span>
                        </td>
                        <td class="text-secondary">
                            @if(!empty($row->description ?? $row['description']))
                                <span class="d-inline-block text-truncate" style="max-width: 320px;" title="{{ $row->description ?? $row['description'] }}">
                                    {{ $row->description ?? $row['description'] }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center text-muted" style="font-size: 13px;">
                            {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            @if(($row->status ?? $row['status']) == 1)
                                <span class="badge badge-modern badge-success-modern"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                            @else
                                <span class="badge badge-modern badge-danger-modern"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-1">
                                <a href="{{ route('detail-template.show', $row->detail_template_id ?? $row['detail_template_id']) }}" class="btn-action btn-action-view" title="จัดการ Layout">
                                    <i class="bi bi-layout-text-window"></i>
                                </a>
                                <a href="#" class="btn-action btn-action-copy copy-btn" title="คัดลอกเทมเพลต" data-id="{{ $row->detail_template_id ?? $row['detail_template_id'] }}">
                                    <i class="bi bi-copy"></i>
                                </a>
                                <a href="#" class="btn-action btn-action-edit" title="แก้ไขข้อมูล"
                                   data-bs-toggle="modal" 
                                   data-bs-target="#createOrEditModal"
                                   data-id="{{ $row->detail_template_id ?? $row['detail_template_id'] }}"
                                   data-name="{{ $row->detail_template_name ?? $row['detail_template_name'] }}"
                                   data-desc="{{ $row->description ?? $row['description'] ?? '' }}"
                                   data-status="{{ $row->status ?? $row['status'] }}"
                                   data-date="{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '' }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn-action btn-action-delete delete-btn" title="ลบ" data-id="{{ $row->detail_template_id ?? $row['detail_template_id'] }}">
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
                        <i class="bi bi-table"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center" id="editModalLabel">
                            Create Detail Template
                        </h5>
                        <small class="text-muted" id="editModalSubtitle">
                            กำหนดชื่อและรายละเอียดเทมเพลตตารางรายการสินค้า (Detail Template) สำหรับใบขนสินค้าขาออก
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
                    <input type="hidden" id="create_or_edit_detail_template_id" name="detail_template_id">
                    <input type="hidden" id="action_mode" name="action_mode" value="create">

                    <!-- Template Name Field -->
                    <div class="mb-3">
                        <label for="create_or_edit_detail_template_name" class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-tag text-primary me-1"></i> Detail Template Name <span class="text-danger">*</span></span>
                            <span class="text-muted fw-normal" style="font-size: 12px;">จำเป็นต้องระบุ</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-fonts"></i></span>
                            <input type="text" class="form-control border-start-0 ps-1" id="create_or_edit_detail_template_name" name="detail_template_name" placeholder="เช่น Standard Export Items Grid Template" required autocomplete="off">
                        </div>
                        <div class="form-text text-muted" style="font-size: 12px;">
                            <i class="bi bi-info-circle me-1"></i>กำหนดชื่อเทมเพลตเพื่อใช้อ้างอิงและจับคู่กับ Profile Template
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="create_or_edit_description" class="form-label fw-semibold text-dark">
                            <i class="bi bi-card-text text-secondary me-1"></i> Description / คำอธิบายเพิ่มเติม
                        </label>
                        <textarea class="form-control" id="create_or_edit_description" name="description" rows="3" placeholder="ระบุวัตถุประสงค์หรือรายละเอียดเพิ่มเติมของเทมเพลตนี้..."></textarea>
                        <div class="form-text text-muted" style="font-size: 12px;">
                            <i class="bi bi-pencil me-1"></i>ระบุข้อมูลสั้น ๆ เพื่อช่วยจดจำวัตถุประสงค์หรือประเภทสินค้าของเทมเพลต
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
                                <i class="bi bi-check2 me-1"></i>เฉพาะเทมเพลตที่ Active เท่านั้นที่สามารถนำไปเลือกใช้งานใน Profile ได้
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
                    <span>พร้อมรองรับระบบ Visual Layout Designer</span>
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
    @vite(['resources/js/ex_declaration/detail_script.js'])
@endpush
