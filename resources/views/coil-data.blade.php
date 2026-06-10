@extends('layouts.app')

@section('title')
    All Coils
@endsection
@section('css')
    <style>
        .column-search {
            display: none;
        }
    </style>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title">Coil Stock</h1>
    <a href="{{ url('/material-in') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add New Coil
    </a>
</div>

<div class="card">
    @if (session('success'))
        <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--success-color); color: var(--success-color); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;" id="alertSuccess">
            <strong>{{ session('success') }}</strong>
        </div>
        <script>
            setTimeout(() => {
                let a = document.getElementById("alertSuccess");
                if (a) a.style.display = "none";
            }, 3000);
        </script>
    @endif

    <div class="table-container">
        <table class="table" id="coilTable">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">
                        <button id="filterToggleBtn" class="btn" style="background: rgba(255,255,255,0.1); padding: 0.2rem 0.5rem; font-size: 0.8rem;" title="Search columns">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </th>
                    <th>Material Name</th>
                    <th>Coil Number</th>
                    <th>Job Size</th>
                    <th>Grade</th>
                    <th>Quantity</th>
                    <th>Total Weight</th>
                    <th>Remaining Weight</th>
                    <th>Actions</th>
                </tr>
                <tr id="searchRow" style="display: none; background: rgba(0,0,0,0.2);">
                    <th></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="1" placeholder="Search..."></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="2" placeholder="Search..."></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="3" placeholder="Search..."></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="4" placeholder="Search..."></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="5" placeholder="Search..."></th>
                    <th><input type="text" class="form-control column-search" style="padding: 0.25rem 0.5rem; min-height: 30px; font-size: 0.8rem;" data-col="6" placeholder="Search..."></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coils as $index => $coil)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: 500;">{{ $coil->coil_name }}</td>
                        <td>{{ $coil->coil_num }}</td>
                        <td>{{ $coil->job_size }}</td>
                        <td>{{ $coil->grade }}</td>
                        <td>{{ $coil->quantity }}</td>
                        <td>
                            <div>{{ $coil->weight_value }} {{ $coil->weight_unit }}</div>
                        </td>
                        <td>
                            @php
                                $dispRemaining = $coil->remaining_weight;
                                if($coil->weight_unit == 'mt') $dispRemaining = $coil->remaining_weight / 1000;
                                elseif($coil->weight_unit == 'g') $dispRemaining = $coil->remaining_weight * 1000;
                            @endphp
                            <span style="font-weight: 600; color: {{ $dispRemaining <= 0 ? 'var(--danger-color)' : 'var(--success-color)' }}">
                                {{ floatval($dispRemaining) }} {{ $coil->weight_unit }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ url('/edit-coil/' . $coil->id) }}" style="color: #f59e0b;" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ url('/delete-coil/' . $coil->id) }}" style="color: var(--danger-color);" onclick="return confirm('Are you sure you want to delete this coil?');" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No coils found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


    {{-- JS Search --}}
    <script>
        const searchInputs = document.querySelectorAll(".column-search");

        searchInputs.forEach(input => {
            input.addEventListener("keyup", function() {

                let colIndex = parseInt(this.getAttribute("data-col"));
                let filter = this.value.toLowerCase();

                let table = document.getElementById("coilTable");
                let rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

                for (let i = 0; i < rows.length; i++) {

                    let cell = rows[i].getElementsByTagName("td")[colIndex];

                    if (cell) {
                        let text = (cell.textContent || cell.innerText).toLowerCase();

                        // SHOW row if match OR ALL searches empty
                        if (text.indexOf(filter) > -1) {
                            rows[i].style.display = "";
                        } else {
                            rows[i].style.display = "none";
                        }
                    }

                }
            });
        });
    </script>
    <script>
        const toggleBtn = document.getElementById("filterToggleBtn");
        const searchRow = document.getElementById("searchRow");
        let searchVisible = false;

        toggleBtn.addEventListener("click", function() {
            searchVisible = !searchVisible;
            searchRow.style.display = searchVisible ? "table-row" : "none";
        });
    </script>
@endsection
