<?php
/**
 * Response Class
 * Helper class for Sending API Responses
 */
class Response {

    /**
     * List of all known HTTP response codes - used to
     * translate numeric codes to messages.
     *
     * @var array
     */
    protected $http_messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * @var $content_type
     */
    private $content_type = 'application/json; charset=utf-8';

    /**
     * @var $response_code
     */
    private $response_code = 200;
    
    /**
     * @var $headers
     */
    private $headers = array();
    
    /**
     * @var $headers_sent - boolean
     */
    private $headers_sent = false;
    
    /**
     * @var $error
     */
    private $error;

    /**
     * @var $body
     */
    private $body = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->addHeader('Content-type: '. $this->content_type);
    }

    /**
     * Add Headers to send
     */
    public function addHeader($header, $replace = true) {
        if (!$this->headers_sent) {
            // if not already in the array, or if in the array but $replace == false, then we add it to the array
            if (!in_array($header, $this->headers) || (in_array($header, $this->headers) && !$replace)) {
                $this->headers[] = array(
                      'header' => $header
                    , 'replace' => $replace
                );
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get an array of headers that are to be sent
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * Set the headers to be sent
     */
    private function setHeaders() {
        // set the HTTP/1.1 header
        header('HTTP/1.1 '. $this->response_code .' '. $this->http_messages[$this->response_code], true, $this->response_code);
        
        // set the rest of the headers
        foreach($this->headers as $key => $header) {
            header($header['header'], $header['replace']);
        }
        
        $this->headers_sent = true;
        
        return $this->headers_sent;
    }
    
    /**
     * Set the content type
     */
    public function setContentType($type) {
        if (is_string($type)) {
            $this->$content_type = $type;
            $this->addHeader('Content-type: '. $this->content_type);
        }
        return $this->getContentType();
    }
    
    /**
     * Get Content Type
     */
    public function getContentType() {
        return $this->content_type;
    }
    
    /**
     * Set Error
     */
    public function setError($code, $msg, $details, $http_response_code = false) {
        $this->error = array(
              'code' => $code
            , 'msg' => $msg
            , 'details' => $details
        );
        
        if ($http_resposne_code != false) {
            $this->setResponseCode($http_resposne_code);
        }
    }
    
    /**
     * Set the HTTP Response Code
     */
    public function setResponseCode($code) {
        if (in_array($code, array_keys($this->http_messages))) {
            $this->response_code = $code;
            return $this->getResponseCode();
        }
        return false;
    }
    
    /**
     * Get the HTTP Response Code
     */
    public function getResponseCode() {
        return $this->response_code;
    }

    /**
     * send the body of the response
     */
    private function sendBody() {
        // add the errors to the body of the response
        if (is_array($this->error)) {
            $this->body['err'] = $this->error;
        }
        echo json_encode($this->body);
    }

    /**
     * Compose and send the response
     */
    public function send(array $body) {
        $this->body = $body;

        // send the headers
        $this->setHeaders();

        // send the response body
        $this->sendBody();

        // exit the script
        exit;
    }
}