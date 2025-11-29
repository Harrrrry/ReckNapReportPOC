<?php
/**
 * Database Configuration for ReckNap Report POC
 */
class DATABASE_CONFIG {

    public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'recknap_reports',
        'prefix' => '',
        'encoding' => 'utf8',
        'port' => 3306
    );

    public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'recknap_reports_test',
        'prefix' => '',
        'encoding' => 'utf8',
        'port' => 3306
    );
}
