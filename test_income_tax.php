<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$count = \App\Models\Payroll::where('income_tax', '>', 0)->count();
echo "Number of payrolls with income_tax > 0: " . $count . "\n";

$pr = \App\Models\Payroll::where('income_tax', '>', 0)->first();
if ($pr) {
    echo "Emp ID: " . $pr->employee_id . ", Income Tax: " . $pr->income_tax . "\n";
}
