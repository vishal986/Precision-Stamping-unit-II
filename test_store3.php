<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/payroll/hourly', 'POST', [
    'month' => '05',
    'year' => '2026',
    'days' => [10 => null],
    'hours' => [10 => null],
    'advances' => [10 => 1500],
    'income_tax' => [10 => 500]
]);

$controller = $app->make(\App\Http\Controllers\PayrollController::class);
try {
    $response = $controller->hourlyStore($request);
    echo "Saved successfully.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$pr = \App\Models\Payroll::where('employee_id', 10)->where('month', '05')->where('year', '2026')->first();
if ($pr) {
    echo "Emp 10 - Advance: {$pr->advance_deduction}, Tax: {$pr->income_tax}\n";
} else {
    echo "Emp 10 Payroll record NOT found.\n";
}
