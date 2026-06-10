<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\Payroll::where('month', '05')->where('year', '2026')->count();
echo "May 2026 total payrolls: " . $count . "\n";
