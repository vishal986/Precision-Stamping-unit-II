<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\Payroll::where('month', '06')->where('year', '2026')->where('income_tax', '>', 0)->count();
echo "June 2026 count of income_tax > 0: " . $count . "\n";
