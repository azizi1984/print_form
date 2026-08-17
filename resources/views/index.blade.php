@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4">
    <!-- Welcome Header -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white overflow-hidden position-relative">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-2">ยินดีต้อนรับสู่ระบบ Print Form</h2>
                        <p class="mb-0 text-white-50 fs-6">ระบบจัดการและออกแบบเทมเพลตเอกสารการส่งออกและฟอร์มต่างๆ</p>
                    </div>
                    <div class="d-none d-md-block opacity-25">
                        <i class="bi bi-printer" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3">
                        <i class="bi bi-file-earmark-text fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Header Template</h5>
                        <small class="text-muted">เทมเพลตส่วนหัวเอกสาร</small>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">จัดการและกำหนดรูปแบบ Layout ส่วนหัวเอกสารพร้อมตารางและกล่องข้อความ</p>
                <a href="{{ route('header-template') }}" class="btn btn-outline-primary rounded-pill mt-2">
                    <i class="bi bi-arrow-right me-1"></i> เข้าสู่หน้าจัดการ
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 bg-success-subtle text-success p-3 me-3">
                        <i class="bi bi-table fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Detail Template</h5>
                        <small class="text-muted">เทมเพลตรายการสินค้า/รายละเอียด</small>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">กำหนดคอลัมน์ ความกว้าง และการแสดงผลข้อมูลรายการในตาราง</p>
                <a href="{{ route('detail-template') }}" class="btn btn-outline-success rounded-pill mt-2">
                    <i class="bi bi-arrow-right me-1"></i> เข้าสู่หน้าจัดการ
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 bg-info-subtle text-info p-3 me-3">
                        <i class="bi bi-layout-text-window-reverse fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Footer Template</h5>
                        <small class="text-muted">เทมเพลตส่วนท้ายเอกสาร</small>
                    </div>
                </div>
                <p class="text-muted flex-grow-1">จัดการข้อมูลสรุปยอดและลายเซ็นต์ส่วนท้ายเอกสาร</p>
                <a href="{{ route('footer-template') }}" class="btn btn-outline-info rounded-pill mt-2">
                    <i class="bi bi-arrow-right me-1"></i> เข้าสู่หน้าจัดการ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection