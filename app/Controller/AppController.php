<?php
/**
 * Application Controller
 * Base controller for ReckNap Report POC
 */
App::uses('Controller', 'Controller');

class AppController extends Controller {
    
    public $components = array(
        'Session',
        'RequestHandler'
    );
    
    public $helpers = array(
        'Html',
        'Form',
        'Session'
    );
    
    /**
     * Before filter
     */
    public function beforeFilter() {
        parent::beforeFilter();
        
        // Set JSON response for AJAX requests
        if ($this->request->is('ajax')) {
            $this->RequestHandler->respondAs('json');
        }
        
        // Security headers
        $this->response->header('X-Content-Type-Options', 'nosniff');
        $this->response->header('X-Frame-Options', 'DENY');
        $this->response->header('X-XSS-Protection', '1; mode=block');
    }
    
    /**
     * Send JSON response
     */
    protected function sendJsonResponse($data, $status = 200) {
        $this->response->statusCode($status);
        $this->response->type('json');
        $this->response->body(json_encode($data));
        return $this->response;
    }
    
    /**
     * Send error response
     */
    protected function sendErrorResponse($message, $status = 400, $errors = null) {
        $response = array(
            'success' => false,
            'message' => $message
        );
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return $this->sendJsonResponse($response, $status);
    }
    
    /**
     * Send success response
     */
    protected function sendSuccessResponse($data = null, $message = 'Success') {
        $response = array(
            'success' => true,
            'message' => $message
        );
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $this->sendJsonResponse($response);
    }
}
