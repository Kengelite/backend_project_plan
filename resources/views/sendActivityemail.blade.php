<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['title'] ?? 'แจ้งเตือนจากระบบ' }}</title>
    <style>
        body {
            font-family: 'Sarabun', 'Tahoma', Arial, sans-serif;
            line-height: 1.8;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a5f 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 25px;
            color: #2c5aa0;
            font-weight: 500;
        }

        .message-body {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 30px;
            text-align: justify;
            color: #444;
        }

        .highlight-box {
            background-color: #f8f9ff;
            border-left: 4px solid #2c5aa0;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }

        .system-info {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 6px;
            font-size: 14px;
            color: #2c5aa0;
            text-align: center;
            margin: 25px 0;
        }

        .footer {
            border-top: 2px solid #e9ecef;
            padding-top: 25px;
            margin-top: 30px;
        }

        .signature {
            text-align: right;
            font-size: 15px;
            color: #555;
        }

        .signature .closing {
            margin-bottom: 15px;
            font-style: italic;
        }

        .signature .name {
            font-weight: 600;
            color: #2c5aa0;
        }

        .bottom-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }

        .contact-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }

        .urgent {
            color: #dc3545;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 6px;
            }

            .header, .content {
                padding: 20px;
            }

            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $details['title'] ?? 'แจ้งเตือนจากระบบ E-plan' }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                เรียน ผู้รับผิดชอบงาน  {{ $reponsible }}
            </div>

            <div class="message-body">
                {{ $details['body'] ?? 'ไม่มีเนื้อหา' }}
            </div>

            @if(isset($details['deadline']))
            <div class="highlight-box">
                <strong class="urgent">กำหนดส่ง:</strong> {{ $details['deadline'] }}
            </div>
            @endif

            <div class="system-info">
                📋 ระบบแผน E-plan
                <br> ระบบบริหารจัดการภารกิจด้านแผนยุทธศาสตร์และแผนงาน
            </div>

            <div class="contact-info">
                <strong>💬 ติดต่อสอบถาม:</strong><br>
                หากมีข้อสงสัยหรือต้องการความช่วยเหลือ กรุณาติดต่อคุณรัตติกร แทนเพชร<br>

            </div>

            <!-- Footer Signature -->
            <div class="footer">
                <div class="signature">
                    <div class="closing">
                        {{ $details['footer'] ?? 'ขอแสดงความนับถือ' }}
                    </div>
                    <div class="name">
                        {{ $details['admin_name'] ?? 'ทีมงานระบบ E-plan' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Footer -->
        <div class="bottom-footer">
            <p>อีเมลนี้ส่งโดยอัตโนมัติจากระบบ E-plan<br>
            กรุณาอย่าตอบกลับอีเมลนี้โดยตรง</p>
            <p style="margin-top: 10px; font-size: 11px;">
                © {{ date('Y') }} ระบบ E-plan | สงวนลิขสิทธิ์
            </p>
        </div>
    </div>
</body>
</html>
