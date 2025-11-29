<?php
/**
 * Setup script for ReckNap Report POC
 * This script creates the database and populates it with sample data
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'recknap_reports';

echo "🚀 ReckNap Report POC Setup\n";
echo "==========================\n\n";

try {
    // Connect to MySQL server (without database)
    echo "1. Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ Connected successfully\n\n";
    
    // Create database
    echo "2. Creating database '$database'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "   ✅ Database created successfully\n\n";
    
    // Connect to the specific database
    echo "3. Connecting to database '$database'...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ Connected to database successfully\n\n";
    
    // Execute schema
    echo "4. Creating database schema...\n";
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema === false) {
        throw new Exception("Could not read schema.sql file");
    }
    
    // Split and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    echo "   ✅ Schema created successfully\n\n";
    
    // Execute sample data
    echo "5. Inserting sample data...\n";
    $sampleData = file_get_contents(__DIR__ . '/database/sample_data.sql');
    if ($sampleData === false) {
        throw new Exception("Could not read sample_data.sql file");
    }
    
    // Split and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sampleData)));
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    echo "   ✅ Sample data inserted successfully\n\n";
    
    // Verify installation
    echo "6. Verifying installation...\n";
    
    // Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $expectedTables = [
        'report_fields',
        'customers',
        'products',
        'invoices',
        'invoice_items',
        'payments',
        'memos',
        'report_configurations'
    ];
    
    $missingTables = array_diff($expectedTables, $tables);
    if (!empty($missingTables)) {
        throw new Exception("Missing tables: " . implode(', ', $missingTables));
    }
    
    // Check data
    $counts = [];
    foreach ($expectedTables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $counts[$table] = $count;
    }
    
    echo "   📊 Table Statistics:\n";
    foreach ($counts as $table => $count) {
        echo "      - $table: $count records\n";
    }
    echo "\n";
    
    echo "✅ Setup completed successfully!\n\n";
    
    echo "📋 Next Steps:\n";
    echo "1. Install Composer dependencies:\n";
    echo "   composer install\n\n";
    echo "2. Configure your web server to point to the 'webroot' directory\n\n";
    echo "3. Access the application at: http://localhost/your-path/\n\n";
    echo "4. Default database configuration:\n";
    echo "   - Host: $host\n";
    echo "   - Database: $database\n";
    echo "   - Username: $username\n";
    echo "   - Password: " . (empty($password) ? '(empty)' : '(set)') . "\n\n";
    
    echo "🎯 Sample Reports Available:\n";
    echo "- Customer Report (8 customers)\n";
    echo "- Invoice Summary (10 invoices)\n";
    echo "- Aging Report (overdue analysis)\n";
    echo "- Payment Collection (5 payments)\n";
    echo "- Product Catalog (10 products)\n\n";
    
    echo "🔧 Features Ready:\n";
    echo "- ✅ Dynamic field selection\n";
    echo "- ✅ Drag & drop field ordering\n";
    echo "- ✅ Excel export with PhpSpreadsheet\n";
    echo "- ✅ Report configuration save/load\n";
    echo "- ✅ Real-time report preview\n";
    echo "- ✅ Field management (add/edit/delete)\n";
    echo "- ✅ Multiple data types support\n";
    echo "- ✅ Security validation\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nSetup failed. Please check your database configuration and try again.\n";
    exit(1);
}
?>
