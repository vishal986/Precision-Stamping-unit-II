@extends('layouts.app');

@section('title')
    Orders
@endsection

@section('content')
    <!--customer orders form starts-->
    <div class="container mt-4">
        <div class="order-card col-md mx-auto">

            <div class="form-title h2 mb-5 fw-bold">Customer Order Form</div>

            <form method="post" action="{{ url('/order-save') }}" id="orderForm" class="">
                @csrf
                <!-- Customer Name -->
                <div class="mb-3">
                    <input type="hidden" class="form-control" name="order_id" id="orderId"
                        value="{{ request()->order_id }}">
                </div>
                <div class="row">
                    <!-- Customer Name -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Customer Name</label>
                        <input type="text" class="form-control" name="custumer_name" id="customerName"
                            value="{{ request()->custumer_name }}">
                        <div class="invalid-feedback">Customer name is required.</div>
                    </div>

                    <!-- Item Name -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Order Number</label>
                        <input type="text" class="form-control" name="order_number"
                            id="orderNumber"value="{{ request()->order_number }}">
                        <div class="invalid-feedback">Order number is required.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Order date</label>
                        <input type="date" class="form-control" name="order_date" id="orderDate"
                            value="{{ request()->orderdate }}">
                        <div class="invalid-feedback">Order date is required.</div>
                    </div>
                </div>
                <div class="row">
                    <!-- Item Name -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Item Name</label>
                        <input type="text" class="form-control" name="item_name" id="itemName"
                            value="{{ request()->item_name }}">
                        <div class="invalid-feedback">Item name is required.</div>
                    </div>

                    <!-- Item article number -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Article Number</label>
                        <input type="text" class="form-control" name="article_number" id="articleNumber"
                            value="{{ request()->article_number }}">
                        <div class="invalid-feedback">Article number is required.</div>
                    </div>

                    <!-- Quantity -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" min="1"
                            value="{{ request()->quantity }}">
                        <div class="invalid-feedback">Enter a valid quantity.</div>
                    </div>
                </div>

                <!-- Delivery Week -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Delivery Week</label>
                    <input type="week" class="form-control" name="delivery_week" id="deliveryWeek"
                        value="{{ request()->delivery_week }}">
                    <div class="invalid-feedback">Delivery week is required.</div>
                </div>
                <!-- Button -->
                @if (request()->order_id)
                    <button type="submit" class="btn btn-success w-25 mt-3">Update Order</button>
                @else
                    <button type="submit" class="btn btn-primary w-25 mt-3">Save Order</button>
                @endif


            </form>
        </div>
    </div>


@section('script')
    <!-- Bootstrap & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('orderForm');

            // Function to validate fields
            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Select all input and week/date fields
                const fields = form.querySelectorAll(
                    "input[type=text], input[type=number], input[type=week], input[type=date]");

                fields.forEach(field => {
                    if (field.value.trim() === "") {
                        field.classList.add("is-invalid");
                        field.classList.remove("is-valid");
                        isValid = false;
                    } else {
                        field.classList.remove("is-invalid");
                        field.classList.add("is-valid");
                    }
                });

                if (!isValid) {
                    event.preventDefault(); // stop form submission
                    event.stopPropagation();
                    return; // stop alert from running
                }

                // Only runs if all fields are filled
                let customer = document.getElementById("customerName").value;
                let ordernumber = document.getElementById("orderNumber").value;
                let orderdate = document.getElementById("orderDate").value;
                let item = document.getElementById("itemName").value;
                let article = document.getElementById("articleNumber").value;
                let qty = document.getElementById("quantity").value;
                let week = document.getElementById("deliveryWeek").value;

                alert("Order Saved Successfully!\n\n" +
                    "Customer: " + customer + "\n" +
                    "Order Number: " + ordernumber + "\n" +
                    "Order Date: " + orderdate + "\n" +
                    "Item: " + item + "\n" +
                    "Article: " + article + "\n" +
                    "Quantity: " + qty + "\n" +
                    "Delivery Week: " + week);
            });

            // Remove red border on typing
            const fields = form.querySelectorAll(
                "input[type=text], input[type=number], input[type=week], input[type=date]");
            fields.forEach(field => {
                field.addEventListener("input", function() {
                    if (this.value.trim() !== "") {
                        this.classList.remove("is-invalid");
                        this.classList.add("is-valid");
                    } else {
                        this.classList.remove("is-valid");
                    }
                });
            });
        });
    </script>

    <script>
        @if (session('success'))
            alert("{{ session('success') }}");
        @endif
    </script>
@endsection

<!--customer orders form end-->
@endsection
