@extends('layouts.app')

@section('content')
    <div class="container">

        <!-- Display success/error messages -->
        @if (session('success'))
            <div class="alert alert-success" id="sessionMsg">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" id="sessionMsg">{{ session('error') }}</div>
        @endif

        <h3 class="mb-4">Issue Coil to Department</h3>

        <form action="{{ url('/issue-coil-save') }}" method="POST" id="issueForm">
            @csrf

            <!-- Searchable Coil Input -->
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Search & Select Coil</label>
                <select name="coil_id" id="coil_id" class="form-control" required>
                    <option value="">Search Coil...</option>
                    @foreach ($coils as $coil)
                        <option value="{{ $coil->id }}" data-remaining="{{ $coil->remaining_weight }}"
                            data-unit="{{ $coil->weight_unit }}">
                            {{ $coil->coil_num }} — {{ $coil->coil_name }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text" id="availableWeight">Available: 0</div>
                @error('coil_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Department -->
            <div class="col-md-4 mb-3">
                <label>Department</label>
                <select name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('department_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Issued Weight -->
            <div class="col-md-4 mb-3">
                <label>Issued Weight</label>
                <input type="number" name="issued_weight" step="0.001" class="form-control" id="issuedWeight" required>
                @error('issued_weight')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Issue Date -->
            <div class="col-md-4 mb-3">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" required>
                @error('issue_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Issued By -->
            <div class="col-md-4 mb-3">
                <label>Issued By</label>
                <input type="text" name="issued_by" class="form-control">
            </div>

            <!-- Remarks -->
            <div class="col-md-6 mb-3">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-25">Issue Coil</button>
        </form>
    </div>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#coil_id').select2({
                placeholder: "Search & select coil...",
                width: '100%'
            });

            // Show available weight in its DB unit when coil is selected
            $('#coil_id').on('change', function() {
                let selected = $('#coil_id option:selected');
                let remaining = parseFloat(selected.data('remaining')) || 0;
                let unit = selected.data('unit') || 'MT';

                $('#availableWeight').text('Available: ' + remaining.toFixed(3) + ' ' + unit);
                $('#issuedWeight').attr('max', remaining.toFixed(3));
            });

            // Client-side validation for issued weight
            $('#issuedWeight').on('input', function() {
                let max = parseFloat($(this).attr('max')) || 0;
                let val = parseFloat($(this).val()) || 0;
                if (val > max) {
                    alert('Issued weight cannot exceed available stock!');
                    $(this).val(max);
                }
            });

            // Hide session messages after 5 sec
            setTimeout(function() {
                let msg = document.getElementById('sessionMsg');
                if (msg) msg.style.display = 'none';
            }, 5000);
        });
    </script>
@endsection
