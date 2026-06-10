<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$prs = \App\Models\Payroll::where('pf_deduction', '>', 1800)->get();
echo "Records with PF > 1800: " . $prs->count() . "\n";
foreach($prs as $pr) {
    echo "Emp ID: {$pr->employee_id}, PF: {$pr->pf_deduction}, Month: {$pr->month}/{$pr->year}\n";
}
