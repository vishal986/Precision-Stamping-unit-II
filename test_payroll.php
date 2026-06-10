<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pr = \App\Models\Payroll::first();
if ($pr) {
    echo "PR exists. Employee ID: " . $pr->employee_id . "\n";
    echo "Present Days: " . ($pr->present_days ?? 'null or not set') . "\n";
    echo "Worked Hours: " . ($pr->worked_hours ?? 'null or not set') . "\n";
} else {
    echo "No PR found.";
}
