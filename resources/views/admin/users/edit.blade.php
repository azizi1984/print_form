@extends('layouts.app')

@section('page-title', 'Edit User: ' . $user->name . ' - Print Form')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-3">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User Profile (แก้ไขข้อมูลผู้ใช้งาน)
                        </h5>
                        <small class="text-muted">User ID: #{{ $user->id }} | Profile: {{ $user->profile_id ?? '-' }} | Username: {{ $user->username ?? '-' }}</small>
                    </div>
                </div>
                <div>
                    @if(($user->status ?? 1) == 1)
                        <span class="badge badge-modern badge-success-modern"><i class="bi bi-check-circle-fill me-1"></i>Active</span>
                    @else
                        <span class="badge badge-modern badge-danger-modern"><i class="bi bi-dash-circle-fill me-1"></i>Inactive</span>
                    @endif
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

                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- ข้อมูลส่วนตัว -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label fw-bold">First Name (ชื่อจริง) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('firstname') is-invalid @enderror" id="firstname" name="firstname" value="{{ old('firstname', $user->firstname ?: $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label fw-bold">Last Name (นามสกุล)</label>
                            <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" name="lastname" value="{{ old('lastname', $user->lastname) }}">
                        </div>
                    </div>

                    <!-- ข้อมูลบัญชีเข้าสู่ระบบ -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-bold">Username (ชื่อผู้ใช้งานสำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" maxlength="25" required>
                        </div>
                        <div class="col-md-6">
                            <label for="profile_id" class="form-label fw-bold">Profile ID (รหัสโปรไฟล์สำหรับ Login) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase @error('profile_id') is-invalid @enderror" id="profile_id" name="profile_id" list="profile_id_options" value="{{ old('profile_id', $user->profile_id ?? 'ZZ00') }}" maxlength="5" required>
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
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-bold">User Role (สิทธิ์การใช้งาน)</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                <option value="">-- Select Role --</option>
                                @if(isset($roles))
                                    @foreach($roles as $roleName => $roleLabel)
                                        <option value="{{ $roleName }}" {{ old('role', $userRole) == $roleName ? 'selected' : '' }}>{{ $roleLabel }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- ข้อมูลบริษัทและสถานะ -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="comp_tax" class="form-label fw-bold">Company Tax ID (เลขประจำตัวผู้เสียภาษีอากร)</label>
                            <input type="text" class="form-control @error('comp_tax') is-invalid @enderror" id="comp_tax" name="comp_tax" value="{{ old('comp_tax', $user->comp_tax) }}" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold">Status (สถานะการใช้งาน)</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" {{ (auth()->check() && (int) auth()->id() === (int) $user->id) ? 'disabled' : '' }}>
                                <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>Active (เปิดใช้งาน)</option>
                                <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>Inactive (ปิดใช้งาน)</option>
                            </select>
                            @if(auth()->check() && (int) auth()->id() === (int) $user->id)
                                <input type="hidden" name="status" value="1">
                                <small class="text-muted">ไม่สามารถปิดใช้งานบัญชีที่กำลังล็อกอินอยู่ได้</small>
                            @endif
                        </div>
                    </div>

                    <!-- ข้อมูล LSP เพิ่มเติม -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="lsp" class="form-label fw-bold">LSP Code</label>
                            <input type="text" class="form-control @error('lsp') is-invalid @enderror" id="lsp" name="lsp" value="{{ old('lsp', $user->lsp) }}" maxlength="2">
                        </div>
                        <div class="col-md-4">
                            <label for="lsp_tax_no" class="form-label fw-bold">LSP Tax No.</label>
                            <input type="text" class="form-control @error('lsp_tax_no') is-invalid @enderror" id="lsp_tax_no" name="lsp_tax_no" value="{{ old('lsp_tax_no', $user->lsp_tax_no) }}" maxlength="13">
                        </div>
                        <div class="col-md-5">
                            <label for="lsp_comp_nmt" class="form-label fw-bold">LSP Company Name</label>
                            <input type="text" class="form-control @error('lsp_comp_nmt') is-invalid @enderror" id="lsp_comp_nmt" name="lsp_comp_nmt" value="{{ old('lsp_comp_nmt', $user->lsp_comp_nmt) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="remark" class="form-label fw-bold">Remark (หมายเหตุเพิ่มเติม)</label>
                        <textarea class="form-control @error('remark') is-invalid @enderror" id="remark" name="remark" rows="2" maxlength="255">{{ old('remark', $user->remark) }}</textarea>
                    </div>

                    <!-- Password Section -->
                    <div class="p-3 bg-light border rounded-3 mb-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-key me-1 text-primary"></i> Change Password (เปลี่ยนรหัสผ่าน)</h6>
                        <small class="text-muted d-block mb-3">หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างช่องนี้ไว้ (Leave blank if you do not want to change password)</small>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">New Password (รหัสผ่านใหม่)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min. 6 characters">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm New Password (ยืนยันรหัสผ่านใหม่)</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('users.index') }}" class="btn btn-light px-4 rounded-pill">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 shadow-sm text-white rounded-pill">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
