<?php
echo "<h2>🔍 Environment Variables Debug</h2>";

echo "<h3>All Environment Variables:</h3>";
echo "<pre>";
foreach ($_ENV as $key => $value) {
    // Hide sensitive values partially
    if (strpos(strtolower($key), 'password') !== false || strpos(strtolower($key), 'secret') !== false) {
        $displayValue = substr($value, 0, 4) . '***' . substr($value, -4);
    } else {
        $displayValue = $value;
    }
    echo "$key = $displayValue\n";
}
echo "</pre>";

echo "<h3>MySQL Related Variables:</h3>";
echo "<pre>";
$mysqlVars = array_filter($_ENV, function($key) {
    return stripos($key, 'mysql') !== false || stripos($key, 'database') !== false;
}, ARRAY_FILTER_USE_KEY);

foreach ($mysqlVars as $key => $value) {
    if (strpos(strtolower($key), 'password') !== false) {
        $displayValue = substr($value, 0, 4) . '***' . substr($value, -4);
    } else {
        $displayValue = $value;
    }
    echo "$key = $displayValue\n";
}
echo "</pre>";

echo "<h3>Railway Variables:</h3>";
echo "<pre>";
$railwayVars = array_filter($_ENV, function($key) {
    return stripos($key, 'railway') !== false;
}, ARRAY_FILTER_USE_KEY);

foreach ($railwayVars as $key => $value) {
    echo "$key = $value\n";
}
echo "</pre>";
?>
