<?php
/**
 * APIResponse Class
 * Helper class for Sending API Responses
 */
class APIResponse {

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
     * @var $body
     */
    private $body;

    public function __construct() {

    }

    private function send_headers() {
        header('HTTP/1.1 '. $this->response_code .' '. $this->http_messages[$this->response_code], true, $this->response_code);
        header('Content-Type: '.$this->content_type, true, $this->response_code);
    }

    private function send_body() {
        echo json_encode($this->body);
    }

    public function send($body) {
        $this->body = $body;

        // send the headers
        $this->send_headers();

        // send the response body
        $this->send_body();

        // exit the script
        exit;
    }

    public function sendError($message, $code = 404) {
        $this->response_code = $code;

        $this->body = array('error'=>array('code'=>$code, 'message'=>$message));

        // send the headers
        $this->send_headers();

        // send the response body
        $this->send_body();

        // exit the script
        exit;
    }

}