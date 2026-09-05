@extends('layouts.app')

@section('page-title', 'Roles & Permissions Management')

@section('content')
<!-- Header Title Banner -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>Roles & Permissions
        </h4>
        <p class="text-muted mb-0" style="font-size: 14px;">
            จัดการบทบาทหน้าที่และสิทธิ์การใช้งานในระบบ (Manage System Roles and Dynamic Permissions)
        </p>
    </div>
    <div>
        @if(($tab ?? 'roles') === 'permissions')
            <button type="button" class="btn btn-primary shadow-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="bi bi-plus-circle me-1"></i> Add Dynamic Permission
            </button>
        @else
            <a class="btn btn-primary shadow-sm rounded-pill px-3" href="{{ route('roles.create') }}">
                <i class="bi bi-plus-circle me-1"></i> Create New Role
            </a>
        @endif
    </div>
</div>

<!-- Alert Notifications -->
@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> เกิดข้อผิดพลาด กรุณาตรวจสอบข้อมูลที่กรอก
        <ul class="mb-0 mt-1 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Navigation Tabs -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-bottom-0 pt-3 px-3">
        <ul class="nav nav-tabs card-header-tabs border-0" id="rolePermissionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link fw-semibold px-4 py-2 {{ ($tab ?? 'roles') === 'roles' ? 'active border-bottom border-primary border-3 text-primary bg-transparent' : 'text-secondary' }}" 
                   href="{{ route('roles.index', ['tab' => 'roles']) }}">
                    <i class="bi bi-person-badge me-2"></i>Roles (บทบาท)
                    <span class="badge bg-primary-subtle text-primary rounded-pill ms-2">{{ $roles->count() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link fw-semibold px-4 py-2 {{ ($tab ?? 'roles') === 'permissions' ? 'active border-bottom border-primary border-3 text-primary bg-transparent' : 'text-secondary' }}" 
                   href="{{ route('roles.index', ['tab' => 'permissions']) }}">
                    <i class="bi bi-key me-2"></i>Permissions (สิทธิ์ในระบบ)
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2">{{ $permissions->count() }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">
        @if(($tab ?? 'roles') === 'roles')
            <!-- ROLES TAB CONTENT -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3" width="5%">#</th>
                            <th width="20%">Role Name</th>
                            <th>Assigned Permissions</th>
                            <th width="12%" class="text-center">Users</th>
                            <th width="15%" class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $key => $role)
                            <tr>
                                <td class="ps-4 text-muted fw-bold">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary-subtle text-primary fw-bold me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $role->name }}</div>
                                            <small class="text-muted">Guard: {{ $role->guard_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($role->permissions->count() > 0)
                                        <div class="d-flex flex-wrap gap-1" style="max-height: 80px; overflow-y: auto;">
                                            @foreach($role->permissions as $perm)
                                                <span class="badge bg-light text-dark border fw-normal" style="font-size: 11px;">
                                                    <i class="bi bi-check-circle-fill text-success me-1"></i>{{ $perm->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning" style="font-size: 11px;">
                                            No Permissions Assigned
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3 py-1">
                                        <i class="bi bi-people me-1"></i>{{ $role->users_count }} Users
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <a class="btn-action btn-action-edit" href="{{ route('roles.edit', $role->id) }}" title="Edit Role">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(!in_array($role->name, ['Super Admin']))
                                            <button type="button" class="btn-action btn-action-delete" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}" title="Delete Role">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Delete Role Modal -->
                                    @if(!in_array($role->name, ['Super Admin']))
                                        <div class="modal fade text-start" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-body text-center p-4">
                                                        <div class="text-danger mb-3">
                                                            <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <h5 class="fw-bold mb-2">ยืนยันการลบ Role?</h5>
                                                        <p class="text-muted small mb-4">คุณแน่ใจหรือว่าต้องการลบ Role <strong>"{{ $role->name }}"</strong>? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">ยกเลิก</button>
                                                                <button type="submit" class="btn btn-danger px-3">ยืนยันลบ</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-x text-secondary d-block mb-2" style="font-size: 2.5rem;"></i>
                                    ไม่พบข้อมูล Role ในระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <!-- PERMISSIONS TAB CONTENT -->
            <div class="p-3 bg-light border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-secondary">Dynamic Permissions Catalog</span>
                    <span class="badge bg-primary rounded-pill">{{ $permissions->count() }} Total</span>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Permission
                </button>
            </div>

            <div class="p-4">
                <div class="row g-4">
                    @forelse($groupedPermissions as $groupName => $groupPerms)
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border shadow-sm" style="border-radius: 10px;">
                                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-2 px-3">
                                    <span class="text-primary">
                                        <i class="bi bi-folder2-open me-2"></i>{{ $groupName }}
                                    </span>
                                    <span class="badge bg-secondary-subtle text-dark rounded-pill" style="font-size: 11px;">
                                        {{ count($groupPerms) }} item(s)
                                    </span>
                                </div>
                                <div class="card-body p-2">
                                    <div class="list-group list-group-flush">
                                        @foreach($groupPerms as $perm)
                                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 border-0 rounded hover-bg-light">
                                                <div>
                                                    <code class="text-dark fw-semibold" style="font-size: 13px;">{{ $perm->name }}</code>
                                                    <small class="text-muted d-block" style="font-size: 11px;">
                                                        Used in {{ $perm->roles_count }} role(s)
                                                    </small>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            class="btn btn-link btn-sm text-secondary p-1 edit-perm-btn"
                                                            data-id="{{ $perm->id }}"
                                                            data-name="{{ $perm->name }}"
                                                            data-guard="{{ $perm->guard_name }}"
                                                            title="Edit Permission">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-link btn-sm text-danger p-1" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deletePermModal{{ $perm->id }}"
                                                            title="Delete Permission">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Permission Modal -->
                                                <div class="modal fade text-start" id="deletePermModal{{ $perm->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                                        <div class="modal-content border-0 shadow">
                                                            <div class="modal-body text-center p-4">
                                                                <div class="text-danger mb-3">
                                                                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-2">ลบ Permission?</h6>
                                                                <p class="text-muted small mb-4">ลบสิทธิ์ <code>{{ $perm->name }}</code>? สิทธิ์นี้จะถูกถอดออกจาก Role ทั้งหมดทันที</p>
                                                                <form action="{{ route('permissions.destroy', $perm->id) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <div class="d-flex justify-content-center gap-2">
                                                                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                                                                        <button type="submit" class="btn btn-sm btn-danger px-3">ลบสิทธิ์นี้</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-key-fill text-secondary d-block mb-2" style="font-size: 2.5rem;"></i>
                            ยังไม่มีข้อมูล Permission ในระบบ
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Create New Permission -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="createPermissionModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add Dynamic Permission
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="perm_name" class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="perm_name" class="form-control" placeholder="Ex. users.create, templates.edit, etc." required>
                        <small class="text-muted mt-1 d-block">
                            แนะนำให้ใช้รูปแบบ <code>module.action</code> เพื่อให้ระบบจัดหมวดหมู่อัตโนมัติ (เช่น <code>reports.export</code>)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="perm_guard" class="form-label fw-semibold">Guard Name</label>
                        <input type="text" name="guard_name" id="perm_guard" class="form-control" value="web">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Permission -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="editPermissionModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Permission
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPermissionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_perm_name" class="form-label fw-semibold">Permission Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_perm_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_perm_guard" class="form-label fw-semibold">Guard Name</label>
                        <input type="text" name="guard_name" id="edit_perm_guard" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-perm-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editPermissionModal'));
    const editForm = document.getElementById('editPermissionForm');
    const editNameInput = document.getElementById('edit_perm_name');
    const editGuardInput = document.getElementById('edit_perm_guard');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const guard = this.getAttribute('data-guard');

            editForm.action = `/permissions/${id}`;
            editNameInput.value = name;
            editGuardInput.value = guard;

            editModal.show();
        });
    });
});
</script>
@endsection