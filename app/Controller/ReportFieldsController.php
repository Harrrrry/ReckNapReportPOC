<?php
/**
 * ReportFields Controller
 * Manages dynamic report field configurations
 */
App::uses('AppController', 'Controller');

class ReportFieldsController extends AppController {
    
    public $uses = array('ReportField');
    
    /**
     * Get all report fields
     * GET /report-fields
     */
    public function index() {
        try {
            $fields = $this->ReportField->getActiveFields();
            
            // Format response
            $formattedFields = array();
            foreach ($fields as $field) {
                $formattedFields[] = array(
                    'id' => $field['ReportField']['id'],
                    'field_key' => $field['ReportField']['field_key'],
                    'label' => $field['ReportField']['label'],
                    'table_name' => $field['ReportField']['table_name'],
                    'column_name' => $field['ReportField']['column_name'],
                    'data_type' => $field['ReportField']['data_type'],
                    'field_type' => $field['ReportField']['field_type'],
                    'sort_order' => $field['ReportField']['sort_order']
                );
            }
            
            return $this->sendSuccessResponse($formattedFields);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to fetch report fields: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Add new report field
     * POST /report-fields/add
     */
    public function add() {
        if (!$this->request->is('post')) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        try {
            $data = $this->request->data;
            
            // Validate required fields
            $required = array('field_key', 'label', 'table_name', 'column_name', 'data_type');
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->sendErrorResponse("Field '{$field}' is required");
                }
            }
            
            // Set defaults
            if (!isset($data['field_type'])) {
                $data['field_type'] = 'simple';
            }
            if (!isset($data['active'])) {
                $data['active'] = 1;
            }
            
            // Get next sort order
            $maxSort = $this->ReportField->find('first', array(
                'fields' => array('MAX(sort_order) as max_sort'),
                'conditions' => array('active' => 1)
            ));
            $data['sort_order'] = ($maxSort[0]['max_sort'] ?? 0) + 1;
            
            // Save field
            $this->ReportField->create();
            if ($this->ReportField->save(array('ReportField' => $data))) {
                $savedField = $this->ReportField->findById($this->ReportField->getLastInsertID());
                return $this->sendSuccessResponse($savedField['ReportField'], 'Report field added successfully');
            } else {
                $errors = $this->ReportField->validationErrors;
                return $this->sendErrorResponse('Validation failed', 400, $errors);
            }
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to add report field: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update report field
     * PUT /report-fields/edit/{id}
     */
    public function edit($id = null) {
        if (!$this->request->is(array('put', 'post'))) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        if (!$id) {
            return $this->sendErrorResponse('Field ID is required');
        }
        
        try {
            $field = $this->ReportField->findById($id);
            if (!$field) {
                return $this->sendErrorResponse('Report field not found', 404);
            }
            
            $data = $this->request->data;
            $data['id'] = $id;
            
            if ($this->ReportField->save(array('ReportField' => $data))) {
                $updatedField = $this->ReportField->findById($id);
                return $this->sendSuccessResponse($updatedField['ReportField'], 'Report field updated successfully');
            } else {
                $errors = $this->ReportField->validationErrors;
                return $this->sendErrorResponse('Validation failed', 400, $errors);
            }
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update report field: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Delete report field
     * DELETE /report-fields/delete/{id}
     */
    public function delete($id = null) {
        if (!$this->request->is(array('delete', 'post'))) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        if (!$id) {
            return $this->sendErrorResponse('Field ID is required');
        }
        
        try {
            $field = $this->ReportField->findById($id);
            if (!$field) {
                return $this->sendErrorResponse('Report field not found', 404);
            }
            
            // Soft delete by setting active = 0
            $field['ReportField']['active'] = 0;
            if ($this->ReportField->save($field)) {
                return $this->sendSuccessResponse(null, 'Report field deleted successfully');
            } else {
                return $this->sendErrorResponse('Failed to delete report field');
            }
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete report field: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Update field order
     * POST /report-fields/reorder
     */
    public function reorder() {
        if (!$this->request->is('post')) {
            return $this->sendErrorResponse('Method not allowed', 405);
        }
        
        try {
            $fieldOrders = $this->request->data['field_orders'] ?? array();
            
            if (empty($fieldOrders)) {
                return $this->sendErrorResponse('Field orders are required');
            }
            
            if ($this->ReportField->updateSortOrder($fieldOrders)) {
                return $this->sendSuccessResponse(null, 'Field order updated successfully');
            } else {
                return $this->sendErrorResponse('Failed to update field order');
            }
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update field order: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get available tables
     * GET /report-fields/tables
     */
    public function tables() {
        try {
            $tables = $this->ReportField->getAvailableTables();
            return $this->sendSuccessResponse($tables);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to fetch available tables: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get columns for a table
     * GET /report-fields/columns/{table}
     */
    public function columns($tableName = null) {
        if (!$tableName) {
            return $this->sendErrorResponse('Table name is required');
        }
        
        try {
            $columns = $this->ReportField->getTableColumns($tableName);
            return $this->sendSuccessResponse($columns);
            
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to fetch table columns: ' . $e->getMessage(), 500);
        }
    }
}
