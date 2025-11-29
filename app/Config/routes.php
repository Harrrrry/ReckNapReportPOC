<?php
/**
 * Routes configuration for ReckNap Report POC
 */

// Default route
Router::connect('/', array('controller' => 'reports', 'action' => 'index'));

// Report Field Management Routes
Router::connect('/report-fields', array('controller' => 'report_fields', 'action' => 'index'));
Router::connect('/report-fields/add', array('controller' => 'report_fields', 'action' => 'add'));
Router::connect('/report-fields/edit/:id', array('controller' => 'report_fields', 'action' => 'edit'), array('pass' => array('id')));
Router::connect('/report-fields/delete/:id', array('controller' => 'report_fields', 'action' => 'delete'), array('pass' => array('id')));
Router::connect('/report-fields/reorder', array('controller' => 'report_fields', 'action' => 'reorder'));
Router::connect('/report-fields/tables', array('controller' => 'report_fields', 'action' => 'tables'));
Router::connect('/report-fields/columns/:table', array('controller' => 'report_fields', 'action' => 'columns'), array('pass' => array('table')));

// Report Generation Routes
Router::connect('/reports', array('controller' => 'reports', 'action' => 'index'));
Router::connect('/reports/generate', array('controller' => 'reports', 'action' => 'generate'));
Router::connect('/reports/export', array('controller' => 'reports', 'action' => 'export'));
Router::connect('/reports/save-config', array('controller' => 'reports', 'action' => 'saveConfig'));
Router::connect('/reports/configs', array('controller' => 'reports', 'action' => 'configs'));
Router::connect('/reports/load-config/:id', array('controller' => 'reports', 'action' => 'loadConfig'), array('pass' => array('id')));

// Load default CakePHP routes
require CAKE . 'Config' . DS . 'routes.php';
