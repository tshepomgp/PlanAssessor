<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Building Plan Compliance Assessment Report</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }

        /* Header Section */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px;
            margin: -20mm -20mm 20px -20mm;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f39c12, #e74c3c, #3498db);
        }

        .header h1 {
            margin: 0;
            font-size: 22pt;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 12pt;
            opacity: 0.9;
            margin-top: 3px;
        }

        .header .report-meta {
            margin-top: 12px;
            font-size: 10pt;
            opacity: 0.8;
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e8ecf0;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .info-card h3 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 12pt;
            font-weight: 600;
            border-bottom: 1px solid #3498db;
            padding-bottom: 5px;
        }

        .info-card p {
            margin: 5px 0;
            font-size: 9pt;
        }

        .info-card .label {
            font-weight: 600;
            color: #34495e;
            display: inline-block;
            width: 80px;
        }

        .info-card .value {
            color: #2c3e50;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Assessment Section */
        .assessment-section {
            margin-top: 25px;
        }

        .section-header {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 20px 0 15px 0;
            border-radius: 0 6px 6px 0;
        }

        .section-header h2 {
            margin: 0;
            font-size: 16pt;
            color: #2c3e50;
            font-weight: 600;
        }

        .section-header .section-desc {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #7f8c8d;
        }

        /* Content Box */
        .content-box {
            background: #ffffff;
            border: 1px solid #e8ecf0;
            border-radius: 6px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            white-space: pre-line;
            font-size: 9pt;
            line-height: 1.5;
        }

        /* Typography for Assessment Content */
        .content-box h1 {
            color: #2c3e50;
            font-size: 14pt;
            margin: 15px 0 10px 0;
            border-bottom: 1px solid #3498db;
            padding-bottom: 5px;
        }

        .content-box h2 {
            color: #34495e;
            font-size: 12pt;
            margin: 12px 0 8px 0;
        }

        .content-box h3 {
            color: #2c3e50;
            font-size: 11pt;
            margin: 10px 0 6px 0;
        }

        /* Compliance Indicators */
        .compliance-check {
            color: #27ae60;
            font-weight: bold;
        }

        .compliance-fail {
            color: #e74c3c;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            color: #7f8c8d;
            font-size: 8pt;
        }

        .footer .company-info {
            margin-bottom: 8px;
        }

        .footer .disclaimer {
            font-style: italic;
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 7pt;
            line-height: 1.3;
        }

        /* Print Optimization */
        @media print {
            .header {
                margin: -20mm -20mm 20mm -20mm;
            }
            
            .info-grid {
                break-inside: avoid;
            }
            
            .content-box {
                break-inside: avoid;
            }
            
            .section-header {
                break-after: avoid;
            }
        }

        /* Additional Styling for Professional Look */
        .highlight-box {
            background: #e8f6f3;
            border: 1px solid #1abc9c;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }

        .warning-box {
            background: #fdf2e9;
            border: 1px solid #e67e22;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }

        .error-box {
            background: #fdf2f2;
            border: 1px solid #e74c3c;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Page Break Control */
        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>🏗️ Building Plan Compliance Assessment</h1>
        <div class="subtitle">Professional Technical Review Report</div>
        <div class="report-meta">
            <strong>Report Generated:</strong> {{ \Carbon\Carbon::now()->format('d F Y • H:i') }}
        </div>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <h3>📋 Project Information</h3>
            <p><span class="label">Plan File:</span> <span class="value">{{ basename($plan->file_path) }}</span></p>
            <p><span class="label">Plan ID:</span> <span class="value">#{{ $plan->id }}</span></p>
            <p><span class="label">Status:</span> 
                <span class="status-badge status-{{ strtolower($plan->status) }}">
                    {{ ucfirst($plan->status) }}
                </span>
            </p>
            <p><span class="label">Submitted:</span> <span class="value">{{ $plan->created_at->format('d M Y') }}</span></p>
        </div>

        <div class="info-card">
            <h3>👤 Architect Information</h3>
            <p><span class="label">Name:</span> <span class="value">{{ Auth::user()->name }}</span></p>
            <p><span class="label">Email:</span> <span class="value">{{ Auth::user()->email }}</span></p>
            <p><span class="label">Company:</span> <span class="value">{{ Auth::user()->company ?? 'Not Specified' }}</span></p>
            <p><span class="label">Registration:</span> <span class="value">{{ Auth::user()->registration_number ?? 'Not Specified' }}</span></p>
        </div>
    </div>

    <div class="assessment-section">
        <div class="section-header">
            <h2>🔍 Comprehensive Assessment Report</h2>
            <div class="section-desc">Detailed analysis of building plan compliance with SANS 10400 and National Building Regulations</div>
        </div>

        <div class="content-box">
            {{ $plan->assessment }}
        </div>
    </div>

    <div class="footer">
        <div class="company-info">
            <strong>PlanAssessInspector</strong> | Professional Building Plan Assessment Service<br>
            🌐 www.sacapsa.com| ✉️ info@sacapsa.com | 📞 +27 (0)11 479 5000
        </div>
        
        <div class="disclaimer">
            <strong>Important Disclaimer:</strong> This assessment report has been generated using advanced AI technology and should be reviewed by a qualified professional architect or building inspector. The analysis provided is for guidance purposes and does not replace professional judgment. All findings should be verified against the latest SANS 10400 standards and National Building Regulations. PlanAssessInspector accepts no liability for decisions made based solely on this automated assessment.
        </div>
    </div>

</body>
</html>