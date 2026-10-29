@extends('layouts.app')

@section('page-title', 'กำหนดสิทธิ์: ' . $role->name)

@push('styles')
    @include('admin.roles.partials.edit_styles')
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Form Validation Alert --}}
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> กรุณาตรวจสอบข้อผิดพลาด:
                <ul class="mb-0 mt-1 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('roles.update', $role->id) }}" method="POST" id="roleEditForm">
            @csrf
            @method('PUT')
            
            {{-- Hidden input for Role Name --}}
            <input type="hidden" name="name" value="{{ old('name', $role->name) }}" id="hiddenRoleNameInput">

            {{-- Main Permissions Card Container --}}
            <div class="role-edit-card">
                {{-- Header with Title & Action Controls --}}
                @include('admin.roles.partials.edit_header', ['role' => $role])

                {{-- Permission Groups Container --}}
                <div class="groups-container">
                    @if(isset($permissionTree) && count($permissionTree) > 0)
                        @foreach($permissionTree as $group)
                            @include('admin.roles.partials.group_accordion', ['group' => $group])
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                            <p class="mb-0">ยังไม่มีข้อมูล Permission ในระบบ สามารถเพิ่มสิทธิ์ใหม่ได้จากหน้าหลัก Roles & Permissions</p>
                        </div>
                    @endif

                    {{-- Footer Action Buttons (Cancel / Save Changes) --}}
                    @include('admin.roles.partials.edit_footer')
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
    @include('admin.roles.partials.edit_scripts')
@endpush
