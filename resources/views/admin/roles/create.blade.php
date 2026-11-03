@extends('layouts.app')

@section('page-title', 'Create New Role - สร้างบทบาทใหม่')

@push('styles')
    @include('admin.roles.partials.edit_styles')
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12">

        {{-- Top Header Row / Breadcrumbs --}}
        <div class="d-flex justify-content-between align-items-center role-page-header">
            <div class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.3px;">
                <i class="bi bi-shield-plus text-primary me-1"></i> สร้างบทบาทใหม่ (CREATE NEW ROLE)
            </div>
            <div class="role-breadcrumb">
                <a href="{{ route('roles.index') }}">Role And Permission</a>
                <span class="mx-1.5 text-muted">&gt;</span>
                <span class="text-secondary">สร้างบทบาทใหม่</span>
            </div>
        </div>

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

        <form action="{{ route('roles.store') }}" method="POST" id="roleCreateForm">
            @csrf

            {{-- Card 1: Role Information (ห้ามซ้ำในระบบ) --}}
            @include('admin.roles.partials.create_role_info')

            {{-- Card 2: Main Permissions Card Container (Menu-based Permission Catalog) --}}
            <div class="role-edit-card">
                {{-- Assign Permissions Header & Action Toolbar --}}
                @include('admin.roles.partials.create_permissions_header')

                {{-- Permission Groups Container (Tree Accordions) --}}
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

                    {{-- Footer Action Buttons (Cancel / Save Role) --}}
                    @include('admin.roles.partials.create_footer')
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
    @include('admin.roles.partials.create_scripts')
@endpush
