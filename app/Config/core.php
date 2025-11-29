<?php
/**
 * Core Configuration for ReckNap Report POC
 */

// Security salt
Configure::write('Security.salt', 'ReckNapReportPOC2024SecretSalt');

// Security cipher seed
Configure::write('Security.cipherSeed', '76859309657453542496749683645');

// Debug level
Configure::write('debug', 2);

// App configuration
Configure::write('App', array(
    'encoding' => 'UTF-8'
));

// Session configuration
Configure::write('Session', array(
    'defaults' => 'php',
    'timeout' => 120,
    'cookieTimeout' => 120,
    'checkAgent' => false
));

// Exception handling
Configure::write('Exception', array(
    'handler' => 'ErrorHandler::handleException',
    'renderer' => 'ExceptionRenderer',
    'log' => true
));

// Error handling
Configure::write('Error', array(
    'handler' => 'ErrorHandler::handleError',
    'level' => E_ALL & ~E_DEPRECATED,
    'trace' => true
));

// Logging
CakeLog::config('debug', array(
    'engine' => 'File',
    'types' => array('notice', 'info', 'debug'),
    'file' => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'File',
    'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
    'file' => 'error',
));

// Cache configuration
Cache::config('default', array(
    'engine' => 'File',
    'duration' => '+1 hours',
    'probability' => 100,
    'path' => CACHE . 'persistent' . DS,
    'prefix' => 'recknap_',
    'lock' => false,
    'serialize' => true
));

// Dispatcher filters
Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));
