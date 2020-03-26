<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;

class Response
{
    /// @todo: do these need to be public?
    public $val = 0;
    public $valType;
    public $errno = 0;
    public $errstr = '';
    public $payload;
    public $hdrs = array();
    public $_cookies = array();
    public $content_type = 'text/xml';
    public $raw_data = '';

    /**
     * @param mixed $val either an xmlrpc value obj, a php value or the xml serialization of an xmlrpc value (a string)
     * @param integer $fCode set it to anything but 0 to create an error response
     * @param string $fString the error string, in case of an error response
     * @param string $valType either 'xmlrpcvals', 'phpvals' or 'xml'
     *
     * @todo add check that $val / $fCode / $fString is of correct type???
     * NB: as of now we do not do it, since it might be either an xmlrpc value or a plain
     * php val, or a complete xml chunk, depending on usage of Client::send() inside which creator is called...
     */
    public function __construct($val, $fCode = 0, $fString = '', $valType = '')
    {
        if ($fCode != 0) {
            // error response
            $this->errno = $fCode;
            $this->errstr = $fString;
        } else {
            // successful response
            $this->val = $val;
            if ($valType == '') {
                // user did not declare type of response value: try to guess it
                if (is_object($this->val) && is_a($this->val, 'PhpXmlRpc\Value')) {
                    $this->valtyp = 'xmlrpcvals';
                } elseif (is_string($this->val)) {
                    $this->valtyp = 'xml';
                } else {
                    $this->valtyp = 'phpvals';
                }
            } else {
                // user declares type of resp value: believe him
                $this->valtyp = $valType;
            }
        }
    }

    /**
     * Returns the error code of the response.
     *
     * @return integer the error code of this response (0 for not-error responses)
     */
    public function faultCode()
    {
        return $this->errno;
    }

    /**
     * Returns the error code of the response.
     *
     * @return string the error string of this response ('' for not-error responses)
     */
    public function faultString()
    {
        return $this->errstr;
    }

    /**
     * Returns the value received by the server.
     *
     * @return Value|string|mixed the xmlrpc value object returned by the server. Might be an xml string or php value if the response has been created by specially configured Client objects
     */
    public function value()
    {
        return $this->val;
    }

    /**
     * Returns an array with the cookies received from the server.
     * Array has the form: $cookiename => array ('value' => $val, $attr1 => $val1, $attr2 = $val2, ...)
     * with attributes being e.g. 'expires', 'path', domain'.
     * NB: cookies sent as 'expired' by the server (i.e. with an expiry date in the past)
     * are still present in the array. It is up to the user-defined code to decide
     * how to use the received cookies, and whether they have to be sent back with the next
     * request to the server (using Client::setCookie) or not.
     *
     * @return array array of cookies received from the server
     */
    public function cookies()
    {
        return $this->_cookies;
    }

    /**
     * Returns xml representation of the response. XML prologue not included.
     *
     * @param string $charsetEncoding the charset to be used for serialization. if null, US-ASCII is assumed
     *
     * @return string the xml representation of the response
     *
     * @throws \Exception
     */
    public function serialize($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            $this->content_type = 'text/xml; charset=' . $charsetEncoding;
        } else {
            $this->content_type = 'text/xml';
        }
        if (PhpXmlRpc::$xmlrpc_null_apache_encoding) {
            $result = "<methodResponse xmlns:ex=\"" . PhpXmlRpc::$xmlrpc_null_apache_encoding_ns . "\">\n";
        } else {
            $result = "<methodResponse>\n";
        }
        if ($this->errno) {
            // G. Giunta 2005/2/13: let non-ASCII response messages be tolerated by clients
            // by xml-encoding non ascii chars
            $result .= "<fault>\n" .
                "<value>\n<struct><member><name>faultCode</name>\n<value><int>" . $this->errno .
                "</int></value>\n</member>\n<member>\n<name>faultString</name>\n<value><string>" .
                Charset::instance()->encodeEntities($this->errstr, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</string></value>\n</member>\n" .
                "</struct>\n</value>\n</fault>";
        } else {
            if (!is_object($this->val) || !is_a($this->val, 'PhpXmlRpc\Value')) {
                if (is_string($this->val) && $this->valtyp == 'xml') {
                    $result .= "<params>\n<param>\n" .
                        $this->val .
                        "</param>\n</params>";
                } else {
                    /// @todo try to build something serializable?
                    throw new \Exception('cannot serialize xmlrpc response objects whose content is native php values');
                }
            } else {
                $result .= "<params>\n<param>\n" .
                    $this->val->serialize($charsetEncoding) .
                    "</param>\n</params>";
            }
        }
        $result .= "\n</methodResponse>";
        $this->payload = $result;

        return $result;
    }
}
