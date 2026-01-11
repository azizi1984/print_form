@extends('layouts.app')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Welcome to Dashboard</h5>
            </div>
            <div class="card-body">
                <p>นี่คือส่วนเนื้อหาหลัก (content) ของหน้า Dashboard ครับ</p>
                <p>ตัว Navbar หลักจะถูกดึงมาจาก <code>layouts/admin.blade.php</code> โดยอัตโนมัติ</p>
            </div>
        </div>
    </div>
</div>
@endsection
