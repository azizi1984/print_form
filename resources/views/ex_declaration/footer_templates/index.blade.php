@extends('layouts.app')

@section('page-title', 'Export Declaration Footer Templates - Print Form')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-layout-text-window-reverse me-2 text-primary"></i> Export Declaration Footer Templates
            </h5>
            <small class="text-muted">จัดการและกำหนดรูปแบบเทมเพลตส่วนท้ายเอกสารและลายเซ็น</small>
        </div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#createOrEditModal">
            <i class="bi bi-plus-lg me-1"></i> Create Footer Template
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="footer_declaration" class="table table-hover align-middle" style="width:100%">
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
                    @foreach($footerTemplates as $row)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                        <td class="fw-semibold text-dark">{{ $row['footer_template_name'] }}</td>
                        <td class="text-secondary">{{ $row['description'] ?? '-' }}</td>
                        <td class="text-center text-muted" style="font-size: 13px;">{{ $row['created_at'] }}</td>
                        <td class="text-center">
                            @if($row['status'] == 1)
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
                                   data-id="{{ $row['footer_template_id'] ?? '' }}"
                                   data-name="{{ $row['footer_template_name'] ?? '' }}"
                                   data-desc="{{ $row['description'] ?? '' }}"
                                   data-status="{{ $row['status'] ?? '' }}"
                                   data-date="{{ $row['created_at'] ?? '' }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="#" class="btn-action btn-action-delete delete-btn" title="ลบ" data-id="{{ $row['footer_template_id'] ?? '' }}">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editModalLabel"><i class="bi bi-journal-text me-2 text-primary"></i>Create / Edit Footer Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createOrEditForm">
                    <input type="hidden" id="create_or_edit_footer_template_id" name="footer_template_id">
                    <input type="hidden" id="action_mode" name="action_mode" value="">
                    <div class="mb-3">
                        <label for="create_or_edit_footer_template_name" class="form-label fw-semibold">Footer Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_or_edit_footer_template_name" name="footer_template_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_or_edit_description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="create_or_edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_or_edit_status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="create_or_edit_status" name="status">
                                <option value="1">Active</option>
                                <option value="0" selected>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="wrapper_created_at">
                            <label for="create_or_edit_created_at" class="form-label fw-semibold">Create Date</label>
                            <input type="text" class="form-control bg-light" id="create_or_edit_created_at" name="created_at" readonly disabled>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" id="btnCancelcreateOrEdit">Cancel</button>
                <button type="button" class="btn btn-primary px-4" id="btnSavecreateOrEdit">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/ex_declaration/footer_script.js'])
@endpush
