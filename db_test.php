<?php
$start = microtime(true);
try {
    $pdo1 = new PDO('mysql:host=127.0.0.1;port=3306;dbname=precision_stamping', 'root', '');
    echo "127.0.0.1 Connected in " . (microtime(true) - $start) . " seconds\n";
} catch (PDOException $e) {
    echo "127.0.0.1 Error: " . $e->getMessage() . "\n";
}

$start = microtime(true);
try {
    $pdo2 = new PDO('mysql:host=localhost;port=3306;dbname=precision_stamping', 'root', '');
    echo "localhost Connected in " . (microtime(true) - $start) . " seconds\n";
} catch (PDOException $e) {
    echo "localhost Error: " . $e->getMessage() . "\n";
}
