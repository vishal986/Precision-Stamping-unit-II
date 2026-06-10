<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Invoice - {{ $exportInvoice->invoice_no }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Arial', sans-serif; color: #000; margin: 0; padding: 0; font-size: 10px; line-height: 1.1; }
        .print-wrapper { padding: 3mm 7mm; width: 100%; display: flex; justify-content: center; page-break-after: always; page-break-inside: avoid; }
        .print-wrapper:last-child { page-break-after: avoid; }
        .invoice-container { width: 100%; min-height: 285mm; height: 285mm; border: 1px solid #000; box-sizing: border-box; background: #fff; display: flex; flex-direction: column; justify-content: space-between; }
        * { box-sizing: border-box; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: 1px solid #000; padding: 2px 3px; vertical-align: top; word-wrap: break-word; }
        
        .title-row td { text-align: center; font-size: 14px; font-weight: bold; padding: 4px; border-bottom: 2px solid #000; }
        
        .label { font-size: 8px; color: #333; display: block; margin-bottom: 2px; text-decoration: none; font-weight: normal; }
        .bold { font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        
        .items-table { flex-grow: 1; min-height: 130mm; height: 100%; display: table; }
        .items-table th { background: #fff; text-align: center; font-size: 9px; padding: 4px; }
        .items-table .desc-col { width: 49%; }
        
        .footer-section { border-top: none; }
        .declaration { font-size: 8px; text-align: justify; }
        .signature-box { height: 105px; text-align: left; vertical-align: top; }
        
        .no-border { border: none !important; }
        .no-top-border { border-top: none !important; }
        .no-bottom-border { border-bottom: none !important; }
        .no-left-border { border-left: none !important; }
        .no-right-border { border-right: none !important; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .print-wrapper { padding: 3mm 7mm; page-break-after: always; page-break-inside: avoid; }
            .print-wrapper:last-child { page-break-after: avoid; }
            .invoice-container { border: 1px solid #000; min-height: 285mm; height: 285mm; }
        }
        @media screen {
            .print-wrapper { margin-bottom: 20px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 10px; padding: 10px; background: #f0f0f0;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print Invoice</button>
    </div>

    @php
        $chunks = $exportInvoice->items->chunk(16);
        $totalPages = count($chunks);

        $firstPageItemCount = $chunks->first() ? count($chunks->first()) : 0;
        if ($firstPageItemCount >= 16) {
            $rowPaddingTop = 2;
            $rowPaddingBottom = 2;
            $rowGap = 4;
        } else {
            $ratio = max(0, ($firstPageItemCount - 1)) / 15;
            $rowPaddingTop = max(2, round(12 - $ratio * 10));
            $rowPaddingBottom = max(2, round(12 - $ratio * 10));
            $rowGap = max(4, round(16 - $ratio * 12));
        }

        $runningQty = 0;
        $runningAmount = 0;
    @endphp

    @foreach($chunks as $pageIndex => $pageItems)
    <div class="print-wrapper">
        <div class="invoice-container" style="margin-top: 15px;  border-bottom: none;">
            <!-- Title -->
            <table>
                <tr class="title-row">
                    <td colspan="2" style="position: relative;">
                        INVOICE
                        <span style="position: absolute; right: 10px; top: 10px; font-size: 10px; font-weight: normal;">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</span>
                    </td>
                </tr>
                <tr>
                    <!-- Exporter -->
                    <td rowspan="2" style="width: 50%;">
                        <span class="label">Exporter</span>
                        <strong style="font-size: 11px;">PRECISION STAMPINGS (UNIT - II)</strong><br>
                        (A DIVN. OF GUPTA MACHINE TOOLS PVT. LTD.)<br>
                        PLOT NO. 71, SECTOR-25,<br>
                        FARIDABAD-121 004 (INDIA)
                    </td>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td class="no-border" style="width: 50%;">
                                    <span class="label">Invoice No. & Date</span>
                                    <strong>{{ $exportInvoice->invoice_no }} dt. {{ \Carbon\Carbon::parse($exportInvoice->invoice_date)->format('d.m.Y') }}</strong>
                                </td>
                                <td class="no-border" style="width: 50%;">
                                    <span class="label">Exporter's Ref.</span>
                                    {{ $exportInvoice->exporter_ref ?? '0288019857 Br. 8' }}<br>
                                    <span class="label" style="display:inline;">GSTN</span> {{ $exportInvoice->customer->gstin ?? '06AABCG0676Q1Z8' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Buyer's Order No. & Date :</span>
                        @php
                            $poList = [];
                            foreach($exportInvoice->items as $item) {
                                if (!empty($item->order_number)) {
                                    $poStr = $item->order_number;
                                    if (!empty($item->order_date)) {
                                        $poStr .= ' dt. ' . \Carbon\Carbon::parse($item->order_date)->format('d.m.Y');
                                    }
                                    if (!in_array($poStr, $poList)) {
                                        $poList[] = $poStr;
                                    }
                                }
                            }
                            
                            $formattedPoList = array_map(function($po) {
                                return '<span style="white-space: nowrap;">' . e($po) . '</span>';
                            }, $poList);
                            
                            $poDisplayString = implode(', ', $formattedPoList);
                            if (empty($poList)) {
                                $poDisplayString = '<span style="white-space: nowrap;">' . e($exportInvoice->buyer_order_no) . '</span>';
                            }
                        @endphp
                        <strong>{!! $poDisplayString !!}</strong>
                    </td>
                </tr>
                <tr>
                    <!-- Consignee -->
                    <td style="border-bottom: none;">
                        <span class="label">Consignee</span>
                        <strong>{{ $exportInvoice->customer->company_name }}</strong><br>
                        {!! nl2br(e($exportInvoice->customer->address)) !!}<br>
                        Attn. Mr. {{ $exportInvoice->customer->contact_person ?? 'Yannick Hugo' }}<br>
                        Tel : {{ $exportInvoice->customer->phone ?? '0049-661-494-552' }}<br>
                        {{ $exportInvoice->customer->email ?? '' }}
                    </td>
                    <td>
                        <span class="label">Buyer (if other than consignee)</span>
                        @if(!empty($exportInvoice->buyer_details) && trim($exportInvoice->buyer_details) !== 'Same as consignee')
                            {!! nl2br(e($exportInvoice->buyer_details)) !!}
                        @else
                            <strong>{{ $exportInvoice->customer->company_name }}</strong><br>
                            {!! nl2br(e($exportInvoice->customer->address)) !!}<br>
                            Attn. Mr. {{ $exportInvoice->customer->contact_person ?? 'Reiner Odenwald' }}<br>
                            Tel : {{ $exportInvoice->customer->phone ?? '' }}<br>
                            {{ $exportInvoice->customer->email ?? '' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 0; border-top: none;">
                        <table style="border: none;">
                            <colgroup>
                                <col style="width: 50%;">
                                <col style="width: 50%;">
                            </colgroup>
                            <tr>
                                <td style="padding: 0; vertical-align: bottom; border: none;">
                                    <table style="border: none;">
                                        <colgroup>
                                            <col style="width: 50%;">
                                            <col style="width: 50%;">
                                        </colgroup>
                                        <tr>
                                            <td style="padding: 2px 3px; border-top: 1px solid #000; border-left: none;">
                                                <span class="label">Pre-Carriage by</span>
                                                <strong>{{ $exportInvoice->pre_carriage_by ?? 'Road' }}</strong>
                                            </td>
                                            <td style="padding: 2px 3px; border-top: 1px solid #000; border-right: none;">
                                                <span class="label">Place of Receipt by Pre-Carrier</span>
                                                <strong>{{ $exportInvoice->place_of_receipt ?? 'ICD Faridabad' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 2px 3px; border-left: none;">
                                                <span class="label">Vessel/Flight No.</span>
                                                <strong>{{ $exportInvoice->vessel_flight_no ?? 'By Sea' }}</strong>
                                            </td>
                                            <td style="padding: 2px 3px; border-right: none;">
                                                <span class="label">Port of Loading</span>
                                                <strong>{{ $exportInvoice->port_of_loading ?? 'Mundra' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 2px 3px; border-bottom: none; border-left: none;">
                                                <span class="label">Port of Discharge</span>
                                                <strong>{{ $exportInvoice->port_of_discharge ?? 'Hamburg' }}</strong>
                                            </td>
                                            <td style="padding: 2px 3px; border-bottom: none; border-right: none;">
                                                <span class="label">Final Destination</span>
                                                <strong>{{ $exportInvoice->final_destination ?? 'Germany' }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="vertical-align: top; border-top: none; border-right: none; border-bottom: none; border-left: 1px solid #000; padding: 0;">
                                    <table style="border: none;">
                                        <colgroup>
                                            <col style="width: 30%;">
                                            <col style="width: 70%;">
                                        </colgroup>
                                        <tr>
                                            <td style="padding: 3px; border-top: none; border-bottom: 1px solid #000; border-left: none; border-right: 1px solid #000;">
                                                <span class="label">Country of Origin of Goods</span>
                                                <strong>{{ $exportInvoice->country_of_origin ?? 'India' }}</strong>
                                            </td>
                                            <td style="padding: 3px; border-top: none; border-bottom: 1px solid #000; border-left: none; border-right: none;">
                                                <span class="label">Country of Final Destination</span>
                                                <strong>{{ $exportInvoice->country_of_final_destination ?? 'Germany' }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="padding: 3px; border-bottom: 1px solid #000;">
                                        <span class="label">Terms of Delivery and Payment</span>
                                        Terms of delivery : {{ $exportInvoice->incoterms ?? 'CIF Hamburg' }}<br>
                                        Terms of Payment : {{ $exportInvoice->payment_terms ?? '30 days net.' }}
                                    </div>
                                    <div style="padding: 3px;">
                                        <span class="label">Bank Details:</span>
                                        <div style="font-size: 9px; line-height: 1.4;">
                                            Please pay through TT ({{ $exportInvoice->currency }}) to our Bank Account as per details below :-<br>
                                            <strong>CANARA BANK - PRIME CORPORATE BRANCH - II , 2ND FLOOR,<br>
                                            WORLD TRADE TOWER, BARAKHAMBA LANE, NEW DELHI-110 001, INDIA.<br>
                                            ACCOUNT No. 0307256054876, &nbsp;&nbsp;&nbsp; SWIFT CODE : CNRBINBBIFD<br>
                                            @if($exportInvoice->currency == 'USD')
                                                Through Corresponding Bank :- J.P. MORGAN CHASE BANK N.A., NEW YORK<br>
                                                SWIFT: CHASUS33 in Account No. 400875020001 of CANARA BANK.
                                            @else
                                                Through Corresponding Bank :- COMMERZBANK, FRANKFURT<br>
                                                SWIFT: COBADEFF in Account No. 400875020001 of CANARA BANK.
                                            @endif
                                            </strong>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Marks & Nos</th>
                        <th style="width: 8%;">No. & Kind<br>of Pkgs.</th>
                        <th style="width: 4%; border-right: none;"></th>
                        <th class="desc-col" style="border-left: none;">Description of Goods</th>
                        <th style="width: 10%;">Quantity<br>(Nos.)</th>
                        <th style="width: 10%;">Rate<br>({{ $exportInvoice->currency }})</th>
                        <th style="width: 10%;">Amount<br>({{ $exportInvoice->currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- General Category Header (Printed on all pages) -->
                    <tr>
                        <td class="center" style="border-bottom: none; border-top: none; position: relative;">
                            <div style="position: absolute; top: 5px; left: 0; right: 0; text-align: center;">
                                {!! nl2br(e($exportInvoice->marks_and_nos ?? "29 Plyboard\nBoxes\nNos\n01/29\nto\n29/29")) !!}
                            </div>
                        </td>
                        <td class="center" style="border-bottom: none; border-top: none; position: relative;">
                            <div style="position: absolute; top: 5px; left: 0; right: 0; text-align: center;">
                                {!! nl2br(e($exportInvoice->no_and_kind_of_pkgs ?? '29 Plyboard Boxes')) !!}
                            </div>
                        </td>
                        <td style="border-bottom: none; border-top: none; border-right: none;"></td>
                        <td style="border-bottom: none; border-top: none; border-left: none; padding-bottom: 0;">
                            <div style="text-align: center; font-weight: bold; margin-bottom: 0px;">
                                ELECTRICAL STAMPINGS & LAMINATION<br>
                                (PARTS OF ELECTRIC MOTOR)<br>
                                STATOR & ROTOR
                            </div>
                        </td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                    </tr>

                    @if($pageIndex > 0)
                    <tr>
                        <td style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;"></td>
                        <td style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;"></td>
                        <td style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;"></td>
                        <td style="border-bottom: none; border-top: none; border-right: none; padding-top: 0px; padding-bottom: 0px;"></td>
                        <td class="right bold" style="border-bottom: none; border-top: none; border-left: none; padding-top: 0px; padding-bottom: 0px; padding-right: 20px;">B/F</td>
                        <td class="center bold" style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;">{{ number_format($runningQty, 0) }}</td>
                        <td style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;"></td>
                        <td class="right bold" style="border-bottom: none; border-top: none; padding-top: 0px; padding-bottom: 0px;">{{ number_format($runningAmount, 2) }}</td>
                    </tr>
                    @endif

                    <!-- Item and Order No labels (Printed on all pages) -->
                    <tr>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none; border-right: none;"></td>
                        <td style="border-bottom: none; border-top: none; border-left: none; padding-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; align-items: baseline; margin-top: {{ $pageIndex == 0 ? '15px' : '0px' }};">
                                <strong>Item</strong>
                                <strong>Order No. & Date</strong>
                            </div>
                        </td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                    </tr>

                    @php
                        $runningQty += $pageItems->sum('quantity');
                        $runningAmount += $pageItems->sum('total_price');
                    @endphp

                    @foreach($pageItems as $itemIndex => $item)
                    <!-- Item Details Row -->
                    <tr>
                        <td style="border-bottom: none; border-top: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;"></td>
                        <td style="border-bottom: none; border-top: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;"></td>
                        <td class="center" style="border-bottom: none; border-top: none; border-right: none; white-space: nowrap; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;">{{ $pageIndex * 16 + $loop->iteration }}.</td>
                        <td style="border-bottom: none; border-top: none; border-left: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;">
                            @php
                                $lfeDisplay = $item->item->lfe;
                                if (empty($lfeDisplay) && !empty($item->item->description)) {
                                    if (preg_match('/lfe\s*[-.:]\s*([^\r\n]+)/i', $item->item->description, $matches)) {
                                        $lfeDisplay = trim($matches[1]);
                                        if (strpos($lfeDisplay, "\n") !== false) {
                                            $lfeDisplay = trim(explode("\n", $lfeDisplay)[0]);
                                        }
                                        $lfeDisplay = str_ireplace('mm', '', $lfeDisplay);
                                        $lfeDisplay = trim($lfeDisplay);
                                    }
                                } else {
                                    $lfeDisplay = str_ireplace('mm', '', $lfeDisplay);
                                    $lfeDisplay = trim($lfeDisplay);
                                }
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong>{{ $item->item->name }}</strong>
                                <span style="white-space: nowrap;">{{ $item->order_number ?? '' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <span>Article No. {{ $item->item->item_code }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Lfe. {{ $lfeDisplay ?? '' }}</span>
                                <span style="white-space: nowrap;">{{ $item->order_date ? \Carbon\Carbon::parse($item->order_date)->format('d.m.Y') : '' }}</span>
                            </div>
                            <div style="height: {{ $rowGap }}px;"></div>
                        </td>
                        <td class="center" style="border-bottom: none; border-top: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;">
                            {{ number_format($item->quantity, 0) }}
                        </td>
                        <td class="center" style="border-bottom: none; border-top: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="center" style="border-bottom: none; border-top: none; padding-top: {{ $rowPaddingTop }}px; padding-bottom: {{ $rowPaddingBottom }}px;">
                            {{ number_format($item->total_price, 2) }}
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- Flexible spacer row to expand and fill empty page height -->
                    <tr style="height: 100%;">
                        <td style="border-bottom: none; border-top: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; border-right: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; border-left: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; height: 100%;"></td>
                        <td style="border-bottom: none; border-top: none; height: 100%;"></td>
                    </tr>
                    @if($pageIndex + 1 == $totalPages)
                    <tr>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none; border-right: none;"></td>
                        <td style="border-bottom: none; border-top: none; border-left: none; padding-top: 20px;">
                            <div style="font-size: 8px;">
                                Note:- "No Russian iron and steel products were used in the production of the specified goods in accordance with regulation (EU) no. 833/2014."
                            </div>
                            <div class="center bold" style="margin-top: 10px;">
                                HS Code &nbsp;&nbsp; 85030090
                            </div>
                        </td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                        <td style="border-bottom: none; border-top: none;"></td>
                    </tr>
                    @endif
                </tbody>
                <tfoot style="border-bottom: none;">
                    <tr style="border-bottom: none;">
                        <td colspan="4" class="right bold">{{ $pageIndex + 1 == $totalPages ? 'Total' : 'C/F' }}</td>
                        <td class="center bold">{{ number_format($runningQty, 0) }}</td>
                        <td class="center bold"></td>
                        <td class="right bold">{{ number_format($runningAmount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Summary -->
            <table>
                <colgroup style="border: 2px solid #000; border-top: none;">
                    <col style="width: 70%;">
                    <col style="width: 30%;">
                </colgroup>
                <tr>
                    <td colspan="2">
                        <span class="label">Amount Chargeable (in words) :</span>
                        <strong style="text-transform: capitalize;">
                            {{ $exportInvoice->currency }} {{ ucfirst(\App\Helpers\NumberHelper::spellOut($exportInvoice->total_amount)) }} Only
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td style="width: 70%; padding: 3px;">
                        <div class="declaration">
                            <strong>DECLARATION 1:</strong> - The Exporter REX Registration No.- INREX0288019857EC028, of the products covered by this document declares that, except where otherwise clearly indicated, these products are of Indian Preferential origin (Products wholly obtained in India "P"), according to rules of origin of the Generalised System of Preferences of the European Union and that the origin criterion met is "P"
                        </div>
                    </td>
                    <td rowspan="2" style="width: 30%; vertical-align: top; padding: 3px;" class="signature-box">
                        <span class="label">Stamp & Signature</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 70%; padding: 3px;">
                        <div class="declaration">
                            <strong>DECLARATION 2:</strong> - We declare that this Invoice shows the actual price of the goods described and that all particulars are true and correct.
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach
</body>
</html>
