<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Token Statement - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #4F46E5;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0;
        }
        
        .user-info {
            background-color: #F8FAFC;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .user-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .user-info td {
            padding: 5px 10px;
            border: none;
        }
        
        .user-info .label {
            font-weight: bold;
            color: #4B5563;
            width: 30%;
        }
        
        .summary {
            margin-bottom: 30px;
        }
        
        .summary h2 {
            color: #4F46E5;
            font-size: 16px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 5px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            border: 1px solid #E5E7EB;
            background-color: #F9FAFB;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
        }
        
        .summary-label {
            font-size: 11px;
            color: #6B7280;
            margin-top: 5px;
        }
        
        .transactions {
            margin-top: 30px;
        }
        
        .transactions h2 {
            color: #4F46E5;
            font-size: 16px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 5px;
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .transaction-table th {
            background-color: #F3F4F6;
            color: #374151;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #E5E7EB;
            font-size: 11px;
        }
        
        .transaction-table td {
            padding: 8px;
            border: 1px solid #E5E7EB;
            font-size: 11px;
        }
        
        .transaction-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .type-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .type-purchase {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .type-usage {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .type-refund {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .type-admin {
            background-color: #F3F4F6;
            color: #374151;
        }
        
        .positive {
            color: #059669;
            font-weight: bold;
        }
        
        .negative {
            color: #DC2626;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6B7280;
            font-size: 10px;
            border-top: 1px solid #E5E7EB;
            padding-top: 20px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>🏗️ SACAP AI Token Statement</h1>
        <p>AI-Powered Plan Assessment System</p>
        <p><strong>Statement Period:</strong> {{ date('M d, Y', strtotime($startDate)) }} to {{ date('M d, Y', strtotime($endDate)) }}</p>
        <p><strong>Generated:</strong> {{ now()->format('M d, Y H:i') }}</p>
    </div>

    {{-- User Information --}}
    <div class="user-info">
        <table>
            <tr>
                <td class="label">Account Holder:</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td class="label">Email:</td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Company:</td>
                <td>{{ $user->company ?: 'Not specified' }}</td>
                <td class="label">Registration:</td>
                <td>{{ $user->registration_number ?: 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="label">Account Status:</td>
                <td>{{ ucfirst($user->status) }}</td>
                <td class="label">Member Since:</td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <h2>📊 Statement Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ number_format($summary['opening_balance']) }}</div>
                <div class="summary-label">Opening Balance</div>
            </div>
            <div class="summary-item">
                <div class="summary-value positive">+{{ number_format($summary['purchased']) }}</div>
                <div class="summary-label">Tokens Purchased</div>
            </div>
            <div class="summary-item">
                <div class="summary-value negative">{{ number_format($summary['used']) }}</div>
                <div class="summary-label">Tokens Used</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ number_format($summary['closing_balance']) }}</div>
                <div class="summary-label">Closing Balance</div>
            </div>
        </div>
        
        @if($summary['total_spent'] > 0)
            <p><strong>Total Amount Spent:</strong> R{{ number_format($summary['total_spent'], 2) }}</p>
        @endif
    </div>

    {{-- Transactions --}}
    <div class="transactions">
        <h2>💳 Transaction History</h2>
        
        @if($transactions->count() > 0)
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 10%;">Tokens</th>
                        <th style="width: 10%;">Amount</th>
                        <th style="width: 13%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = $summary['opening_balance']; @endphp
                    @foreach($transactions as $transaction)
                        @php $runningBalance += $transaction->tokens; @endphp
                        <tr>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                {{ $transaction->description }}
                                @if($transaction->plan)
                                    <br><small style="color: #6B7280;">Plan: {{ $transaction->plan->client_name }}</small>
                                @endif
                                @if($transaction->metadata && isset($transaction->metadata['bonus_tokens']) && $transaction->metadata['bonus_tokens'] > 0)
                                    <br><small style="color: #2563EB;">+{{ $transaction->metadata['bonus_tokens'] }} bonus tokens</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="type-badge type-{{ $transaction->type }}">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                </span>
                            </td>
                            <td class="text-right {{ $transaction->tokens > 0 ? 'positive' : 'negative' }}">
                                {{ $transaction->tokens > 0 ? '+' : '' }}{{ number_format($transaction->tokens) }}
                            </td>
                            <td class="text-right">
                                @if($transaction->amount > 0)
                                    R{{ number_format($transaction->amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right"><strong>{{ number_format($runningBalance) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #6B7280; padding: 40px;">
                No transactions found in the selected date range.
            </p>
        @endif
    </div>

    {{-- Usage Statistics --}}
    @if($transactions->where('type', 'usage')->count() > 0)
        <div class="summary" style="margin-top: 40px;">
            <h2>📈 Usage Analytics</h2>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">{{ $transactions->where('type', 'usage')->count() }}</div>
                    <div class="summary-label">Assessments Completed</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">
                        {{ number_format($transactions->where('type', 'usage')->avg('tokens') ? abs($transactions->where('type', 'usage')->avg('tokens')) : 0) }}
                    </div>
                    <div class="summary-label">Avg Tokens per Assessment</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">{{ $transactions->where('type', 'usage')->unique('plan_id')->count() }}</div>
                    <div class="summary-label">Unique Plans Assessed</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>This statement was generated automatically by the SACAP AI system.</p>
        <p>For questions about your account or transactions, please contact support.</p>
        <p>© {{ date('Y') }} SACAP AI-Powered Plan Assessment System. All rights reserved.</p>
    </div>
</body>
</html>