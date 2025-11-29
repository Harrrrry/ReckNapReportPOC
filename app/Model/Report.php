<?php
/**
 * Report Model
 * Handles dynamic report generation and queries
 */
App::uses('AppModel', 'Model');

class Report extends AppModel {
    
    public $useTable = false; // This model doesn't have its own table
    
    public $uses = array('ReportField');
    
    /**
     * Generate dynamic report data
     */
    public function generateReport($selectedFields, $fieldOrder = null, $filters = null, $limit = null) {
        // Load ReportField model
        $this->ReportField = ClassRegistry::init('ReportField');
        
        // Get field configurations
        $fields = $this->ReportField->getFieldsByKeys($selectedFields, $fieldOrder);
        
        if (empty($fields)) {
            return array('fields' => array(), 'data' => array());
        }
        
        // Build dynamic query
        $query = $this->buildDynamicQuery($fields, $filters, $limit);
        
        // Execute query
        $db = $this->getDataSource();
        $results = $db->fetchAll($query);
        
        return array(
            'fields' => $fields,
            'data' => $results
        );
    }
    
    /**
     * Build dynamic SQL query based on selected fields
     */
    private function buildDynamicQuery($fields, $filters = null, $limit = null) {
        $selectFields = array();
        $joinTables = array();
        $mainTable = null;
        
        // Process each field to build SELECT and JOIN clauses
        foreach ($fields as $field) {
            $fieldConfig = $field['ReportField'];
            $tableName = $fieldConfig['table_name'];
            $columnName = $fieldConfig['column_name'];
            $fieldKey = $fieldConfig['field_key'];
            
            // Determine main table (usually the first table or invoices as primary)
            if ($mainTable === null || $tableName === 'invoices') {
                $mainTable = $tableName;
            }
            
            // Build SELECT field
            if ($fieldConfig['field_type'] === 'calculated' && !empty($fieldConfig['calculation_logic'])) {
                $selectFields[] = "({$fieldConfig['calculation_logic']}) AS `{$fieldKey}`";
            } else {
                $selectFields[] = "`{$tableName}`.`{$columnName}` AS `{$fieldKey}`";
            }
            
            // Track tables for JOINs
            if (!in_array($tableName, $joinTables)) {
                $joinTables[] = $tableName;
            }
        }
        
        // Build base query
        $sql = "SELECT " . implode(', ', $selectFields) . " FROM `{$mainTable}`";
        
        // Add JOINs
        $sql .= $this->buildJoinClauses($mainTable, $joinTables);
        
        // Add WHERE clause
        $whereConditions = array();
        
        // Add filters if provided
        if ($filters && is_array($filters)) {
            foreach ($filters as $filter) {
                if (isset($filter['field']) && isset($filter['operator']) && isset($filter['value'])) {
                    $whereConditions[] = $this->buildFilterCondition($filter);
                }
            }
        }
        
        // Add default conditions (e.g., active records only)
        $whereConditions[] = "`{$mainTable}`.`id` IS NOT NULL";
        
        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(' AND ', $whereConditions);
        }
        
        // Add ORDER BY
        $sql .= " ORDER BY `{$mainTable}`.`id` DESC";
        
