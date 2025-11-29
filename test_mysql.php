<?php
echo "🔍 MySQL Connection Test\n";
echo "========================\n";

try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    echo "✅ SUCCESS - MySQL is running and accessible!\n";
    echo "📊 MySQL Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch(Exception $e) {
    echo "❌ FAILED - MySQL connection error:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "💡 Solutions:\n";
    echo "1. Start Laragon services (Apache & MySQL)\n";
    echo "2. Check if MySQL is running on port 3306\n";
    echo "3. Verify MySQL credentials (default: root with no password)\n";
}
?>
