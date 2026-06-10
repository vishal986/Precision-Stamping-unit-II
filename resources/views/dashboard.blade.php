@extends('layouts.app')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Overview</h1>
    <button class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> New Quick Action
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Stat Card 1 -->
    <div class="card" style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.2); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-box"></i>
        </div>
        <div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Total Inventory</div>
            <div style="font-size: 1.5rem; font-weight: 700;">1,245 <span style="font-size: 0.875rem; color: var(--success-color);"><i class="fa-solid fa-arrow-up"></i> 12%</span></div>
        </div>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="card" style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.2); color: var(--success-color); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Monthly Sales</div>
            <div style="font-size: 1.5rem; font-weight: 700;">$45,230 <span style="font-size: 0.875rem; color: var(--success-color);"><i class="fa-solid fa-arrow-up"></i> 8%</span></div>
        </div>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="card" style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.2); color: var(--danger-color); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-exclamation-triangle"></i>
        </div>
        <div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Low Stock Items</div>
            <div style="font-size: 1.5rem; font-weight: 700;">14 <span style="font-size: 0.875rem; color: var(--danger-color);"><i class="fa-solid fa-arrow-down"></i> Action Req.</span></div>
        </div>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="card" style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(168, 85, 247, 0.2); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-industry"></i>
        </div>
        <div>
            <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Active Production</div>
            <div style="font-size: 1.5rem; font-weight: 700;">8 <span style="font-size: 0.875rem; color: var(--text-secondary);">Orders</span></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.125rem;">Recent Sales Orders</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="#">SO-10024</a></td>
                        <td>Acme Corp</td>
                        <td><span style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Shipped</span></td>
                        <td>$1,250.00</td>
                    </tr>
                    <tr>
                        <td><a href="#">SO-10025</a></td>
                        <td>Global Tech</td>
                        <td><span style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">Processing</span></td>
                        <td>$3,400.00</td>
                    </tr>
                    <tr>
                        <td><a href="#">SO-10026</a></td>
                        <td>Stark Industries</td>
                        <td><span style="background: rgba(59, 130, 246, 0.2); color: var(--primary-color); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">New</span></td>
                        <td>$850.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.125rem;">Quick Links</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="{{ url('/items') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: var(--border-radius); transition: background var(--transition-fast);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="color: var(--primary-color);"><i class="fa-solid fa-plus-circle fa-lg"></i></div>
                    <div style="font-weight: 500; color: var(--text-primary);">Create Item</div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
            </a>
            <a href="{{ url('/export-invoices') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: var(--border-radius); transition: background var(--transition-fast);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="color: var(--success-color);"><i class="fa-solid fa-file-invoice fa-lg"></i></div>
                    <div style="font-weight: 500; color: var(--text-primary);">New Invoice</div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
            </a>
            <a href="{{ url('/production') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: var(--border-radius); transition: background var(--transition-fast);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="color: #a855f7;"><i class="fa-solid fa-clipboard-list fa-lg"></i></div>
                    <div style="font-weight: 500; color: var(--text-primary);">Start Production</div>
                </div>
                <i class="fa-solid fa-chevron-right" style="color: var(--text-secondary); font-size: 0.875rem;"></i>
            </a>
        </div>
    </div>
</div>
@endsection
