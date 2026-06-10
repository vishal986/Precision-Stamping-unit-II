@extends('layouts.app')

@section('content')
<style>
    /* Limit Item Choices dropdown list to display exactly maximum 2 items at a time */
    #itemsTable .choices .choices__list--dropdown .choices__list {
        max-height: 85px !important;
    }
    /* Compact styling for Item Specifications table */
    #itemsTable th {
        font-size: 0.85rem;
        padding: 0.5rem 0.4rem;
    }
    #itemsTable td {
        font-size: 0.85rem;
        padding: 0.4rem 0.3rem;
        vertical-align: middle;
    }
    #itemsTable .form-control {
        height: 30px;
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }
    #itemsTable .choices__inner {
        min-height: 30px;
        padding: 0.15rem 0.5rem;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }
    #itemsTable .choices[data-type*=select-one] .choices__input {
        padding: 0.2rem;
        font-size: 0.85rem;
    }
</style>
<div class="page-header">
    <h1 class="page-title">Create Export Invoice (Germany/International)</h1>
</div>

<form action="{{ route('export-invoices.store') }}" method="POST">
    @csrf
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Left Side: Basic Info & Items -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Basic Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" name="invoice_no" class="form-control" value="EXP-{{ date('Y') }}-{{ rand(1000,9999) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer (Germany/International)</label>
                        <select name="customer_id" id="customer_id" class="form-control searchable-select" onchange="filterItemsByCustomer(this.value)" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->company_name }} ({{ $c->country ?? 'Germany' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-control" onchange="updateCurrencySymbol(this.value)">
                            <option value="EUR">EURO (€)</option>
                            <option value="USD">USD ($)</option>
                            <option value="INR">INR (₹)</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Buyer <span style="color: var(--text-secondary); font-size: 0.8rem;">(if other than consignee)</span></label>
                        <textarea name="buyer_details" class="form-control" rows="3" placeholder="Enter manual buyer details or leave as default...">Same as consignee</textarea>
                    </div>

                    <input type="hidden" name="exporter_ref" value="0288019857 Br. 8">

                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: var(--primary-color);">Packaging & Total Boxes</h3>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label class="form-label" style="margin: 0; white-space: nowrap;">Total Boxes:</label>
                        <input type="number" id="total_boxes_input" class="form-control" style="width: 100px; padding: 0.25rem 0.5rem;" value="0" min="0" oninput="updateBoxNumbers(this.value)">
                    </div>
                </div>
                <div style="display: none;">
                    <textarea name="marks_and_nos" id="marks_and_nos">0 Plyboard
Boxes
Nos
01/00
to
00/00</textarea>
                    <textarea name="no_and_kind_of_pkgs" id="no_and_kind_of_pkgs">0 Plyboard
Boxes
Nos
01/00
to
00/00</textarea>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 1.5rem; color: var(--success-color);"><i class="fa-solid fa-bank"></i> Bank & Payment</h3>
                <div class="form-group">
                    <label class="form-label">Bank Details (Germany Standards)</label>
                    <textarea name="bank_details" class="form-control" rows="4">HDFC BANK LTD
SWIFT: HDFCBBBB
IBAN: IN12 3456 7890 1234
Beneficiary: PRECISION STAMPINGS</textarea>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Payment Terms</label>
                    <input type="text" name="payment_terms" class="form-control" value="30 Days Net from BL Date">
                </div>
            </div>
        </div>

        <!-- Right Side: Export Logistics -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.2);">
                <h3 style="margin-bottom: 1.5rem; color: #3b82f6;"><i class="fa-solid fa-ship"></i> Export Logistics</h3>
                <div class="form-group">
                    <label class="form-label">Incoterms</label>
                    <select name="incoterms" class="form-control">
                        <option value="FOB">FOB</option>
                        <option value="CIF Faridabad">CIF Faridabad</option>
                        <option value="EX-Works">EX-Works</option>
                        <option value="DDP">DDP</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Vessel / Flight No.</label>
                    <input type="text" name="vessel_flight_no" class="form-control" placeholder="e.g. Sea" value="Sea">
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Port of Loading</label>
                    <input type="text" name="port_of_loading" class="form-control" value="Mundra / Nhava Sheva">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Port of Discharge</label>
                    <input type="text" name="port_of_discharge" class="form-control" value="Hamburg">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Pre-Carriage by</label>
                    <input type="text" name="pre_carriage_by" class="form-control" value="Road">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Place of Receipt</label>
                    <input type="text" name="place_of_receipt" class="form-control" value="ICD Faridabad">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Country of Origin</label>
                    <input type="text" name="country_of_origin" class="form-control" value="India">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label class="form-label">Country of Final Destination</label>
                    <input type="text" name="country_of_final_destination" class="form-control" value="Germany">
                </div>
            </div>
        </div>
    </div>

    <!-- Full Width: Item Specifications & Grand Total / Submit -->
    <div class="card" style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Item Specifications (Export)</h3>
        <div class="table-responsive" id="itemsTableContainer" style="overflow-x: auto; transition: padding-bottom 0.2s ease;">
            <table class="table" id="itemsTable" style="min-width: 1000px; width: 100%;">
                <colgroup>
                    <col style="width: 30%;">
                    <col style="width: 12%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 8%;">
                    <col style="width: 5%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>HS Code</th>
                        <th>Order No</th>
                        <th>Order Date</th>
                        <th>Quantity</th>
                        <th id="priceLabel">Unit Price (€)</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][item_id]" id="item_select_0" class="form-control item-select" required>
                                <option value="">Select Item</option>
                            </select>
                            <div class="stock-info" style="font-size: 0.75rem; color: var(--success-color); margin-top: 4px;"></div>
                        </td>
                        <td><input type="text" name="items[0][hs_code]" class="form-control" placeholder="HS Code"></td>
                        <td><input type="text" name="items[0][order_number]" class="form-control order-no" placeholder="Order #"></td>
                        <td><input type="date" name="items[0][order_date]" class="form-control order-date"></td>
                        <td><input type="number" step="0.01" name="items[0][quantity]" class="form-control qty" oninput="calculateTotal(this)" required></td>
                        <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control price" oninput="calculateTotal(this)" required></td>
                        <td><span class="row-total">0.00</span></td>
                        <td><button type="button" class="btn btn-sm btn-danger" onclick="const tr = this.closest('tr'); const sel = tr.querySelector('.item-select'); if(sel) itemChoicesMap.delete(sel); tr.remove(); updateGrandTotal();"><i class="fa-solid fa-times"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" onclick="addRow()" style="margin-top: 1rem; width: fit-content;">
            <i class="fa-solid fa-plus"></i> Add Item
        </button>

        <div style="margin-top: 3rem; border-top: 2px solid var(--border-color); padding-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; font-size: 1.5rem; font-weight: bold;">
                <span>Grand Total:</span>
                <span id="grandTotal" style="color: var(--success-color); margin-left: 1rem;">€ 0.00</span>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 14px 36px; font-size: 1.2rem; font-weight: bold;">
                <i class="fa-solid fa-file-export"></i> Generate Export Invoice
            </button>
        </div>
    </div>
</form>

<script>
    let rowCount = 1;
    let currencySymbol = '€';

    function updateCurrencySymbol(val) {
        if(val === 'EUR') currencySymbol = '€';
        else if(val === 'USD') currencySymbol = '$';
        else currencySymbol = '₹';
        
        document.getElementById('priceLabel').innerText = `Unit Price (${currencySymbol})`;
        updateGrandTotal();
    }

    let choicesInstance = null;
    let itemChoicesMap = new Map();

    const masterItems = [
        @foreach($items as $item)
        {
            id: "{{ $item->id }}",
            client_id: "{{ $item->client_id }}",
            stock: "{{ round($item->current_stock) }}",
            order_no: "{{ $item->suggested_order_no }}",
            order_date: "{{ $item->suggested_order_date }}",
            name: {!! json_encode($item->item_code . ' - ' . $item->name) !!}
        },
        @endforeach
    ];

    function handleItemChange(selectEl) {
        const selectedVal = selectEl.value;
        const item = masterItems.find(i => i.id == selectedVal);
        const row = selectEl.closest('tr');
        const stockInfo = row.querySelector('.stock-info');
        
        if (item) {
            const stockNum = Math.round(parseFloat(item.stock)) || 0;
            if (stockNum > 0) {
                stockInfo.innerText = `Available Stock: ${stockNum}`;
            } else {
                stockInfo.innerText = '';
            }
            if (item.order_no) row.querySelector('.order-no').value = item.order_no;
            if (item.order_date) row.querySelector('.order-date').value = item.order_date;

            const customerSelect = document.getElementById('customer_id');
            if (item.client_id && !customerSelect.value) {
                if (choicesInstance) {
                    choicesInstance.setChoiceByValue(item.client_id.toString());
                } else {
                    customerSelect.value = item.client_id;
                }
                filterItemsByCustomer(item.client_id);
            }
        } else {
            stockInfo.innerText = '';
        }
    }

    function initItemSelect(selectEl) {
        const customerId = document.getElementById('customer_id') ? document.getElementById('customer_id').value : '';
        const ch = new Choices(selectEl, {
            searchEnabled: true,
            shouldSort: false,
            itemSelectText: '',
            placeholder: true,
            placeholderValue: 'Select Item'
        });
        itemChoicesMap.set(selectEl, ch);
        selectEl.addEventListener('change', function() { handleItemChange(this); });
        selectEl.addEventListener('showDropdown', function() {
            const container = document.getElementById('itemsTableContainer');
            if(container) container.style.paddingBottom = '120px';
        });
        selectEl.addEventListener('hideDropdown', function() {
            const container = document.getElementById('itemsTableContainer');
            if(container) container.style.paddingBottom = '';
        });

        filterSingleChoices(ch, customerId);
    }

    function filterSingleChoices(chObj, customerId) {
        const filtered = masterItems.filter(i => !customerId || i.client_id == customerId);
        const choicesData = [
            { value: '', label: 'Select Item', placeholder: true, selected: true, disabled: false },
            ...filtered.map(i => ({ value: String(i.id), label: i.name, selected: false }))
        ];
        chObj.clearChoices();
        chObj.setChoices(choicesData, 'value', 'label', true);
    }

    function filterItemsByCustomer(customerId) {
        itemChoicesMap.forEach((choiceObj, selectEl) => {
            const currVal = selectEl.value;
            filterSingleChoices(choiceObj, customerId);
            if (currVal && masterItems.some(i => i.id == currVal && (!customerId || i.client_id == customerId))) {
                choiceObj.setChoiceByValue(String(currVal));
            }
        });
    }

    function addRow() {
        const tbody = document.querySelector('#itemsTable tbody');
        const rows = tbody.querySelectorAll('tr');
        if (rows.length > 0) {
            const lastRow = rows[rows.length - 1];
            const selectEl = lastRow.querySelector('.item-select');
            const qtyEl = lastRow.querySelector('.qty');
            const priceEl = lastRow.querySelector('.price');

            const selVal = selectEl ? selectEl.value : '';

            if (!selVal || selVal === '' || selVal === 'Select Item') {
                alert("Please select an item in the current row first.");
                const choiceWrapper = lastRow.querySelector('.choices');
                if(choiceWrapper) choiceWrapper.focus();
                return;
            }
            const qty = parseFloat(qtyEl.value);
            if (isNaN(qty) || qty <= 0) {
                alert("Please enter a valid Quantity for the selected item before adding a new row.");
                qtyEl.focus();
                return;
            }
            const price = parseFloat(priceEl.value);
            if (isNaN(price) || price <= 0) {
                alert("Please enter a valid Unit Price for the selected item before adding a new row.");
                priceEl.focus();
                return;
            }
        }

        const selectId = `item_select_${rowCount}`;
        const newRow = `
            <tr>
                <td>
                    <select name="items[${rowCount}][item_id]" id="${selectId}" class="form-control item-select" required>
                        <option value="">Select Item</option>
                    </select>
                    <div class="stock-info" style="font-size: 0.75rem; color: var(--success-color); margin-top: 4px;"></div>
                </td>
                <td><input type="text" name="items[${rowCount}][hs_code]" class="form-control"></td>
                <td><input type="text" name="items[${rowCount}][order_number]" class="form-control order-no"></td>
                <td><input type="date" name="items[${rowCount}][order_date]" class="form-control order-date"></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][quantity]" class="form-control qty" oninput="calculateTotal(this)" required></td>
                <td><input type="number" step="0.01" name="items[${rowCount}][unit_price]" class="form-control price" oninput="calculateTotal(this)" required></td>
                <td><span class="row-total">0.00</span></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="const tr = this.closest('tr'); const sel = tr.querySelector('.item-select'); if(sel) itemChoicesMap.delete(sel); tr.remove(); updateGrandTotal();"><i class="fa-solid fa-times"></i></button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
        const newSel = document.getElementById(selectId);
        initItemSelect(newSel);
        rowCount++;
    }

    function calculateTotal(el) {
        const row = el.closest('tr');
        const qty = row.querySelector('.qty').value || 0;
        const price = row.querySelector('.price').value || 0;
        const total = qty * price;
        row.querySelector('.row-total').innerText = total.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('.row-total').forEach(el => {
            grand += parseFloat(el.innerText);
        });
        document.getElementById('grandTotal').innerText = `${currencySymbol} ${grand.toFixed(2)}`;
    }

    function updateBoxNumbers(val) {
        let count = parseInt(val);
        if (isNaN(count) || count < 0) return;
        let padded = String(count).padStart(2, '0');
        let newText = `${count} Plyboard\nBoxes\nNos\n01/${padded}\nto\n${padded}/${padded}`;
        document.getElementById('marks_and_nos').value = newText;
        document.getElementById('no_and_kind_of_pkgs').value = newText;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const custSel = document.getElementById('customer_id');
        if(custSel) choicesInstance = new Choices(custSel);

        document.querySelectorAll('.item-select').forEach((sel, idx) => {
            if(!sel.id) sel.id = `item_select_${idx}`;
            initItemSelect(sel);
        });

        const formEl = document.querySelector('form');
        if(formEl) {
            formEl.addEventListener('submit', function(e) {
                let isValid = true;
                document.querySelectorAll('#itemsTable tbody tr').forEach((tr, idx) => {
                    const sel = tr.querySelector('.item-select');
                    const qty = tr.querySelector('.qty');
                    const price = tr.querySelector('.price');
                    
                    const selVal = sel ? sel.value : '';
                    
                    if (!selVal || selVal === '' || selVal === 'Select Item') {
                        alert(`Row #${idx + 1}: Please select an item.`);
                        const choiceWrapper = tr.querySelector('.choices');
                        if(choiceWrapper) choiceWrapper.focus();
                        isValid = false;
                    } else if (!qty || parseFloat(qty.value) <= 0 || isNaN(parseFloat(qty.value))) {
                        alert(`Row #${idx + 1}: Please enter a valid Quantity.`);
                        if(qty) qty.focus();
                        isValid = false;
                    } else if (!price || parseFloat(price.value) <= 0 || isNaN(parseFloat(price.value))) {
                        alert(`Row #${idx + 1}: Please enter a valid Unit Price.`);
                        if(price) price.focus();
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endsection
