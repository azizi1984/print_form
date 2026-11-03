{{-- Role Information Card --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-12">
                <label for="roleNameInput" class="form-label fw-bold text-dark fs-6 mb-1">
                    Role Name (ชื่อบทบาท) <span class="text-danger">*</span>
                </label>
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input type="text" 
                               name="name" 
                               id="roleNameInput" 
                               class="form-control form-control-lg border-start-0 @error('name') is-invalid @enderror" 
                               placeholder="เช่น Admin, Manager, Editor, Accountant, Supervisor" 
                               value="{{ old('name') }}" 
                               required 
                               autocomplete="off">
                    </div>
                    <div id="roleNameFeedback" class="mt-2" style="font-size: 13px;">
                        @error('name')
                            <span class="text-danger fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>{{ $message }}</span>
                        @else
                            <span class="text-muted"><i class="bi bi-info-circle me-1"></i>กรุณากรอกชื่อบทบาท (ระบบตรวจสอบชื่อซ้ำแบบ Real-time)</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-12 mt-3 mt-lg-0">
                <div class="alert alert-light border mb-0 py-2.5 px-3 rounded-3 shadow-2xs">
                    <div class="fw-bold text-dark small mb-1">
                        <i class="bi bi-shield-exclamation text-warning me-1"></i> เงื่อนไขชื่อบทบาท (Role Name Constraint):
                    </div>
                    <ul class="mb-0 ps-3 small text-muted">
                        <li><strong>ห้ามสร้างชื่อซ้ำ</strong> กับบทบาทที่มีอยู่แล้วในระบบ</li>
                        <li>ระบบจะตรวจสอบความซ้ำซ้อนทันทีขณะพิมพ์ (Real-time Validation)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