        // Add LIMIT if specified
        if ($limit && is_numeric($limit)) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $sql;
    }
    
    /**
     * Build JOIN clauses based on required tables
     */
    private function buildJoinClauses($mainTable, $joinTables) {
        $joins = array();
        
        foreach ($joinTables as $table) {
            if ($table === $mainTable) {
                continue; // Skip main table
            }
            
            // Define JOIN relationships
            switch ($table) {
                case 'customers':
                    if ($mainTable === 'invoices' || in_array('invoices', $joinTables)) {
                        $joins[] = "LEFT JOIN `customers` ON `invoices`.`customer_id` = `customers`.`id`";
                    } elseif ($mainTable === 'payments' || in_array('payments', $joinTables)) {
                        $joins[] = "LEFT JOIN `customers` ON `payments`.`customer_id` = `customers`.`id`";
                    } elseif ($mainTable === 'memos' || in_array('memos', $joinTables)) {
                        $joins[] = "LEFT JOIN `customers` ON `memos`.`customer_id` = `customers`.`id`";
                    }
                    break;
                    
                case 'invoices':
                    if ($mainTable === 'customers') {
                        $joins[] = "LEFT JOIN `invoices` ON `customers`.`id` = `invoices`.`customer_id`";
                    } elseif ($mainTable === 'payments') {
                        $joins[] = "LEFT JOIN `invoices` ON `payments`.`invoice_id` = `invoices`.`id`";
                    } elseif ($mainTable === 'memos') {
                        $joins[] = "LEFT JOIN `invoices` ON `memos`.`invoice_id` = `invoices`.`id`";
                    } elseif ($mainTable === 'invoice_items') {
                        $joins[] = "LEFT JOIN `invoices` ON `invoice_items`.`invoice_id` = `invoices`.`id`";
                    }
                    break;
                    
                case 'products':
                    if ($mainTable === 'invoice_items' || in_array('invoice_items', $joinTables)) {
                        $joins[] = "LEFT JOIN `products` ON `invoice_items`.`product_id` = `products`.`id`";
                    }
                    break;
                    
                case 'invoice_items':
                    if ($mainTable === 'invoices') {
                        $joins[] = "LEFT JOIN `invoice_items` ON `invoices`.`id` = `invoice_items`.`invoice_id`";
                    } elseif ($mainTable === 'products') {
                        $joins[] = "LEFT JOIN `invoice_items` ON `products`.`id` = `invoice_items`.`product_id`";
                    }
                    break;
                    
                case 'payments':
                    if ($mainTable === 'customers') {
                        $joins[] = "LEFT JOIN `payments` ON `customers`.`id` = `payments`.`customer_id`";
                    } elseif ($mainTable === 'invoices') {
                        $joins[] = "LEFT JOIN `payments` ON `invoices`.`id` = `payments`.`invoice_id`";
                    }
                    break;
                    
                case 'memos':
                    if ($mainTable === 'customers') {
                        $joins[] = "LEFT JOIN `memos` ON `customers`.`id` = `memos`.`customer_id`";
                    } elseif ($mainTable === 'invoices') {
                        $joins[] = "LEFT JOIN `memos` ON `invoices`.`id` = `memos`.`invoice_id`";
                    }
                    break;
            }
        }
        
        return empty($joins) ? '' : ' ' . implode(' ', array_unique($joins));
    }
    
    /**
     * Build filter condition for WHERE clause
     */
    private function buildFilterCondition($filter) {
        $field = $filter['field'];
        $operator = $filter['operator'];
        $value = $filter['value'];
        
        // Sanitize field name (should be from our whitelist)
        $field = preg_replace('/[^a-zA-Z0-9_.]/', '', $field);
        
        switch ($operator) {
            case 'equals':
                return "`{$field}` = '" . mysql_real_escape_string($value) . "'";
            case 'not_equals':
                return "`{$field}` != '" . mysql_real_escape_string($value) . "'";
            case 'contains':
                return "`{$field}` LIKE '%" . mysql_real_escape_string($value) . "%'";
            case 'starts_with':
                return "`{$field}` LIKE '" . mysql_real_escape_string($value) . "%'";
            case 'ends_with':
                return "`{$field}` LIKE '%" . mysql_real_escape_string($value) . "'";
            case 'greater_than':
                return "`{$field}` > '" . mysql_real_escape_string($value) . "'";
            case 'less_than':
                return "`{$field}` < '" . mysql_real_escape_string($value) . "'";
            case 'between':
                if (is_array($value) && count($value) === 2) {
                    return "`{$field}` BETWEEN '" . mysql_real_escape_string($value[0]) . "' AND '" . mysql_real_escape_string($value[1]) . "'";
                }
                break;
            case 'in':
                if (is_array($value)) {
                    $escaped_values = array_map('mysql_real_escape_string', $value);
                    return "`{$field}` IN ('" . implode("','", $escaped_values) . "')";
                }
                break;
        }
        
        return '1=1'; // Default safe condition
    }
    
    /**
     * Get report statistics
     */
    public function getReportStats($selectedFields, $filters = null) {
        $reportData = $this->generateReport($selectedFields, null, $filters);
        
        return array(
            'total_records' => count($reportData['data']),
            'fields_count' => count($reportData['fields']),
            'generated_at' => date('Y-m-d H:i:s')
        );
    }
}
