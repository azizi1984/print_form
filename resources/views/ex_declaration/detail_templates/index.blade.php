<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-class') bg-light">
    <!-- Using include instead of extends for the topbar component -->
    @include('layouts.topbar')
    
    <div class="container-fluid px-4 mt-4 mb-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-arrow-up me-2"></i>Export Declaration Detail Templates</h5>
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createOrEditModal">
                    <i class="bi bi-plus-lg me-1"></i> Create
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="detail_declaration" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10%;">No.</th>
                                <th style="width: 25%;">Template Name</th>
                                <th style="width: 35%;">Description</th>
                                <th class="text-center" style="width: 15%;">Create Date</th>
                                <th class="text-center" style="width: 15%;">Status</th>
                                <th class="text-center" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailTemplates as $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $row['detail_template_name'] }}</td>
                                <td>{{ $row['description'] }}</td>
                                <td class="text-center">{{ $row['created_at'] }}</td>
                                <td class="text-center">
                                    @if($row['status'] == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center flex-nowrap gap-2">
                                        <a href="{{ route('detail-template.show', $row['detail_template_id']) }}" class="text-primary" title="Manage"><i class="bi bi-gear"></i></a>
                                        <a href="#" class="text-success copy-btn" title="Copy" data-id="{{ $row['detail_template_id'] ?? '' }}"><i class="bi bi-copy"></i></a>
                                        <a href="#" class="text-warning" title="Edit"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#createOrEditModal"
                                           data-id="{{ $row['detail_template_id'] ?? '' }}"
                                           data-name="{{ $row['detail_template_name'] ?? '' }}"
                                           data-desc="{{ $row['description'] ?? '' }}"
                                           data-status="{{ $row['status'] ?? '' }}"
                                           data-date="{{ $row['created_at'] ?? '' }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="#" class="text-danger delete-btn" title="Delete" data-id="{{ $row['detail_template_id'] ?? '' }}"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Modal -->
    <div class="modal fade" id="createOrEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editModalLabel"><i class="bi bi-journal-text me-2"></i>Create / Edit Detail Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createOrEditForm">
                        <input type="hidden" id="create_or_edit_detail_template_id" name="detail_template_id">
                        <input type="hidden" id="action_mode" name="action_mode" value="">
                        <div class="mb-3">
                            <label for="create_or_edit_detail_template_name" class="form-label">Detail Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_or_edit_detail_template_name" name="detail_template_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="create_or_edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="create_or_edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="create_or_edit_status" class="form-label">Status</label>
                                <select class="form-select" id="create_or_edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0" selected>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3" id="wrapper_created_at">
                                <label for="create_or_edit_created_at" class="form-label">Create Date</label>
                                <input type="text" class="form-control" id="create_or_edit_created_at" name="created_at" readonly disabled>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelcreateOrEdit">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSavecreateOrEdit">Save</button>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/ex_declaration/detail_script.js'])
</body>
</html>
