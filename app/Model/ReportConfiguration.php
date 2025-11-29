<?php
/**
 * ReportConfiguration Model
 * Manages saved report configurations
 */
App::uses('AppModel', 'Model');

class ReportConfiguration extends AppModel {
    
    public $useTable = 'report_configurations';
    
    public $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Configuration name is required'
            )
        ),
        'selected_fields' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Selected fields are required'
            ),
            'validJson' => array(
                'rule' => 'validateJson',
                'message' => 'Selected fields must be valid JSON'
            )
        ),
        'field_order' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Field order is required'
            ),
            'validJson' => array(
                'rule' => 'validateJson',
                'message' => 'Field order must be valid JSON'
            )
        )
    );
    
    /**
     * Validate JSON format
     */
    public function validateJson($check) {
        $value = array_values($check)[0];
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Get default configurations
     */
    public function getDefaultConfigurations() {
        return $this->find('all', array(
            'conditions' => array('is_default' => 1),
            'order' => array('name ASC')
        ));
    }
    
    /**
     * Save configuration with JSON encoding
     */
    public function saveConfiguration($data) {
        // Encode arrays to JSON if needed
        if (isset($data['selected_fields']) && is_array($data['selected_fields'])) {
            $data['selected_fields'] = json_encode($data['selected_fields']);
        }
        if (isset($data['field_order']) && is_array($data['field_order'])) {
            $data['field_order'] = json_encode($data['field_order']);
        }
        if (isset($data['filters']) && is_array($data['filters'])) {
            $data['filters'] = json_encode($data['filters']);
        }
        
        $this->create();
        return $this->save(array('ReportConfiguration' => $data));
    }
    
    /**
     * Get configuration with decoded JSON
     */
    public function getConfiguration($id) {
        $config = $this->findById($id);
        if ($config) {
            // Decode JSON fields
            $config['ReportConfiguration']['selected_fields'] = json_decode($config['ReportConfiguration']['selected_fields'], true);
            $config['ReportConfiguration']['field_order'] = json_decode($config['ReportConfiguration']['field_order'], true);
            if ($config['ReportConfiguration']['filters']) {
                $config['ReportConfiguration']['filters'] = json_decode($config['ReportConfiguration']['filters'], true);
            }
        }
        return $config;
    }
}
