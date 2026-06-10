<?php
require_once __DIR__ . '/SimpleXLSX.php';

if ($xlsx = Shuchkin\SimpleXLSX::parse('Items data.xlsx')) {
    $rows = $xlsx->rows();
    echo "Total rows: " . count($rows) . "\n";
    print_r(array_slice($rows, 0, 5));
} else {
    echo Shuchkin\SimpleXLSX::parseError();
}
