<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CoilController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\DepartmentController;
use App\Models\Coil;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/items-export', [App\Http\Controllers\ItemController::class, 'exportExcel'])->name('items.export');
Route::resource('items', App\Http\Controllers\ItemController::class);
Route::resource('item-categories', App\Http\Controllers\ItemCategoryController::class);
Route::post('/items/{item}/bom', [App\Http\Controllers\BomController::class, 'store'])->name('items.boms.store');
Route::delete('/boms/{bom}', [App\Http\Controllers\BomController::class, 'destroy'])->name('boms.destroy');
Route::get('/contacts-export', [App\Http\Controllers\ContactController::class, 'exportExcel'])->name('contacts.export');
Route::resource('contacts', App\Http\Controllers\ContactController::class);
Route::post('/production/{production}/issue', [App\Http\Controllers\ProductionOrderController::class, 'issueMaterial'])->name('production.issue');
Route::post('/production/{production}/log', [App\Http\Controllers\ProductionOrderController::class, 'storeLog'])->name('production.log');
Route::post('/production/{production}/complete', [App\Http\Controllers\ProductionOrderController::class, 'completeOrder'])->name('production.complete');
Route::resource('production', App\Http\Controllers\ProductionOrderController::class);

Route::get('/settings', [App\Http\Controllers\CompanySettingController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [App\Http\Controllers\CompanySettingController::class, 'update'])->name('settings.update');

// HR & Payroll Routes
Route::resource('employees', App\Http\Controllers\EmployeeController::class);

Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

Route::get('/biometric', [App\Http\Controllers\BiometricController::class, 'index'])->name('biometric.index');
Route::post('/biometric/import', [App\Http\Controllers\BiometricController::class, 'importCsv'])->name('biometric.import');
Route::post('/biometric/process', [App\Http\Controllers\BiometricController::class, 'process'])->name('biometric.process');

Route::resource('leaves', App\Http\Controllers\LeaveController::class)->except(['show', 'edit', 'update', 'destroy']);
Route::post('/leaves/{leaf}/status', [App\Http\Controllers\LeaveController::class, 'updateStatus'])->name('leaves.updateStatus');

Route::get('/salary-structures', [App\Http\Controllers\SalaryStructureController::class, 'index'])->name('salary_structures.index');
Route::post('/salary-structures', [App\Http\Controllers\SalaryStructureController::class, 'store'])->name('salary_structures.store');

Route::resource('advances', App\Http\Controllers\EmployeeAdvanceController::class)->except(['show', 'edit', 'update', 'create']);

Route::get('/payroll', [App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
Route::get('/payroll/hourly', [App\Http\Controllers\PayrollController::class, 'hourlyIndex'])->name('payroll.hourly.index');
Route::post('/payroll/hourly', [App\Http\Controllers\PayrollController::class, 'hourlyStore'])->name('payroll.hourly.store');
Route::post('/payroll/generate', [App\Http\Controllers\PayrollController::class, 'generate'])->name('payroll.generate');
Route::post('/payroll/mark-paid', [App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payroll.markPaid');
Route::get('/payroll/{payroll}/payslip', [App\Http\Controllers\PayrollController::class, 'payslip'])->name('payroll.payslip');
Route::get('/payroll-reports', [App\Http\Controllers\PayrollController::class, 'reports'])->name('payroll.reports');
Route::get('/payroll-reports/pf-ecr', [App\Http\Controllers\PayrollController::class, 'exportPfEcr'])->name('payroll.reports.pf_ecr');
Route::get('/payroll-reports/esi-csv', [App\Http\Controllers\PayrollController::class, 'exportEsiCsv'])->name('payroll.reports.esi_csv');
Route::get('/payroll-reports/bank-statement', [App\Http\Controllers\PayrollController::class, 'exportBankStatement'])->name('payroll.reports.bank_statement');
Route::get('/payroll-reports/pdf', [App\Http\Controllers\PayrollController::class, 'exportPdfReport'])->name('payroll.reports.pdf');

Route::get('/gatepasses/{gatepass}/print', [App\Http\Controllers\GatepassController::class, 'print'])->name('gatepasses.print');
Route::resource('gatepasses', App\Http\Controllers\GatepassController::class)->except(['create', 'edit', 'show']);

Route::resource('holidays', App\Http\Controllers\HolidayController::class)->except(['create', 'edit', 'show']);

// Logistics & Export
Route::get('/logistics/qc', [App\Http\Controllers\LogisticsController::class, 'qcIndex'])->name('logistics.qc.index');
Route::post('/logistics/qc', [App\Http\Controllers\LogisticsController::class, 'qcStore'])->name('logistics.qc.store');
Route::get('/logistics/inventory', [App\Http\Controllers\LogisticsController::class, 'inventory'])->name('logistics.inventory');

Route::get('/export-invoices/{exportInvoice}/print', [App\Http\Controllers\ExportInvoiceController::class, 'print'])->name('export-invoices.print');
Route::resource('export-invoices', App\Http\Controllers\ExportInvoiceController::class);

Route::get('/orders/create', [OrderController::class, 'ordersPage']);
Route::post('/order-save', [OrderController::class, 'orderSubmit']);
Route::get('/orders-data', [OrderController::class, 'viewOrders']);
Route::get('/edit-orders', [OrderController::class, 'editOrder']);
Route::get('/delete-orders/{order_id}', [OrderController::class, 'deleteOrder']);
Route::get('/material-in', [CoilController::class, 'materialIN']);
Route::post('/store/material', [CoilController::class, 'save']);
Route::get('/coil-data', [CoilController::class, 'viewCoils'])->name('coil.data');
Route::get('/delete-coil/{id}', [CoilController::class, 'deleteCoil']);
Route::get('/edit-coil/{id}', [CoilController::class, 'editCoil']);
// Issue Coil
Route::get('/issue-coil', [IssueController::class, 'create']);
Route::post('/issue-coil-save', [IssueController::class, 'store']);

// Edit Issue
Route::get('/issue-edit/{id}', [IssueController::class, 'edit']);
Route::post('/issue-update/{id}', [IssueController::class, 'update']);

// Delete Issue
Route::get('/issue-delete/{id}', [IssueController::class, 'delete']);

// Report
Route::get('/issue-report', [IssueController::class, 'report']);
Route::get('/issue-report-pdf', [IssueController::class, 'reportPdf']);

//Route::get('/issue/create', [IssueController::class, 'create']);

// Department Master
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');




