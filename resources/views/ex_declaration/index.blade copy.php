<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-class') bg-light">
    <!-- Using include instead of extends for the topbar component -->
    @include('layouts.topbar')
    
    <div class="container mt-4 mb-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-export me-2"></i>ข้อมูลใบขนสินค้าขาออก (Export Declaration)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ex_declaration" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10%;">ลำดับ (No.)</th>
                                <th style="width: 25%;">เลขที่เอกสาร (Doc No)</th>
                                <th class="text-center" style="width: 15%;">วันที่ (Date)</th>
                                <th style="width: 35%;">ชื่อผู้ส่งออก (Exporter)</th>
                                <th class="text-center" style="width: 15%;">สถานะ (Status)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="text-center">1</td><td>EX-2404-001</td><td class="text-center">2024-04-01</td><td>บริษัท เอบีซี จำกัด</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">2</td><td>EX-2404-002</td><td class="text-center">2024-04-02</td><td>บริษัท เอ็กซ์วายซี จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">3</td><td>EX-2404-003</td><td class="text-center">2024-04-03</td><td>หจก. รุ่งเรืองค้าส่ง</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">4</td><td>EX-2404-004</td><td class="text-center">2024-04-04</td><td>บริษัท สยามอิมพอร์ต จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">5</td><td>EX-2404-005</td><td class="text-center">2024-04-05</td><td>บริษัท สมบูรณ์การค้า จำกัด</td><td class="text-center"><span class="badge bg-danger">ยกเลิก</span></td></tr>
                            <tr><td class="text-center">6</td><td>EX-2404-006</td><td class="text-center">2024-04-06</td><td>หจก. ทวีทรัพย์</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">7</td><td>EX-2404-007</td><td class="text-center">2024-04-07</td><td>บริษัท เอบีซี จำกัด</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">8</td><td>EX-2404-008</td><td class="text-center">2024-04-08</td><td>บริษัท มหานครโลจิสติกส์ จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">9</td><td>EX-2404-009</td><td class="text-center">2024-04-09</td><td>บริษัท ก้าวหน้า จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">10</td><td>EX-2404-010</td><td class="text-center">2024-04-10</td><td>หจก. รุ่งเรืองค้าส่ง</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">11</td><td>EX-2404-011</td><td class="text-center">2024-04-11</td><td>บริษัท สยามอิมพอร์ต จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">12</td><td>EX-2404-012</td><td class="text-center">2024-04-12</td><td>บริษัท เอกการค้า จำกัด</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">13</td><td>EX-2404-013</td><td class="text-center">2024-04-13</td><td>บริษัท สมบูรณ์การค้า จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">14</td><td>EX-2404-014</td><td class="text-center">2024-04-14</td><td>หจก. เจริญพาณิชย์</td><td class="text-center"><span class="badge bg-danger">ยกเลิก</span></td></tr>
                            <tr><td class="text-center">15</td><td>EX-2404-015</td><td class="text-center">2024-04-15</td><td>บริษัท เอบีซี จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">16</td><td>EX-2404-016</td><td class="text-center">2024-04-16</td><td>บริษัท มหานครโลจิสติกส์ จำกัด</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                            <tr><td class="text-center">17</td><td>EX-2404-017</td><td class="text-center">2024-04-17</td><td>หจก. รุ่งเรืองค้าส่ง</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">18</td><td>EX-2404-018</td><td class="text-center">2024-04-18</td><td>บริษัท เอกการค้า จำกัด</td><td class="text-center"><span class="badge bg-danger">ยกเลิก</span></td></tr>
                            <tr><td class="text-center">19</td><td>EX-2404-019</td><td class="text-center">2024-04-19</td><td>บริษัท สยามอิมพอร์ต จำกัด</td><td class="text-center"><span class="badge bg-success">อนุมัติ</span></td></tr>
                            <tr><td class="text-center">20</td><td>EX-2404-020</td><td class="text-center">2024-04-20</td><td>บริษัท ก้าวหน้า จำกัด</td><td class="text-center"><span class="badge bg-warning text-dark">รอตรวจสอบ</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/ex_declaration/script.js'])
</body>
</html>