<!DOCTYPE html>
<html>
<head>
    <title>Gatepass Slip - {{ $gatepass->employee->employee_code }}</title>
    <style>
        body { font-family: sans-serif; background: #fff; color: #000; padding: 20px; }
        .slip { width: 350px; border: 2px dashed #000; padding: 15px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .title { font-size: 1.5rem; font-weight: bold; text-transform: uppercase; }
        .field { margin: 8px 0; display: flex; justify-content: space-between; }
        .label { font-weight: bold; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        .sign-area { border-top: 1px solid #000; width: 120px; text-align: center; font-size: 0.8rem; padding-top: 5px; }
        .type-badge { background: #000; color: #fff; padding: 2px 8px; font-size: 0.8rem; border-radius: 4px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Gatepass Slip</button>
    </div>

    <div class="slip">
        <div class="header">
            <div style="font-weight: bold; font-size: 1.1rem;">PRECISION STAMPINGS</div>
            <div class="title">GATEPASS SLIP</div>
            <div class="type-badge">{{ strtoupper($gatepass->type) }}</div>
        </div>

        <div class="field">
            <span class="label">GP Number:</span>
            <span>#GP-{{ str_pad($gatepass->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="field">
            <span class="label">Date:</span>
            <span>{{ \Carbon\Carbon::parse($gatepass->date)->format('d-m-Y') }}</span>
        </div>
        <div class="field">
            <span class="label">Employee:</span>
            <span>{{ $gatepass->employee->first_name }} {{ $gatepass->employee->last_name }}</span>
        </div>
        <div class="field">
            <span class="label">Emp Code:</span>
            <span>{{ $gatepass->employee->employee_code }}</span>
        </div>
        <div class="field">
            <span class="label">Dept:</span>
            <span>{{ $gatepass->employee->department->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="label">Out Time:</span>
            <span style="font-size: 1.2rem; font-weight: bold;">{{ \Carbon\Carbon::parse($gatepass->out_time)->format('H:i') }}</span>
        </div>
        @if($gatepass->in_time)
        <div class="field">
            <span class="label">In Time:</span>
            <span style="font-size: 1.2rem; font-weight: bold;">{{ \Carbon\Carbon::parse($gatepass->in_time)->format('H:i') }}</span>
        </div>
        @endif
        <div class="field">
            <span class="label">Reason:</span>
            <span style="font-size: 0.9rem;">{{ $gatepass->reason }}</span>
        </div>

        <div class="footer">
            <div class="sign-area">Employee Sign</div>
            <div class="sign-area">Auth. Signatory</div>
        </div>
        
        <div style="text-align: center; font-size: 0.7rem; margin-top: 15px; color: #666;">
            Valid only for the date and time mentioned above.
        </div>
    </div>
</body>
</html>
