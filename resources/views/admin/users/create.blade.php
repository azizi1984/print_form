@extends('layouts.app')

@section('page-title', 'Create New User - Print Form')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-3">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-person-plus-fill me-2 text-primary"></i>Create New User (สร้างผู้ใช้งานใหม่)
                        </h5>
                        <small class="text-muted">กรอกข้อมูลผู้ใช้งานและกำหนดสิทธิ์การเข้าถึงระบบ</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <strong><i class="bi bi-exclamation-triangle me-1"></i> กรุณาตรวจสอบข้อมูล:</strong>
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- ข้อมูลส่วนตัว -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label fw-bold">First Name (ชื่อจริง) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" name="firstname" value="{{ old('firstname') }}" placeholder="Ex. Somchai" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label fw-bold">Last Name (นามสกุล)</label>
                            <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" name="lastname" value="{{ old('lastname') }}" placeholder="Ex. Srisuk">
                        </div>
                    </div>

                    <!-- ข้อมูลบัญชีเข้าสู่ระบบ -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-bold">Username (ชื่อผู้ใช้งานสำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" maxlength="25" placeholder="Ex. somchai_s" required>
                        </div>
                        <div class="col-md-6">
                            <label for="profile_id" class="form-label fw-bold">Profile ID (รหัสโปรไฟล์สำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase @error('profile_id') is-invalid @enderror" id="profile_id" name="profile_id" list="profile_id_options" value="{{ old('profile_id', Auth::user()->profile_id ?? 'ZZ00') }}" maxlength="5" placeholder="Ex. ZZ00" required>
                            <datalist id="profile_id_options">
                                <option value="ZZ00">ZZ00 - ส่วนกลาง (Central Profile)</option>
                                @if(isset($profiles))
                                    @foreach($profiles as $profile)
                                        @if($profile->profile_id !== 'ZZ00')
                                            <option value="{{ $profile->profile_id }}">{{ $profile->profile_id }}{{ $profile->profile_tax ? ' - Tax: ' . $profile->profile_tax : '' }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </datalist>
                        </div>
                    </div>

                    <!-- อีเมลและบทบาท -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email Address (อีเมล)</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Ex. user@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-bold">User Role (สิทธิ์การใช้งาน)</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                <option value="">-- Select Role --</option>
                                @if(isset($roles))
                                    @foreach($roles as $roleName => $roleLabel)
                                        <option value="{{ $roleName }}" {{ old('role') == $roleName ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- รหัสผ่าน -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold">Password (รหัสผ่าน) <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="At least 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm Password (ยืนยันรหัสผ่าน) <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <!-- ข้อมูลบริษัทและสถานะ -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="comp_tax" class="form-label fw-bold">Company Tax ID (เลขประจำตัวผู้เสียภาษีอากร)</label>
                            <input type="text" class="form-control @error('comp_tax') is-invalid @enderror" id="comp_tax" name="comp_tax" value="{{ old('comp_tax') }}" maxlength="15" placeholder="Ex. 0105550000000">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Status (สถานะการใช้งาน)</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active (เปิดใช้งาน)</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive (ปิดใช้งาน)</option>
                            </select>
                        </div>
                    </div>

                    <!-- ข้อมูล LSP เพิ่มเติม -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="lsp" class="form-label fw-bold">LSP Code</label>
                            <input type="text" class="form-control @error('lsp') is-invalid @enderror" id="lsp" name="lsp" value="{{ old('lsp') }}" maxlength="2" placeholder="Ex. 01">
                        </div>
                        <div class="col-md-4">
                            <label for="lsp_tax_no" class="form-label fw-bold">LSP Tax No.</label>
                            <input type="text" class="form-control @error('lsp_tax_no') is-invalid @enderror" id="lsp_tax_no" name="lsp_tax_no" value="{{ old('lsp_tax_no') }}" maxlength="13" placeholder="Ex. 0105550000000">
                        </div>
                        <div class="col-md-5">
                            <label for="lsp_comp_nmt" class="form-label fw-bold">LSP Company Name</label>
                            <input type="text" class="form-control @error('lsp_comp_nmt') is-invalid @enderror" id="lsp_comp_nmt" name="lsp_comp_nmt" value="{{ old('lsp_comp_nmt') }}" placeholder="ชื่อบริษัทตัวแทนออกของ">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="remark" class="form-label fw-bold">Remark (หมายเหตุเพิ่มเติม)</label>
                        <textarea class="form-control @error('remark') is-invalid @enderror" id="remark" name="remark" rows="2" maxlength="255" placeholder="Note or remarks...">{{ old('remark') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4 rounded-pill">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                            <i class="bi bi-save me-1"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
