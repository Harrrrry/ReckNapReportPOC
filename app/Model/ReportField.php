<?php
/**
 * ReportField Model
 * Manages dynamic report field configurations
 */
App::uses('AppModel', 'Model');

class ReportField extends AppModel {
    
    public $useTable = 'report_fields';
    
    public $validate = array(
        'field_key' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Field key is required'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Field key must be unique'
            ),
            'alphaNumericDashUnderscore' => array(
                'rule' => '/^[a-zA-Z0-9_-]+$/',
                'message' => 'Field key can only contain letters, numbers, dashes and underscores'
            )
        ),
        'label' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Label is required'
            )
        ),
        'table_name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Table name is required'
            ),
            'validTable' => array(
                'rule' => 'validateTableName',
                'message' => 'Invalid table name'
            )
        ),
        'column_name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Column name is required'
            )
        ),
        'data_type' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Data type is required'
            ),
            'inList' => array(
                'rule' => array('inList', array('string', 'integer', 'decimal', 'date', 'datetime', 'boolean')),
                'message' => 'Invalid data type'
            )
        ),
        'field_type' => array(
            'inList' => array(
                'rule' => array('inList', array('simple', 'calculated', 'joined', 'aggregated')),
                'message' => 'Invalid field type'
            )
        )
    );
    
    /**
     * Whitelist of allowed tables for security
     */
    private $allowedTables = array(
        'customers',
        'products', 
        'invoices',
        'invoice_items',
        'payments',
        'memos'
    );
    
    /**
     * Validate table name against whitelist
     */
    public function validateTableName($check) {
        $tableName = array_values($check)[0];
        return in_array($tableName, $this->allowedTables);
    }
    
    /**
     * Get all active fields ordered by sort_order
     */
    public function getActiveFields() {
        return $this->find('all', array(
            'conditions' => array('active' => 1),
            'order' => array('sort_order ASC', 'label ASC')
        ));
    }
    
    /**
     * Get fields by keys in specified order
     */
    public function getFieldsByKeys($fieldKeys, $order = null) {
        $conditions = array('field_key' => $fieldKeys, 'active' => 1);
        
        $fields = $this->find('all', array(
            'conditions' => $conditions
        ));
        
        // If order is specified, sort the results accordingly
        if ($order && is_array($order)) {
            $orderedFields = array();
            foreach ($order as $key) {
                foreach ($fields as $field) {
                    if ($field['ReportField']['field_key'] === $key) {
                        $orderedFields[] = $field;
                        break;
                    }
                }
            }
            return $orderedFields;
        }
        
        return $fields;
    }
    
    /**
     * Update sort order for multiple fields
     */
    public function updateSortOrder($fieldOrders) {
        $success = true;
        foreach ($fieldOrders as $fieldKey => $sortOrder) {
            $field = $this->findByFieldKey($fieldKey);
            if ($field) {
                $field['ReportField']['sort_order'] = $sortOrder;
                if (!$this->save($field)) {
                    $success = false;
                }
            }
        }
        return $success;
    }
    
    /**
     * Get available tables for dropdown
     */
    public function getAvailableTables() {
        $tables = array();
        foreach ($this->allowedTables as $table) {
            $tables[$table] = ucwords(str_replace('_', ' ', $table));
        }
        return $tables;
    }
    
    /**
     * Get columns for a specific table
     */
    public function getTableColumns($tableName) {
        if (!in_array($tableName, $this->allowedTables)) {
            return array();
        }
        
        // Get table schema
        $db = $this->getDataSource();
        $schema = $db->describe($tableName);
        
        $columns = array();
        foreach ($schema as $column => $details) {
            // Skip system columns
            if (!in_array($column, array('id', 'created', 'modified'))) {
                $columns[$column] = ucwords(str_replace('_', ' ', $column));
            }
        }
        
        return $columns;
    }
}
