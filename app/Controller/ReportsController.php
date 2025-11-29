<?php
/**
 * Reports Controller
 * Handles dynamic report generation and Excel export
 */
App::uses('AppController', 'Controller');
require_once(APP . 'Vendor' . DS . 'autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportsController extends AppController {
    
    public $uses = array('Report', 'ReportField', 'ReportConfiguration');
    
    /**
     * Main report interface
     */
    public function index() {
        // This will render the main report interface view
        $this->set('title', 'Dynamic Report Generator');
    }
    
    /**
     * Generate report data
     * POST /reports/generate
     */
    public function generate() {
        if (!$this->request->is('post')) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        try {
            $data = $this->request->data;
            
            // Validate input
            if (empty($data['selected']) || !is_array($data['selected'])) {
                return $this->sendErrorResponse('Selected fields are required');
            }
            
            $selectedFields = $data['selected'];
            $fieldOrder = $data['order'] ?? $selectedFields;
            $filters = $data['filters'] ?? null;
            $limit = $data['limit'] ?? 100; // Default limit for preview
            
            // Generate report
            $reportData = $this->Report->generateReport($selectedFields, $fieldOrder, $filters, $limit);
            
            // Get statistics
            $stats = $this->Report->getReportStats($selectedFields, $filters);
            
            $response = array(
                'fields' => $reportData['fields'],
                'data' => $reportData['data'],
                'stats' => $stats
            );
            
            return $this->sendSuccessResponse($response);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to generate report: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Export report to Excel
     * POST /reports/export
     */
    public function export() {
        if (!$this->request->is('post')) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        try {
            $data = $this->request->data;
            
            // Validate input
            if (empty($data['selected']) || !is_array($data['selected'])) {
                return $this->sendErrorResponse('Selected fields are required');
            }
            
            $selectedFields = $data['selected'];
            $fieldOrder = $data['order'] ?? $selectedFields;
            $filters = $data['filters'] ?? null;
            $reportName = $data['report_name'] ?? 'Dynamic_Report';
            
            // Generate full report (no limit for export)
            $reportData = $this->Report->generateReport($selectedFields, $fieldOrder, $filters);
            
            // Create Excel file
            $excelFile = $this->createExcelFile($reportData, $reportName);
            
            // Send file for download
            $this->response->file($excelFile, array(
                'download' => true,
                'name' => $reportName . '_' . date('Y-m-d_H-i-s') . '.xlsx'
            ));
            
            // Clean up temporary file
            register_shutdown_function(function() use ($excelFile) {
                if (file_exists($excelFile)) {
                    unlink($excelFile);
                }
            });
            
            return $this->response;
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to export report: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Create Excel file using PhpSpreadsheet
     */
    private function createExcelFile($reportData, $reportName) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set sheet name
        $sheet->setTitle(substr($reportName, 0, 31)); // Excel sheet name limit
        
        $fields = $reportData['fields'];
        $data = $reportData['data'];
        
        // Set headers (Row 1)
        $col = 1;
        foreach ($fields as $field) {
            $label = $field['ReportField']['label'];
            $sheet->setCellValueByColumnAndRow($col, 1, $label);
            
            // Style header
            $cellCoordinate = $sheet->getCellByColumnAndRow($col, 1)->getCoordinate();
            $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
            $sheet->getStyle($cellCoordinate)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E6E6FA');
            $sheet->getStyle($cellCoordinate)->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $col++;
        }
        
        // Fill data rows
        $row = 2;
        foreach ($data as $record) {
            $col = 1;
            foreach ($fields as $field) {
                $fieldKey = $field['ReportField']['field_key'];
                $value = $record[0][$fieldKey] ?? '';
                
                // Format value based on data type
                $dataType = $field['ReportField']['data_type'];
                $formattedValue = $this->formatCellValue($value, $dataType);
                
                $sheet->setCellValueByColumnAndRow($col, $row, $formattedValue);
                
                // Auto-size column
                $columnLetter = $sheet->getCellByColumnAndRow($col, 1)->getColumn();
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                
                $col++;
            }
            $row++;
        }
        
        // Add summary information
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'Report Generated:');
        $sheet->setCellValue('B' . $summaryRow, date('Y-m-d H:i:s'));
        $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Records:');
        $sheet->setCellValue('B' . ($summaryRow + 1), count($data));
        
        // Style summary
        $sheet->getStyle('A' . $summaryRow . ':A' . ($summaryRow + 1))->getFont()->setBold(true);
        
        // Create temporary file
        $tempDir = sys_get_temp_dir();
        $fileName = $tempDir . DS . 'report_' . uniqid() . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);
        
        return $fileName;
    }
    
    /**
     * Format cell value based on data type
     */
    private function formatCellValue($value, $dataType) {
        if ($value === null || $value === '') {
            return '';
        }
        
        switch ($dataType) {
            case 'decimal':
                return is_numeric($value) ? number_format((float)$value, 2) : $value;
            case 'integer':
                return is_numeric($value) ? (int)$value : $value;
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'datetime':
                return date('Y-m-d H:i:s', strtotime($value));
            case 'boolean':
                return $value ? 'Yes' : 'No';
            default:
                return (string)$value;
        }
    }
    
    /**
     * Save report configuration
     * POST /reports/save-config
     */
    public function saveConfig() {
        if (!$this->request->is('post')) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        try {
            $data = $this->request->data;
            
            // Validate required fields
            if (empty($data['name']) || empty($data['selected_fields']) || empty($data['field_order'])) {
                return $this->sendErrorResponse('Name, selected fields, and field order are required');
            }
            
            if ($this->ReportConfiguration->saveConfiguration($data)) {
                return $this->sendSuccessResponse(null, 'Report configuration saved successfully');
            } else {
                $errors = $this->ReportConfiguration->validationErrors;
                return $this->sendErrorResponse('Failed to save configuration', 400, $errors);
            }
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to save configuration: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get saved report configurations
     * GET /reports/configs
     */
    public function configs() {
        try {
            $configs = $this->ReportConfiguration->find('all', array(
                'order' => array('name ASC')
            ));
            
            $formattedConfigs = array();
            foreach ($configs as $config) {
                $formattedConfigs[] = array(
                    'id' => $config['ReportConfiguration']['id'],
                    'name' => $config['ReportConfiguration']['name'],
                    'description' => $config['ReportConfiguration']['description'],
                    'is_default' => $config['ReportConfiguration']['is_default'],
                    'created' => $config['ReportConfiguration']['created']
                );
            }
            
            return $this->sendSuccessResponse($formattedConfigs);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to fetch configurations: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Load report configuration
     * GET /reports/load-config/{id}
     */
    public function loadConfig($id = null) {
        if (!$id) {
            return $this->sendErrorResponse('Configuration ID is required');
        }
        
        try {
            $config = $this->ReportConfiguration->getConfiguration($id);
            
            if (!$config) {
                return $this->sendErrorResponse('Configuration not found', 404);
            }
            
            return $this->sendSuccessResponse($config['ReportConfiguration']);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to load configuration: ' . $e->getMessage(), 500);
        }
    }
}
