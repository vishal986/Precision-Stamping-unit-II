<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$prs = \App\Models\Payroll::where('month', '05')->where('year', '2026')
    ->where(function($q) {
        $q->where('advance_deduction', '>', 0)->orWhere('income_tax', '>', 0);
    })->get();

foreach($prs as $pr) {
    echo "Emp ID: {$pr->employee_id}, Advance: {$pr->advance_deduction}, Tax: {$pr->income_tax}, Status: {$pr->status}\n";
}
