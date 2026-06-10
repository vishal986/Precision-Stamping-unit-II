<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\Payroll::where('month', '05')->where('year', '2026')
    ->where(function($q) {
        $q->where('advance_deduction', '>', 0)->orWhere('income_tax', '>', 0);
    })->count();

echo "May 2026 count of advance or income_tax > 0: " . $count . "\n";
