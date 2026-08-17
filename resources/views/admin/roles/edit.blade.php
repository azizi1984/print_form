@extends('layouts.app')

@section('page-title', 'Edit Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Role: {{ $role->name }}</h4>
                <p class="text-muted mb-0" style="font-size: 14px;">แก้ไขชื่อบทบาทและกำหนดสิทธิ์การใช้งาน (Edit role details & update permissions)</p>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Roles List
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> กรุณาตรวจสอบข้อผิดพลาด:
                <ul class="mb-0 mt-1 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Role Name (ชื่อบทบาท) <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg" value="{{ old('name', $role->name) }}" required>
                    </div>
                </div>
            </div>

            <!-- Assign Permissions Section -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-key-fill text-warning me-2"></i>Permissions Assignment
                        </h5>
                        <small class="text-muted">ปรับเปลี่ยนสิทธิ์การใช้งานสำหรับ Role นี้</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="toggleAllMaster">
                            <i class="bi bi-check2-all me-1"></i> Select / Deselect All
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(isset($groupedPermissions) && count($groupedPermissions) > 0)
                        <div class="row g-4">
                            @foreach($groupedPermissions as $groupName => $perms)
                                @php
                                    $groupPermNames = collect($perms)->pluck('name')->toArray();
                                    $allInGroupChecked = count(array_intersect($groupPermNames, $rolePermissions)) === count($groupPermNames);
                                @endphp
                                <div class="col-md-6 col-lg-6">
                                    <div class="card h-100 border bg-light-subtle shadow-sm" style="border-radius: 10px;">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 px-3">
                                            <span class="fw-bold text-primary" style="font-size: 15px;">
                                                <i class="bi bi-folder-symlink me-1"></i>{{ $groupName }}
                                            </span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input group-toggle" 
                                                       type="checkbox" 
                                                       id="group_toggle_{{ Str::slug($groupName) }}" 
                                                       data-group="{{ Str::slug($groupName) }}"
                                                       {{ $allInGroupChecked ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold" for="group_toggle_{{ Str::slug($groupName) }}">Select Group</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                @foreach($perms as $permission)
                                                    @php
                                                        $isChecked = in_array($permission->name, old('permissions', $rolePermissions));
                                                    @endphp
                                                    <div class="col-12">
                                                        <div class="form-check py-1 px-2 rounded hover-bg-white">
                                                            <input class="form-check-input perm-checkbox perm-group-{{ Str::slug($groupName) }}" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission->name }}" 
                                                                   id="perm_{{ $permission->id }}"
                                                                   {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="form-check-label cursor-pointer" for="perm_{{ $permission->id }}">
                                                                <code class="text-dark">{{ $permission->name }}</code>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">ยังไม่มีข้อมูล Permission ในระบบ สามารถเพิ่ม Permission ได้จากหน้าหลัก</p>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center border-top">
                    <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                        <i class="bi bi-save me-1"></i> Update Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Group level toggle
    const groupToggles = document.querySelectorAll('.group-toggle');
    groupToggles.forEach(toggle => {
        toggle.addEventListener('change', function () {
            const groupSlug = this.getAttribute('data-group');
            const checkboxes = document.querySelectorAll(`.perm-group-${groupSlug}`);
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Master toggle (Select / Deselect All)
    const toggleAllBtn = document.getElementById('toggleAllMaster');
    let masterState = false;
    toggleAllBtn.addEventListener('click', function () {
        masterState = !masterState;
        const allCheckboxes = document.querySelectorAll('.perm-checkbox');
        const allGroupToggles = document.querySelectorAll('.group-toggle');
        
        allCheckboxes.forEach(cb => cb.checked = masterState);
        allGroupToggles.forEach(gt => gt.checked = masterState);
    });
});
</script>
@endsection