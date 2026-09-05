@extends('layouts.app')

@section('page-title', 'User Management - Print Form')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="bi bi-people-fill me-2 text-primary"></i> User Management (จัดการผู้ใช้งานระบบ)
            </h5>
            <small class="text-muted">รายการผู้ใช้งานทั้งหมดในระบบและการจัดการสิทธิ์</small>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
            <i class="bi bi-person-plus me-1"></i> Create New User
        </a>
    </div>
    <div class="card-body">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table id="users_table" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No.</th>
                        <th style="width: 25%;">Name / Username</th>
                        <th style="width: 20%;">Email</th>
                        <th class="text-center" style="width: 15%;">Role</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 13%;">Created Date</th>
                        <th class="text-center" style="width: 12%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data populated dynamically via Server-Side DataTables AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        const initTable = () => {
            if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#users_table')) {
                    $('#users_table').DataTable().destroy();
                }

                $('#users_table').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    ajax: {
                        url: "{{ route('users.index') }}",
                        type: "GET",
                        error: function(xhr, error, code) {
                            console.error("DataTables Server-Side error: ", error);
                        }
                    },
                    columns: [
                        { data: 'no', name: 'id', className: 'text-center fw-semibold text-muted', orderable: false, searchable: false },
                        { data: 'name', name: 'name', orderable: true, searchable: true },
                        { data: 'email', name: 'email', orderable: true, searchable: true },
                        { data: 'role', name: 'role', className: 'text-center', orderable: false, searchable: true },
                        { data: 'status', name: 'status', className: 'text-center', orderable: true, searchable: true },
                        { data: 'created_at', name: 'created_at', className: 'text-center', orderable: true, searchable: true },
                        { data: 'action', name: 'action', className: 'text-center', orderable: false, searchable: false }
                    ],
                    order: [[5, 'desc']],
                    language: {
                        lengthMenu: "แสดง _MENU_ รายการ",
                        search: "ค้นหา:",
                        info: "แสดงข้อมูล _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                        infoEmpty: "แสดงข้อมูล 0 ถึง 0 จากทั้งหมด 0 รายการ",
                        infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                        zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                        processing: '<div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>กำลังโหลดข้อมูล...',
                        paginate: {
                            first: "«",
                            last: "»",
                            next: "›",
                            previous: "‹"
                        }
                    }
                });
            }
        };

        initTable();
        window.addEventListener('load', initTable);
    });
</script>
@endpush
