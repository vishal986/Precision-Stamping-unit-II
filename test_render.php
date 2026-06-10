<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/payroll/hourly?month=05&year=2026', 'GET');
$controller = $app->make(\App\Http\Controllers\PayrollController::class);
$response = $controller->hourlyIndex($request);

$html = $response->render();

if (strpos($html, 'name="advances[2]"') !== false) {
    preg_match('/name="advances\[2\]"[^>]*value="([^"]*)"/', $html, $matches);
    echo "Advance value for Emp 2: " . ($matches[1] ?? 'NOT FOUND') . "\n";
} else {
    echo "Emp 2 not found in HTML\n";
}
