<html>
<head>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: 'Georgia', serif; background: white; }
        .container {
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 5px solid #d4af37;
            page-break-after: avoid;
        }
        .header { text-align: center; margin-bottom: 40px; }
        .logo { font-size: 32px; font-weight: bold; color: #1a5490; margin-bottom: 10px; }
        .title {
            font-size: 48px;
            font-weight: bold;
            color: #d4af37;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content { text-align: center; margin: 30px 0; }
        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a5490;
            margin: 20px 0;
        }
        .course-name {
            font-size: 20px;
            color: #333;
            margin: 20px 0;
        }
        .date { font-size: 14px; color: #666; margin-top: 20px; }
        .certificate-number {
            font-size: 12px;
            color: #999;
            margin-top: 30px;
        }
        .footer {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            width: 100%;
        }
        .signature { text-align: center; width: 25%; }
        .signature-line { border-top: 2px solid #333; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
        </div>

        <div class="title">Certificate of Completion</div>

        <div class="content">
            <p>This is to certify that</p>
            <div class="student-name">{{ $enrollment->user->name }}</div>
            <p>has successfully completed the course</p>
            <div class="course-name">{{ $enrollment->course->title }}</div>
            <p>with a final progress of {{ round($enrollment->progress_percent, 1) }}%</p>
        </div>

        <div class="date">
            <p>Issued on {{ $issuedDate }}</p>
            <div class="certificate-number">Certificate #: {{ $certificateNumber }}</div>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Student</p>
                <div class="signature-line"></div>
            </div>
            <div class="signature">
                <p>Instructor</p>
                <div class="signature-line"></div>
                <p style="font-size: 12px;">{{ $enrollment->course->instructor->name }}</p>
            </div>
            <div class="signature">
                <p>{{ config('app.name') }}</p>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>
