<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/payroll/hourly', 'POST', [
    'days' => [10 => ''],
    'hours' => [10 => '']
]);
$request->headers->set('Accept', 'application/json');

$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'days' => 'nullable|array',
    'days.*' => 'nullable|numeric|min:0',
    'hours' => 'nullable|array',
    'hours.*' => 'nullable|numeric|min:0',
]);

$validated = $validator->validated();
print_r($validated);
