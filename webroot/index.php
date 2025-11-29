<?php
/**
 * Index file for ReckNap Report POC
 * CakePHP entry point
 */

// Define path constants
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('APP_DIR', 'app');
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');

// Include CakePHP bootstrap (if using CakePHP framework)
// For this POC, we'll create a simple bootstrap

// Set error reporting
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

// Simple autoloader for our classes
spl_autoload_register(function ($class) {
    $file = ROOT . DS . 'app' . DS . str_replace('\\', DS, $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove base path from request URI
if ($base_path !== '/') {
    $request_uri = substr($request_uri, strlen($base_path));
}

// Remove query string
$request_uri = strtok($request_uri, '?');

// Basic routing
switch ($request_uri) {
    case '/':
    case '/reports':
        // Serve the main report interface
        include ROOT . DS . 'app' . DS . 'View' . DS . 'Reports' . DS . 'index.ctp';
        break;
        
    case '/report-fields':
        // Handle report fields API
        handleReportFieldsAPI();
        break;
        
    case '/reports/generate':
        // Handle report generation
        handleReportGeneration();
        break;
        
    case '/reports/export':
        // Handle Excel export
        handleReportExport();
        break;
        
    default:
        // Check if it's a static file request
        $file_path = ROOT . DS . 'webroot' . $request_uri;
        if (file_exists($file_path) && is_file($file_path)) {
            // Serve static file
            $mime_type = mime_content_type($file_path);
            header('Content-Type: ' . $mime_type);
            readfile($file_path);
        } else {
            // 404 Not Found
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
        break;
}

// API Handlers
function handleReportFieldsAPI() {
    header('Content-Type: application/json');
    
    try {
        // Connect to database
        $host = $_ENV['MYSQLHOST'] ?? 'localhost';
        $database = $_ENV['MYSQLDATABASE'] ?? 'recknap_reports';
        $username = $_ENV['MYSQLUSER'] ?? 'root';
        $password = $_ENV['MYSQLPASSWORD'] ?? '';
        $port = $_ENV['MYSQLPORT'] ?? '3306';
        
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all active fields from database
        $stmt = $pdo->query("SELECT * FROM report_fields WHERE active = 1 ORDER BY sort_order, label");
        $fields = [];
        
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
        
        echo json_encode(['success' => true, 'data' => $fields]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function handleReportGeneration() {
    header('Content-Type: application/json');
    
    // Mock report data
    $data = [
        'fields' => [
            ['ReportField' => ['field_key' => 'customer_name', 'label' => 'Customer Name', 'data_type' => 'string']],
            ['ReportField' => ['field_key' => 'invoice_number', 'label' => 'Invoice Number', 'data_type' => 'string']],
            ['ReportField' => ['field_key' => 'total_amount', 'label' => 'Total Amount', 'data_type' => 'decimal']]
        ],
        'data' => [
            [0 => ['customer_name' => 'ABC Electronics Pvt Ltd', 'invoice_number' => 'INV-2024-001', 'total_amount' => '106200.00']],
            [0 => ['customer_name' => 'XYZ Trading Co', 'invoice_number' => 'INV-2024-002', 'total_amount' => '39530.00']],
            [0 => ['customer_name' => 'PQR Industries', 'invoice_number' => 'INV-2024-003', 'total_amount' => '147500.00']]
        ],
        'stats' => [
            'total_records' => 3,
            'fields_count' => 3,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode(['success' => true, 'data' => $data]);
}

function handleReportExport() {
    // For now, just return a success message
    // In real implementation, this would generate and download Excel file
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Excel export functionality requires full CakePHP setup with PhpSpreadsheet']);
}
?>
