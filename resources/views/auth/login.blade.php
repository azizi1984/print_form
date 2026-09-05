<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Print Form') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center min-vh-100 bg-body-modern py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card border-0 rounded-4 overflow-hidden" style="box-shadow: 0 20px 35px -5px rgba(15, 23, 42, 0.1), 0 10px 15px -5px rgba(15, 23, 42, 0.05);">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Left Branding Panel -->
                            <div class="col-md-5 d-none d-md-flex flex-column justify-content-between p-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #4338ca 100%);">
                                <div class="position-relative" style="z-index: 2;">
                                    <div class="d-flex align-items-center gap-2 mb-4">
                                        <div class="logo-badge">
                                            <i class="bi bi-printer-fill"></i>
                                        </div>
                                        <span class="fw-bold fs-5 tracking-wide">PRINT <span style="color: #818cf8 !important;">FORM</span></span>
                                    </div>
                                    <h4 class="fw-bold mb-2">ระบบจัดการแบบฟอร์ม</h4>
                                    <p class="text-white-50 small mb-0">ออกแบบและกำหนดรูปแบบเทมเพลตใบขนสินค้าขาออกอย่างมืออาชีพ</p>
                                </div>

                                <div class="text-white-50 small position-relative" style="z-index: 2;">
                                    &copy; {{ date('Y') }} Print Form System.
                                </div>

                                <!-- Decorative glow -->
                                <div class="position-absolute bottom-0 start-0 rounded-circle" style="width: 250px; height: 250px; background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%); transform: translate(-30%, 30%); z-index: 1;"></div>
                            </div>

                            <!-- Right Form Panel -->
                            <div class="col-md-7 p-4 p-md-5 bg-white">
                                <div class="mb-4">
                                    <h3 class="fw-bold text-dark mb-1">เข้าสู่ระบบ</h3>
                                    <p class="text-muted small">กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบงาน Print Form</p>
                                </div>

                                <form action="{{ route('login') }}" method="POST"> 
                                    @csrf 
                                    @error('username')
                                        <div class="alert alert-danger border-0 small py-2 px-3 mb-3 d-flex align-items-center rounded-3">
                                            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ $message }}
                                        </div>
                                    @enderror

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="profile_id" name="profile_id" placeholder="Profile ID" required>
                                        <label for="profile_id"><i class="bi bi-building me-1 text-muted"></i> Profile ID</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                        <label for="username"><i class="bi bi-person me-1 text-muted"></i> Username</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                        <label for="password"><i class="bi bi-lock me-1 text-muted"></i> Password</label>
                                    </div>

                                    @error('password')
                                        <div class="alert alert-danger border-0 small py-2 px-3 mb-3 d-flex align-items-center rounded-3">
                                            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ $message }}
                                        </div>
                                    @enderror

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm rounded-pill" style="font-size: 15px;">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ (Login)
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
