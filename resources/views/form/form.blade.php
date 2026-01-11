<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบรับแจ้งการปลูกกัญชา กัญชง</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <style>
        /* ตั้งค่าพื้นฐาน */
        body {
            background-color: #525659; /* สีพื้นหลังตอนดูในจอ */
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        /* จำลองกระดาษ A4 */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm; /* ขอบกระดาษ */
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
            position: relative;
        }

        /* CSS สำหรับ Input แบบเส้นประ */
        .input-dotted {
            border: none;
            border-bottom: 1px dotted #000;
            width: auto;
            display: inline-block;
            text-align: center;
            color: #000;
            font-family: 'Sarabun', sans-serif;
            font-size: 16pt;
            background: transparent;
            outline: none;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        
        /* จัด Layout หัวกระดาษ */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .garuda {
            width: 3cm; /* ขนาดครุฑมาตรฐาน */
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .form-content {
            font-size: 16pt; /* ขนาดตัวอักษรมาตรฐานราชการ 16pt */
            line-height: 1.6;
        }

        .indent {
            text-indent: 2.5cm; /* ย่อหน้า */
        }

        /* ปุ่ม Print (ซ่อนตอนปริ้น) */
        .btn-print-wrapper {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-print {
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-print:hover { background-color: #0b5ed7; }

        /* --- ตั้งค่าสำหรับการ Print (A4) --- */
        @media print {
            body {
                background: none;
                display: block;
            }
            .page {
                margin: 0;
                box-shadow: none;
                width: auto;
                height: auto;
                page-break-after: always;
            }
            .btn-print-wrapper {
                display: none; /* ซ่อนปุ่มปริ้น */
            }
            @page {
                size: A4;
                margin: 0; /* ให้ CSS ควบคุม Margin เอง */
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-wrapper">
        <button onclick="window.print()" class="btn-print">
            🖨️ Print Form (A4)
        </button>
    </div>

    <div class="page">
        
        <div class="text-right" style="font-size: 12pt; margin-bottom: -20px;">
            แบบ จ.ก. ๑
        </div>

        <div class="text-center mb-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/Thai_Government_Garuda_Emblem_%28Version_2%29.svg/1200px-Thai_Government_Garuda_Emblem_%28Version_2%29.svg.png" 
                 class="garuda" alt="Garuda">
        </div>

        <div class="header-top">
            <div style="flex: 1;"></div>
            <div style="flex: 2;" class="text-center">
                <h2 class="font-bold" style="margin: 0; font-size: 20pt;">ใบรับแจ้งการปลูกกัญชา กัญชง</h2>
            </div>
            <div style="flex: 1;" class="text-right">
                เลขที่รับ <input type="text" class="input-dotted" style="width: 80px;" value=""><br>
                วันที่ <input type="text" class="input-dotted" style="width: 80px;" value="">
            </div>
        </div>

        <div class="form-content">
            
            <div class="text-right mb-2">
                เขียนที่ <input type="text" class="input-dotted" style="width: 250px;" value="สำนักงานสาธารณสุขจังหวัด...">
            </div>
            <div class="text-right mb-4">
                วันที่ <input type="text" class="input-dotted" style="width: 40px;"> 
                เดือน <input type="text" class="input-dotted" style="width: 100px;"> 
                พ.ศ. <input type="text" class="input-dotted" style="width: 60px;">
            </div>

            <p class="indent mb-2">
                ข้าพเจ้า <input type="text" class="input-dotted" style="width: 300px;" placeholder="ระบุชื่อ-นามสกุล">
                อายุ <input type="text" class="input-dotted" style="width: 50px;"> ปี
                สัญชาติ <input type="text" class="input-dotted" style="width: 100px;">
            </p>

            <p class="mb-2">
                เลขประจำตัวประชาชน <input type="text" class="input-dotted" style="width: 250px;">
            </p>

            <p class="mb-2">
                อยู่บ้านเลขที่ <input type="text" class="input-dotted" style="width: 80px;">
                หมู่ที่ <input type="text" class="input-dotted" style="width: 50px;">
                ตรอก/ซอย <input type="text" class="input-dotted" style="width: 150px;">
                ถนน <input type="text" class="input-dotted" style="width: 150px;">
            </p>
            <p class="mb-4">
                ตำบล/แขวง <input type="text" class="input-dotted" style="width: 150px;">
                อำเภอ/เขต <input type="text" class="input-dotted" style="width: 150px;">
                จังหวัด <input type="text" class="input-dotted" style="width: 150px;">
                รหัสไปรษณีย์ <input type="text" class="input-dotted" style="width: 80px;">
                โทรศัพท์ <input type="text" class="input-dotted" style="width: 150px;">
            </p>

            <p class="indent mb-4">
                ขอแจ้งการจดแจ้งการปลูก 
                <span style="margin: 0 10px;">
                    <input type="checkbox"> กัญชา 
                </span>
                <span style="margin: 0 10px;">
                    <input type="checkbox"> กัญชง
                </span>
                ต่อนายทะเบียน โดยมีรายละเอียดดังนี้
            </p>

            <p class="mb-2">
                ๑. วัตถุประสงค์การปลูก <input type="text" class="input-dotted" style="width: 100%;">
                <input type="text" class="input-dotted" style="width: 100%;">
            </p>

            <p class="mb-4">
                ๒. สถานที่ปลูก ตั้งอยู่เลขที่ <input type="text" class="input-dotted" style="width: 350px;">
                (ระบุพิกัด หากมี) <input type="text" class="input-dotted" style="width: 200px;">
            </p>

            <p class="indent mb-4">
                ข้าพเจ้าขอรับรองว่า รายละเอียดข้างต้นเป็นความจริงทุกประการ
            </p>

            <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
                <div style="text-align: center; width: 300px;">
                    <p>
                        (ลงชื่อ) <input type="text" class="input-dotted" style="width: 200px;"> ผู้แจ้ง
                    </p>
                    <p style="margin-top: -10px;">
                        (<input type="text" class="input-dotted" style="width: 200px;">)
                    </p>
                </div>
            </div>

            <hr style="border-top: 2px solid black; margin: 30px 0;">
            
            <p class="font-bold">สำหรับเจ้าหน้าที่</p>
            <div class="indent mb-2">
                <input type="checkbox"> ตรวจสอบแล้ว ครบถ้วนถูกต้อง รับจดแจ้งเลขที่ <input type="text" class="input-dotted" style="width: 150px;">
            </div>
            <div class="indent mb-2">
                <input type="checkbox"> เอกสารไม่ครบถ้วน/ไม่ถูกต้อง ให้ดำเนินการแก้ไขภายใน <input type="text" class="input-dotted" style="width: 50px;"> วัน
            </div>

            <div style="margin-top: 40px; display: flex; justify-content: flex-end;">
                <div style="text-align: center; width: 300px;">
                    <p>
                        (ลงชื่อ) <input type="text" class="input-dotted" style="width: 200px;"> นายทะเบียน
                    </p>
                    <p style="margin-top: -10px;">
                        (<input type="text" class="input-dotted" style="width: 200px;">)
                    </p>
                    <p>
                        ตำแหน่ง <input type="text" class="input-dotted" style="width: 200px;">
                    </p>
                    <p>
                        วันที่ <input type="text" class="input-dotted" style="width: 200px;">
                    </p>
                </div>
            </div>

        </div> </div> </body>
</html>