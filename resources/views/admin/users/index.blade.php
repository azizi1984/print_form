<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-class') bg-light">
    @include('layouts.topbar')

    <div class="container-fluid px-4 mt-4 mb-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-people me-2 text-primary"></i>User Management (จัดการผู้ใช้งานระบบ)
                    </h5>
                    <small class="text-muted">รายการผู้ใช้งานทั้งหมดในระบบและการจัดการสิทธิ์</small>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="bi bi-person-plus me-1"></i> Create New User
                </a>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show m-2 mb-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-2 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="users_table" class="table table-striped table-bordered table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
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
    </div>

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
                            { data: 'no', name: 'id', className: 'text-center fw-semibold', orderable: false, searchable: false },
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
                            search: "ค้นหาข้อมูลทุกฟิลด์ (Search):",
                            info: "แสดงข้อมูล _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                            infoEmpty: "แสดงข้อมูล 0 ถึง 0 จากทั้งหมด 0 รายการ",
                            infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                            zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                            processing: '<div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>กำลังโหลดข้อมูล...',
                            paginate: {
                                first: "หน้าแรก",
                                last: "หน้าสุดท้าย",
                                next: "ถัดไป",
                                previous: "ก่อนหน้า"
                            }
                        }
                    });
                }
            };

            initTable();
            window.addEventListener('load', initTable);
        });
    </script>
</body>
</html>
