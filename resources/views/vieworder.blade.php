@extends ('layouts.app');

@section('title')
    View all Orders
@endsection

@section('content')
    <div class="container ml-4" style="overflow-x:auto;">

        @if (session('success'))
            <div id="alert_delete_order"class="alert alert-danger" role="alert">
                <strong>{{ session('success') }}</strong>
            </div>
        @endif

        <p class="order h2 text-center">All Customer Orders</p>

        <table class="table table-striped table-inverse table-responsive w-100">
            <thead class="thead-inverse">
                <tr>
                    <th>Customer Name
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(0)">
                    </th>
                    <th>Order Number
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(1)">
                    </th>
                    <th>Order Date
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(2)">
                    </th>
                    <th>Item Name
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(3)">
                    </th>
                    <th>Article Number
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(4)">
                    </th>
                    <th>Quantity
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(5)">
                    </th>
                    <th>Delivery Week
                        <input type="text" class="form-control form-control-sm mt-1" placeholder="Search ..."
                            onkeyup="filterTable(6)">
                    </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderdata as $row)
                    <tr>
                        <td scope="row" class="custName">{{ $row->custumer_name }}</td>
                        <td>{{ $row->order_number }}</td>
                        <td>{{ $row->order_date }}</td>
                        <td>{{ $row->item_name }}</td>
                        <td>{{ $row->article_number }}</td>
                        <td>{{ $row->quantity }}</td>
                        <td>{{ $row->delivery_week }}</td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center gap-0.5">
                                <a href="{{ url(
                                    '/orders/create?order_id=' .
                                        $row->order_id .
                                        '&custumer_name=' .
                                        $row->custumer_name .
                                        '&order_number=' .
                                        $row->order_number .
                                        '&order_date=' .
                                        $row->order_date .
                                        '&item_name=' .
                                        $row->item_name .
                                        '&article_number=' .
                                        $row->article_number .
                                        '&quantity=' .
                                        $row->quantity .
                                        '&delivery_week=' .
                                        $row->delivery_week,
                                ) }}"
                                    onclick="return confirm('Do you want to edit this order?')"
                                    class="fa fa-edit fa-lg text-decoration-none"></a>
                                <a href="{{ url('/delete-orders/' . $row->order_id) }}"onclick="return confirm('Do you want to delete this order?')"
                                    class="fa fa-trash text-danger fa-lg mx-3 text-decoration-none"></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        setTimeout(function() {
            let alertBox = document.getElementById('alert_delete_order');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500); // remove after fade-out
            }
        }, 3000); // 3 seconds

        function filterTable(colIndex) {
            let input = document.querySelectorAll("thead input")[colIndex];
            let filter = input.value.toLowerCase();
            let table = document.querySelector("table tbody");
            let rows = table.getElementsByTagName("tr");

            for (let r = 0; r < rows.length; r++) {
                let cell = rows[r].getElementsByTagName("td")[colIndex];
                if (cell) {
                    let text = cell.textContent.toLowerCase();
                    rows[r].style.display = text.includes(filter) ? "" : "none";
                }
            }
        }
    </script>
@endsection
