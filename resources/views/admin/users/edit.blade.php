<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User: {{ $user->name }} - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-class') bg-light">
    @include('layouts.topbar')

    <div class="container-fluid px-4 mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="bi bi-pencil-square me-2 text-warning"></i>Edit User Profile (แก้ไขข้อมูลผู้ใช้งาน)
                                </h5>
                                <small class="text-muted">User ID: #{{ $user->id }} | {{ $user->email }}</small>
                            </div>
                        </div>
                        <div>
                            @if(($user->status ?? 1) == 1)
                                <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Active</span>
                            @else
                                <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <strong><i class="bi bi-exclamation-triangle me-1"></i> กรุณาตรวจสอบข้อมูล:</strong>
                                <ul class="mb-0 mt-2">
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

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold">Full Name (ชื่อ-นามสกุล) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label fw-bold">Username (ชื่อผู้ใช้งานในระบบ)</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email Address (อีเมล) <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
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

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="comp_tax" class="form-label fw-bold">Company Tax ID (เลขประจำตัวผู้เสียภาษีอากร)</label>
                                    <input type="text" class="form-control @error('comp_tax') is-invalid @enderror" id="comp_tax" name="comp_tax" value="{{ old('comp_tax', $user->comp_tax) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status (สถานะการใช้งาน)</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="1" {{ old('status', $user->status ?? 1) == 1 ? 'selected' : '' }}>Active (เปิดใช้งาน)</option>
                                        <option value="0" {{ old('status', $user->status ?? 1) == 0 ? 'selected' : '' }}>Inactive (ปิดใช้งาน)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="remark" class="form-label fw-bold">Remark (หมายเหตุเพิ่มเติม)</label>
                                <textarea class="form-control" id="remark" name="remark" rows="2">{{ old('remark', $user->remark) }}</textarea>
                            </div>

                            <!-- Password Section -->
                            <div class="p-3 bg-light border rounded mb-4">
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
                                <a href="{{ route('users.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-warning px-4 shadow-sm text-white">
                                    <i class="bi bi-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
