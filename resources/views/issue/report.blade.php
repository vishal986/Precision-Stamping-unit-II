@extends('layouts.app')

@section('content')
    <div class="container">

        <h3 class="mb-3">Issued Coil Reports</h3>

        <a href="/issue-report-pdf" class="btn btn-danger mb-3">Download PDF</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Coil No</th>
                    <th>Department</th>
                    <th>Issued Weight</th>
                    <th>Issue Date</th>
                    <th>Issued By</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($issues as $issue)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Coil may be soft deleted --}}
                        <td>{{ $issue->coil->coil_num ?? 'Deleted Coil' }}</td>

                        <td>{{ $issue->department->name ?? '-' }}</td>

                        {{-- issued_weight already in MT --}}
                        <td>{{ number_format($issue->issued_weight, 3) }} MT</td>

                        <td>{{ $issue->issue_date }}</td>
                        <td>{{ $issue->issued_by }}</td>
                        <td>{{ $issue->remarks }}</td>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
