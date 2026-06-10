<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pr = \App\Models\Payroll::where('employee_id', 7)->first();
echo "Emp ID: " . $pr->employee_id . "\n";
echo "Income Tax: " . $pr->income_tax . "\n";
echo "Condition: " . ($pr && $pr->income_tax > 0 ? 'true' : 'false') . "\n";
echo "Value: " . ($pr && $pr->income_tax > 0 ? floatval($pr->income_tax) : '') . "\n";
