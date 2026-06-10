<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pr = \App\Models\Payroll::where('employee_id', 2)->where('month', '05')->first();
echo "Emp ID: " . $pr->employee_id . "\n";
echo "Advance: " . $pr->advance_deduction . "\n";
echo "Condition (> 0): " . ($pr && $pr->advance_deduction > 0 ? 'true' : 'false') . "\n";
echo "Value output: " . ($pr && $pr->advance_deduction > 0 ? floatval($pr->advance_deduction) : '') . "\n";
