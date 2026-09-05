@extends('layouts.app')

@section('page-title', 'Dashboard - Print Form')

@section('content')
<div class="row g-4">
    <!-- Welcome Header -->
    <div class="col-12">
        <div class="card border-0 rounded-4 text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #3730a3 100%); box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);">
            <div class="card-body p-4 p-md-5 position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 px-3 py-2 rounded-pill mb-3" style="font-size: 12px; letter-spacing: 0.05em;">
                            <i class="bi bi-stars me-1 text-warning"></i> ระบบจัดการเทมเพลตฟอร์ม
                        </span>
                        <h2 class="fw-bold mb-2 text-white">ยินดีต้อนรับสู่ระบบ Print Form</h2>
                        <p class="mb-0 text-white-50 fs-6" style="max-width: 600px;">
                            ระบบจัดการและออกแบบเทมเพลตเอกสารการส่งออกและฟอร์มต่างๆ จัดการส่วนหัว รายการสินค้า และส่วนท้ายเอกสารได้อย่างสะดวกและมีประสิทธิภาพ
                        </p>
                    </div>
                    <div class="d-none d-lg-block opacity-20 me-4">
                        <i class="bi bi-printer" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
            <!-- Decorative circle glow -->
            <div class="position-absolute top-0 end-0 rounded-circle" style="width: 320px; height: 320px; background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%); transform: translate(30%, -30%); z-index: 1;"></div>
        </div>
    </div>
</div>
@endsection