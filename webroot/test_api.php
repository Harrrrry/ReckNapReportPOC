<?php
header('Content-Type: application/json');

echo "🔍 API Endpoint Test\n";
echo "===================\n\n";

try {
    // Test database connection
    $pdo = new PDO('mysql:host=localhost;dbname=recknap_reports', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection: OK\n";
    
    // Test report_fields table
    $stmt = $pdo->query("SELECT COUNT(*) FROM report_fields WHERE active = 1");
    $count = $stmt->fetchColumn();
    echo "✅ Active report fields: $count\n\n";
    
    if ($count > 0) {
        echo "📋 Sample Fields:\n";
        $stmt = $pdo->query("SELECT field_key, label, table_name, column_name FROM report_fields WHERE active = 1 LIMIT 5");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "   • {$row['label']} ({$row['table_name']}.{$row['column_name']})\n";
        }
        
        echo "\n🎯 API Response Test:\n";
        
        // Simulate the API response
        $fields = [];
        $stmt = $pdo->query("SELECT * FROM report_fields WHERE active = 1 ORDER BY sort_order");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fields[] = [
                'id' => $row['id'],
                'field_key' => $row['field_key'],
                'label' => $row['label'],
                'table_name' => $row['table_name'],
                'column_name' => $row['column_name'],
                'data_type' => $row['data_type'],
                'field_type' => $row['field_type'],
                'sort_order' => $row['sort_order']
            ];
        }
        
        $response = ['success' => true, 'data' => $fields];
        echo json_encode($response, JSON_PRETTY_PRINT);
        
    } else {
        echo "❌ No active fields found in database\n";
        echo "💡 Run 'php setup.php' to create sample fields\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
