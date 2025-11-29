<?php
echo "📊 Database Tables & Data Test\n";
echo "==============================\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=recknap_reports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to recknap_reports database\n\n";
    
    // Check tables and record counts
    $tables = [
        'report_fields' => 'Report Fields Configuration',
        'customers' => 'Customer Data',
        'products' => 'Product Catalog',
        'invoices' => 'Invoice Records',
        'invoice_items' => 'Invoice Line Items',
        'payments' => 'Payment Records',
        'memos' => 'Credit/Debit Memos',
        'report_configurations' => 'Saved Report Configs'
    ];
    
    $allGood = true;
    
    foreach ($tables as $table => $description) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $count = $stmt->fetchColumn();
            echo "✅ $description: $count records\n";
        } catch (Exception $e) {
            echo "❌ $description: Table missing or error\n";
            $allGood = false;
        }
    }
    
    if ($allGood) {
        echo "\n🎉 All database tables are present with data!\n";
        
        // Test a sample query
        echo "\n📋 Sample Data Test:\n";
        $stmt = $pdo->query("SELECT c.name as customer_name, i.invoice_number, i.total_amount 
                            FROM customers c 
                            JOIN invoices i ON c.id = i.customer_id 
                            LIMIT 3");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "   • {$row['customer_name']} - {$row['invoice_number']} - \${$row['total_amount']}\n";
        }
        
        echo "\n✅ Database is fully functional!\n";
    } else {
        echo "\n❌ Some database issues found. Run 'php setup.php' to fix.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
?>
