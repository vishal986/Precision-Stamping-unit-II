<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PrecisionStampings ERP') }}</title>

        <!-- Scripts & Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        
        <!-- Font Awesome for Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Choices.js (Searchable Selects) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        
        <style>
            /* Customizing Choices.js to match dark theme - HIGH VISIBILITY */
            .choices {
                color: #ffffff !important;
            }
            .choices__inner {
                background-color: rgba(0, 0, 0, 0.3) !important;
                border: 1px solid rgba(255, 255, 255, 0.3) !important;
                color: #ffffff !important;
                border-radius: 0.5rem !important;
                min-height: 44px !important;
            }
            
            /* HIDE original select which is made visible by accident */
            select.choices__input {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Only target the search input field */
            .choices__list--dropdown .choices__input {
                background-color: #2d2d3f !important;
                color: #ffffff !important;
                border: 1px solid rgba(255,255,255,0.4) !important;
                border-radius: 0.25rem !important;
                padding: 8px !important;
                margin-bottom: 5px !important;
                opacity: 1 !important;
                font-weight: 500 !important;
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .choices__list--dropdown .choices__input::placeholder {
                color: #cccccc !important; 
                opacity: 1 !important;
            }
            
            .choices__list--dropdown, .choices__list[aria-expanded] {
                background-color: #1e1e2d !important;
                border: 1px solid rgba(255, 255, 255, 0.3) !important;
                color: #ffffff !important;
                z-index: 9999 !important;
            }
            .choices.is-active {
                z-index: 9999 !important;
            }
            .choices__list--dropdown {
                z-index: 10000 !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            .choices__list--dropdown .choices__item--selectable.is-highlighted {
                background-color: #3b82f6 !important;
                color: #ffffff !important;
            }
            .choices[data-type*="select-one"]::after {
                border-color: #ffffff transparent transparent transparent !important;
            }
            
            /* Sidebar Submenu */
            .sub-menu {
                list-style: none;
                padding: 0;
                margin: 0;
                display: none;
                background: rgba(0, 0, 0, 0.1);
            }
            .sub-menu.show {
                display: block;
            }
            /* Hiding Spin Buttons for Number Inputs */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
        <script>
            function toggleDropdown(id) {
                var el = document.getElementById(id);
                el.classList.toggle("show");
            }

            // Prevent number input from changing value on scroll but allow page scroll
            document.addEventListener('wheel', function(e) {
                if (document.activeElement.type === 'number') {
                    document.activeElement.blur();
                }
            });

            // Auto-hide alerts after 3 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = ['success-alert', 'error-alert'];
                alerts.forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        setTimeout(function() {
                            el.style.opacity = '0';
                            setTimeout(function() {
                                el.style.display = 'none';
                            }, 500);
                        }, 3000);
                    }
                });
            });
        </script>
    </head>
    <body>
        <div class="app-container">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <i class="fa-solid fa-cube nav-icon" style="color: var(--primary-color);"></i>
                    Precision ERP
                </div>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie nav-icon"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('items*') || request()->is('contacts*') || request()->is('material-in*') || request()->is('departments*') ? 'active' : '' }}" onclick="toggleDropdown('masterDropdown')">
                            <i class="fa-solid fa-database nav-icon"></i>
                            Master Data
                            <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                        </a>
                        <ul id="masterDropdown" class="sub-menu {{ request()->is('items*') || request()->is('contacts*') || request()->is('material-in*') || request()->is('departments*') ? 'show' : '' }}">
                            <li>
                                <a href="/items" class="nav-link {{ request()->is('items*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-box nav-icon"></i>
                                    Items & Inventory
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('departments.index') }}" class="nav-link {{ request()->is('departments*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-building nav-icon"></i>
                                    Departments
                                </a>
                            </li>
                            <li>
                                <a href="/contacts" class="nav-link {{ request()->is('contacts*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-users nav-icon"></i>
                                    Client / Contact
                                </a>
                            </li>
                            <li>
                                <a href="/material-in" class="nav-link {{ request()->is('material-in*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-truck-ramp-box nav-icon"></i>
                                    Raw Material IN
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('export-invoices*') ? 'active' : '' }}" onclick="toggleDropdown('salesDropdown')">
                            <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                            Sales & Export
                            <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                        </a>
                        <ul id="salesDropdown" class="sub-menu {{ request()->is('export-invoices*') ? 'show' : '' }}">
                            <li>
                                <a href="{{ route('export-invoices.index') }}" class="nav-link {{ request()->is('export-invoices*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-file-export nav-icon"></i>
                                    Export Invoices
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('production*') || request()->is('logistics*') ? 'active' : '' }}" onclick="toggleDropdown('prodDropdown')">
                            <i class="fa-solid fa-industry nav-icon"></i>
                            Manufacturing
                            <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                        </a>
                        <ul id="prodDropdown" class="sub-menu {{ request()->is('production*') || request()->is('logistics*') ? 'show' : '' }}">
                            <li>
                                <a href="/production" class="nav-link {{ request()->is('production*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-list-check nav-icon"></i>
                                    Production Orders
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logistics.qc.index') }}" class="nav-link {{ request()->is('logistics/qc*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-clipboard-check nav-icon"></i>
                                    Quality Control
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logistics.inventory') }}" class="nav-link {{ request()->is('logistics/inventory*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-warehouse nav-icon"></i>
                                    FG Inventory
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}" onclick="toggleDropdown('hrDropdown')">
                            <i class="fa-solid fa-id-badge nav-icon"></i>
                            HR & Payroll
                            <i class="fa-solid fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                        </a>
                        <ul id="hrDropdown" class="sub-menu {{ request()->is('employees*') || request()->is('attendance*') || request()->is('leaves*') || request()->is('salary-structures*') || request()->is('advances*') || request()->is('payroll*') ? 'show' : '' }}">
                            <li>
                                <a href="/employees" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-users nav-icon"></i>
                                    Employee Master
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('holidays.index') }}" class="nav-link {{ request()->is('holidays*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-calendar-days nav-icon"></i>
                                    Holiday Calendar
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->is('attendance*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-calendar-check nav-icon"></i>
                                    Daily Attendance
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('biometric.index') }}" class="nav-link {{ request()->is('biometric*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-fingerprint nav-icon"></i>
                                    Biometric Logs
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('gatepasses.index') }}" class="nav-link {{ request()->is('gatepasses*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-door-open nav-icon"></i>
                                    Gatepasses
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('leaves.index') }}" class="nav-link {{ request()->is('leaves*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-person-walking-arrow-right nav-icon"></i>
                                    Leave Applications
                                </a>
                            </li>
                            <li style="margin: 0.5rem 0; border-top: 1px solid rgba(255,255,255,0.1);"></li>
                            <li>
                                <a href="{{ route('salary_structures.index') }}" class="nav-link {{ request()->is('salary-structures*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-money-check-dollar nav-icon"></i>
                                    Salary Structures
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('advances.index') }}" class="nav-link {{ request()->is('advances*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-hand-holding-dollar nav-icon"></i>
                                    Advances & Loans
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->is('payroll*') && !request()->is('payroll-reports*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                                    Monthly Payroll
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('payroll.reports') }}" class="nav-link {{ request()->is('payroll-reports*') ? 'active' : '' }}" style="padding-left: 3rem; font-size: 0.9rem;">
                                    <i class="fa-solid fa-chart-line nav-icon"></i>
                                    Payroll Reports
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="/settings" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear nav-icon"></i>
                            Settings
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content Area -->
            <main class="main-content">
                <!-- Top Header -->
                <header class="top-header">
                    <div class="header-search">
                        <i class="fa-solid fa-search" style="position: absolute; left: 1rem; top: 0.8rem; color: var(--text-secondary);"></i>
                        <input type="text" placeholder="Search everywhere...">
                    </div>
                    <div class="header-actions">
                        <a href="#" style="color: var(--text-secondary); position: relative;">
                            <i class="fa-solid fa-bell" style="font-size: 1.25rem;"></i>
                            <span style="position: absolute; top: -5px; right: -5px; background: var(--danger-color); width: 10px; height: 10px; border-radius: 50%;"></span>
                        </a>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: 1rem; cursor: pointer;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <span style="font-weight: 500; font-size: 0.875rem;">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="page-container animate-fade-in">
                    @if(session('success'))
                        <div id="success-alert" style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); border: 1px solid var(--success-color); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; transition: opacity 0.5s ease;">
                            <i class="fa-solid fa-circle-check" style="font-size: 1.25rem;"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div id="error-alert" style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); border: 1px solid var(--danger-color); padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; transition: opacity 0.5s ease;">
                            <i class="fa-solid fa-circle-exclamation" style="font-size: 1.25rem;"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
