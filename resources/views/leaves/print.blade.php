<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request {{ $leave->leave_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        .document {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 60px 50px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #3496D7;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 32px;
            color: #3496D7;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .ref-number {
            text-align: right;
            color: #999;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #3496D7;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
        }
        .section-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .section-content.full {
            grid-template-columns: 1fr;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-size: 12px;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .field-value {
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-cancelled {
            background: #e2e3e5;
            color: #383d41;
        }
        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 30px;
            padding-top: 10px;
            font-size: 12px;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .document {
                max-width: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="document">
        <!-- Header -->
        <div class="header">
            <h1>LEAVE REQUEST FORM</h1>
            <p>FLAXEM Support Desk - Employee Leave Management</p>
        </div>

        <div class="ref-number">
            <strong>Reference:</strong> {{ $leave->leave_number }}<br>
            <strong>Submitted:</strong> {{ $leave->created_at?->format('F j, Y g:i A') }}
        </div>

        <!-- Employee Information -->
        <div class="section">
            <div class="section-title">Employee Information</div>
            <div class="section-content">
                <div class="field">
                    <div class="field-label">Full Name</div>
                    <div class="field-value">{{ $leave->employee?->full_name ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Department</div>
                    <div class="field-value">{{ $leave->employee?->department?->dept_name ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Employee ID</div>
                    <div class="field-value">{{ $leave->employee?->check_number ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Email</div>
                    <div class="field-value">{{ $leave->employee?->user_email ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Leave Details -->
        <div class="section">
            <div class="section-title">Leave Details</div>
            <div class="section-content">
                <div class="field">
                    <div class="field-label">Leave Type</div>
                    <div class="field-value"><strong>{{ $leave->leave_type ? str_replace('_', ' ', ucfirst($leave->leave_type)) : 'N/A' }}</strong></div>
                </div>
                <div class="field">
                    <div class="field-label">Days Requested</div>
                    <div class="field-value"><strong>{{ $leave->days_requested }} working day(s)</strong></div>
                </div>
                <div class="field">
                    <div class="field-label">From Date</div>
                    <div class="field-value">{{ $leave->from_date?->format('F j, Y (l)') ?? 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">To Date</div>
                    <div class="field-value">{{ $leave->to_date?->format('F j, Y (l)') ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Reason -->
        <div class="section">
            <div class="section-title">Reason for Leave</div>
            <div class="section-content full">
                <div class="field">
                    <div class="field-value" style="border: 1px solid #e0e0e0; padding: 12px; background: #fafafa; border-radius: 4px; min-height: 60px;">
                        {{ $leave->reason ?? 'Not provided' }}
                    </div>
                </div>
                @if($leave->other_specify)
                    <div class="field">
                        <div class="field-label">Additional Notes</div>
                        <div class="field-value" style="border: 1px solid #e0e0e0; padding: 12px; background: #fafafa; border-radius: 4px; min-height: 40px;">
                            {{ $leave->other_specify }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- Approval Information -->
        <div class="section">
            <div class="section-title">Approval Chain</div>
            <div class="section-content">
                <div class="field">
                    <div class="field-label">Supervisor</div>
                    <div class="field-value">{{ $leave->supervisor?->full_name ?? 'Not assigned' }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Approver</div>
                    <div class="field-value">{{ $leave->approver?->full_name ?? 'Pending approval' }}</div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="section">
            <div class="section-title">Current Status</div>
            <div class="section-content full">
                <div class="field">
                    <div class="field-value">
                        <span class="status-badge status-{{ $leave->status }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                </div>
                @if($leave->status === 'approved')
                    <div class="field">
                        <div class="field-label">Approved On</div>
                        <div class="field-value">{{ $leave->approved_at?->format('F j, Y g:i A') ?? 'N/A' }}</div>
                    </div>
                @elseif($leave->status === 'rejected' && $leave->rejection_reason)
                    <div class="field">
                        <div class="field-label">Rejection Reason</div>
                        <div class="field-value" style="border: 1px solid #f8d7da; padding: 12px; background: #fff5f7; border-radius: 4px;">
                            {{ $leave->rejection_reason }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Signature Section (for printing) -->
        <div class="signature-section">
            <div class="signature-block">
                <div class="field-label">Employee Signature</div>
                <div class="signature-line"></div>
                <small>{{ $leave->employee?->full_name ?? '' }}</small>
            </div>
            <div class="signature-block">
                <div class="field-label">Approver Signature</div>
                <div class="signature-line"></div>
                <small>{{ $leave->approver?->full_name ?? 'Pending' }}</small>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is an official leave request document generated from FLAXEM Support Desk System.</p>
            <p>Document Generated: {{ now()->format('F j, Y H:i:s') }}</p>
            <p style="margin-top: 10px; font-size: 11px;">For official use only - Confidential</p>
        </div>
    </div>

    <script>
        // Auto-print on page load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
