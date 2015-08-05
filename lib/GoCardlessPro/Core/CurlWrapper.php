<?php
/**
  * WARNING: Do not edit by hand, this file was generated by Crank:
  * https://github.com/gocardless/crank
  *
  */

namespace GoCardlessPro\Core;

/**
  * Curl Wrapper class to wrap the curl procedural api with an object api.
  * @package GoCardlessPro
  * @subpackage Core
  * @version 0.0.2
  */
class CurlWrapper
{
    /** The path to the CA cert bundle for use by Curl */
    const CA_CERT_FILENAME = 'cacert.pem';

    /** @var array[string]string Associative array of request headers */
    private $headers;

    /** @var array[string]string Associative array of response headers */
    private $response_headers;

    /** @var int Raw curl handle reference */
    private $curl;

    /** @var array[int]mixed Curl options hash */
    private $opts;

  /**
    * Creates a new curl request to a given url
    *
    * @param string $method HTTP Method
    * @param string $url HTTP url
    */
    public function __construct($method, $url)
    {
        $this->headers = array();
        $this->response_headers = array();
        $this->curl = curl_init();
        $this->setup_curl($method, $url);
    }

  /**
    * Internal function delegated from the constructor to init the curl handle.
    *
    * @param string $method HTTP Method
    * @param string $url HTTP url
    */
    private function setup_curl($method, $url)
    {
        $this->opts = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_USERAGENT => $this->getUserAgent(),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_VERBOSE => false,
            CURLOPT_CAINFO => $this->getLibraryRootPath() . self::CA_CERT_FILENAME,
            CURLOPT_HEADERFUNCTION => array($this, 'setResponseHeader')
        );
    }

  /**
    * Gets the client's user agent for calling to the api.
    *
    * @return string
    */
    private function getUserAgent()
    {
        $curlinfo = curl_version();
        $uagent = array();
        $uagent[] = 'gocardless-pro-php/0.3.0';
        $uagent[] = 'php/' . phpversion();
        $uagent[] = 'curl/' . $curlinfo['version'];
        $uagent[] = 'os/' . $curlinfo['host'];
        $uagent[] = 'schema-version/2015-07-06';
        return implode(' ', $uagent);
    }

  /**
    * Get a curl option previously set.
    *
    * @param integer $opt Curl option constant
    */
    public function getOpt($opt)
    {
        if (!isset($this->opts[$opt])) {
            return null;
        }
        return $this->opts[$opt];
    }

  /**
    * Get a header set (case-insensitive)
    *
    * @param string $name header name
    * @return string
    */
    public function getHeader($name)
    {
        return $this->headers[strtolower($name)];
    }

  /**
    * Set option header for CURL
    * @param integer $key curl option key
    * @param mixed $val curl option value
    */
    private function setOpt($key, $val)
    {
        $this->opts[$key] = $val;
    }

  /**
    * Sets a post body
    * @param string $post_body The post body data
    * @param string $content_type content type of the post body
    */
    public function setPostBody($post_body, $content_type)
    {
        $this->setHeaders(array('content-type' => $content_type));
        $this->setOpt(CURLOPT_POSTFIELDS, $post_body);
    }

  /**
    * Sets the curl authorization header
    * @param string $auth Authorisation header to set
    */
    public function setAuth($auth)
    {
        $this->setHeaders(array('Authorization' => 'Bearer ' . $auth));
    }

  /**
    * Sets request headers from an associative array. Case insensitive.
    * @param array[string]string $headers Keys need to be strings, values need to be strings.
    */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $val) {
            if (!is_string($key)) {
                throw new \Exception('Header names need to be strings, not ' . gettype($key));
            }
            $this->headers[strtolower($key)] = $val;
        }
    }

  /**
    * Internal function called by curl to set a response header as a callback.
    * @param unused
    * @param string $header Full header line from response
    */
    public function setResponseHeader($_, $header)
    {
        $pos = strpos($header, ':');
        if ($pos > 0) {
            $this->response_headers[substr($header, 0, $pos)] = rtrim(substr($header, $pos + 2));
        }
        return strlen($header);
    }

  /**
    * Setup the http request headers.
    */
    protected function setup_request()
    {
        $curl_headers = array();
        foreach ($this->headers as $key => $val) {
            $curl_headers[] = $key . ': ' . $val;
        }
        $this->opts[CURLOPT_HTTPHEADER] = $curl_headers;
        foreach ($this->opts as $key => $val) {
            curl_setopt($this->curl, $key, $val);
        }
    }

  /**
    * Run the prepared http request
    * @return Response HTTP response object.
    */
    public function run()
    {
        $this->setup_request();
        $body = curl_exec($this->curl);
        if ($body === false) {
            throw new Error\HttpError(curl_errno($this->curl), curl_error($this->curl));
        }
        $content_type = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        return new Response(
            $body,
            $status,
            $content_type,
            $this->response_headers
        );
    }

 /**
    * Internal function for finding the root path of the library, used to build the path to cacert.pem
    * @return Path to the root of the library
    */
    private function getLibraryRootPath()
    {
        return dirname(__FILE__) . "/../../../";
    }
}
