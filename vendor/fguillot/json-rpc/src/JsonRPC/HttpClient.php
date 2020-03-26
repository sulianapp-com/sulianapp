<?php

namespace JsonRPC;

use Closure;
use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\ConnectionFailureException;
use JsonRPC\Exception\ServerErrorException;

/**
 * Class HttpClient
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class HttpClient
{
    /**
     * URL of the server
     *
     * @var string
     */
    protected $url;

    /**
     * HTTP client timeout
     *
     * @var integer
     */
    protected $timeout = 5;

    /**
     * Default HTTP headers to send to the server
     *
     * @var array
     */
    protected $headers = [
        'User-Agent: JSON-RPC PHP Client <https://github.com/fguillot/JsonRPC>',
        'Content-Type: application/json',
        'Accept: application/json',
        'Connection: close',
    ];

    /**
     * Username for authentication
     *
     * @var string
     */
    protected $username;

    /**
     * Password for authentication
     *
     * @var string
     */
    protected $password;

    /**
     * Enable debug output to the php error log
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     * Cookies
     *
     * @var array
     */
    protected $cookies = [];

    /**
     * SSL certificates verification
     *
     * @var boolean
     */
    protected $verifySslCertificate = true;

    /**
     * SSL client certificate
     *
     * @var string
     */
    protected $sslLocalCert;

    /**
     * Callback called before the doing the request
     *
     * @var Closure
     */
    protected $beforeRequest;

    /**
     * HttpClient constructor
     *
     * @param  string $url
     */
    public function __construct($url = '')
    {
        $this->url = $url;
    }

    /**
     * Set URL
     *
     * @param  string $url
     *
     * @return $this
     */
    public function withUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set username
     *
     * @param  string $username
     *
     * @return $this
     */
    public function withUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param  string $password
     *
     * @return $this
     */
    public function withPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set timeout
     *
     * @param  integer $timeout
     *
     * @return $this
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set headers
     *
     * @param  array $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Set cookies
     *
     * @param  array     $cookies
     * @param  boolean   $replace
     */
    public function withCookies(array $cookies, $replace = false)
    {
        if ($replace) {
            $this->cookies = $cookies;
        } else {
            $this->cookies = array_merge($this->cookies, $cookies);
        }
    }

    /**
     * Enable debug mode
     *
     * @return $this
     */
    public function withDebug()
    {
        $this->debug = true;

        return $this;
    }

    /**
     * Disable SSL verification
     *
     * @return $this
     */
    public function withoutSslVerification()
    {
        $this->verifySslCertificate = false;

        return $this;
    }

    /**
     * Assign a certificate to use TLS
     *
     * @return $this
     */
    public function withSslLocalCert($path)
    {
        $this->sslLocalCert = $path;

        return $this;
    }

    /**
     * Assign a callback before the request
     *
     * @param  Closure $closure
     *
     * @return $this
     */
    public function withBeforeRequestCallback(Closure $closure)
    {
        $this->beforeRequest = $closure;

        return $this;
    }

    /**
     * Get cookies
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Do the HTTP request
     *
     * @param string   $payload
     * @param string[] $headers Headers for this request
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws ConnectionFailureException
     * @throws ServerErrorException
     */
    public function execute($payload, array $headers = [])
    {
        if (is_callable($this->beforeRequest)) {
            call_user_func_array($this->beforeRequest, [$this, $payload, $headers]);
        }

        if ($this->isCurlLoaded()) {
            $ch = curl_init();
            $requestHeaders = $this->buildHeaders($headers);
            $headers = [];
            curl_setopt_array($ch, [
                CURLOPT_URL => trim($this->url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $this->timeout,
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_SSL_VERIFYPEER => $this->verifySslCertificate,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => $requestHeaders,
                CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$headers) {
                    $headers[] = rtrim($header, "\r\n");

                    return strlen($header);
                }
            ]);

            if ($this->sslLocalCert !== null) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->sslLocalCert);
            }

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response !== false) {
                $response = json_decode($response, true);
            } else {
                throw new ConnectionFailureException('Unable to establish a connection');
            }
        } else {
            $stream = fopen(trim($this->url), 'r', false, $this->buildContext($payload, $headers));

            if (! is_resource($stream)) {
                throw new ConnectionFailureException('Unable to establish a connection');
            }

            $metadata = stream_get_meta_data($stream);
            $headers = $metadata['wrapper_data'];
            $response = json_decode(stream_get_contents($stream), true);

            fclose($stream);
        }

        if ($this->debug) {
            error_log(sprintf(
                '==> Request: %s%s',
                PHP_EOL,
                (is_string($payload) ? $payload : json_encode($payload, JSON_PRETTY_PRINT))
            ));
            error_log(sprintf(
                '==> Headers: %s%s',
                PHP_EOL,
                var_export($headers, true)
            ));
            error_log(sprintf(
                '==> Response: %s%s',
                PHP_EOL,
                json_encode($response, JSON_PRETTY_PRINT)
            ));
        }

        $this->handleExceptions($headers);
        $this->parseCookies($headers);

        return $response;
    }

    /**
     * Prepare stream context
     *
     * @param  string   $payload
     * @param  string[] $headers
     *
     * @return resource
     */
    protected function buildContext($payload, array $headers = [])
    {
        $headers = $this->buildHeaders($headers);

        $options = [
            'http' => [
                'method' => 'POST',
                'protocol_version' => 1.1,
                'timeout' => $this->timeout,
                'max_redirects' => 2,
                'header' => implode("\r\n", $headers),
                'content' => $payload,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => $this->verifySslCertificate,
                'verify_peer_name' => $this->verifySslCertificate
            ]
        ];

        if ($this->sslLocalCert !== null) {
            $options['ssl']['local_cert'] = $this->sslLocalCert;
        }

        return stream_context_create($options);
    }

    /**
     * Parse cookies from response
     *
     * @param  array $headers
     */
    protected function parseCookies(array $headers)
    {
        foreach ($headers as $header) {
            $pos = stripos($header, 'Set-Cookie:');

            if ($pos !== false) {
                $cookies = explode(';', substr($header, $pos + 11));

                foreach ($cookies as $cookie) {
                    $item = explode('=', $cookie);

                    if (count($item) === 2) {
                        $name = trim($item[0]);
                        $value = $item[1];
                        $this->cookies[$name] = $value;
                    }
                }
            }
        }
    }

    /**
     * Throw an exception according the HTTP response
     *
     * @param array $headers
     *
     * @throws AccessDeniedException
     * @throws ConnectionFailureException
     * @throws ServerErrorException
     */
    public function handleExceptions(array $headers)
    {
        $exceptions = [
            '401' => '\JsonRPC\Exception\AccessDeniedException',
            '403' => '\JsonRPC\Exception\AccessDeniedException',
            '404' => '\JsonRPC\Exception\ConnectionFailureException',
            '500' => '\JsonRPC\Exception\ServerErrorException',
        ];

        foreach ($headers as $header) {
            foreach ($exceptions as $code => $exception) {
                if (strpos($header, 'HTTP/1.0 '.$code) !== false || strpos($header, 'HTTP/1.1 '.$code) !== false) {
                    throw new $exception('Response: '.$header);
                }
            }
        }
    }

    /**
     * Tests if the curl extension is loaded
     *
     * @return bool
     */
    protected function isCurlLoaded()
    {
        return extension_loaded('curl');
    }

    /**
     * Prepare Headers
     *
     * @param array $headers
     *
     * @return array
     */
    protected function buildHeaders(array $headers)
    {
        $headers = array_merge($this->headers, $headers);

        if (!empty($this->username) && !empty($this->password)) {
            $headers[] = 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password);
        }

        if (!empty($this->cookies)) {
            $cookies = [];

            foreach ($this->cookies as $key => $value) {
                $cookies[] = $key . '=' . $value;
            }

            $headers[] = 'Cookie: ' . implode('; ', $cookies);
        }
        return $headers;
    }
}
