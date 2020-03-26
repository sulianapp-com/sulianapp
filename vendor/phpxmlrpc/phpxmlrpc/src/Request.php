<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;
use PhpXmlRpc\Helper\Http;
use PhpXmlRpc\Helper\Logger;
use PhpXmlRpc\Helper\XMLParser;

class Request
{
    /// @todo: do these need to be public?
    public $payload;
    public $methodname;
    public $params = array();
    public $debug = 0;
    public $content_type = 'text/xml';

    // holds data while parsing the response. NB: Not a full Response object
    protected $httpResponse = array();

    /**
     * @param string $methodName the name of the method to invoke
     * @param Value[] $params array of parameters to be passed to the method (Value objects)
     */
    public function __construct($methodName, $params = array())
    {
        $this->methodname = $methodName;
        foreach ($params as $param) {
            $this->addParam($param);
        }
    }

    public function xml_header($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            return "<?xml version=\"1.0\" encoding=\"$charsetEncoding\" ?" . ">\n<methodCall>\n";
        } else {
            return "<?xml version=\"1.0\"?" . ">\n<methodCall>\n";
        }
    }

    public function xml_footer()
    {
        return '</methodCall>';
    }

    public function createPayload($charsetEncoding = '')
    {
        if ($charsetEncoding != '') {
            $this->content_type = 'text/xml; charset=' . $charsetEncoding;
        } else {
            $this->content_type = 'text/xml';
        }
        $this->payload = $this->xml_header($charsetEncoding);
        $this->payload .= '<methodName>' . Charset::instance()->encodeEntities($this->methodname, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</methodName>\n";
        $this->payload .= "<params>\n";
        foreach ($this->params as $p) {
            $this->payload .= "<param>\n" . $p->serialize($charsetEncoding) .
                "</param>\n";
        }
        $this->payload .= "</params>\n";
        $this->payload .= $this->xml_footer();
    }

    /**
     * Gets/sets the xmlrpc method to be invoked.
     *
     * @param string $methodName the method to be set (leave empty not to set it)
     *
     * @return string the method that will be invoked
     */
    public function method($methodName = '')
    {
        if ($methodName != '') {
            $this->methodname = $methodName;
        }

        return $this->methodname;
    }

    /**
     * Returns xml representation of the message. XML prologue included.
     *
     * @param string $charsetEncoding
     *
     * @return string the xml representation of the message, xml prologue included
     */
    public function serialize($charsetEncoding = '')
    {
        $this->createPayload($charsetEncoding);

        return $this->payload;
    }

    /**
     * Add a parameter to the list of parameters to be used upon method invocation.
     *
     * @param Value $param
     *
     * @return boolean false on failure
     */
    public function addParam($param)
    {
        // add check: do not add to self params which are not xmlrpc values
        if (is_object($param) && is_a($param, 'PhpXmlRpc\Value')) {
            $this->params[] = $param;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the nth parameter in the request. The index zero-based.
     *
     * @param integer $i the index of the parameter to fetch (zero based)
     *
     * @return Value the i-th parameter
     */
    public function getParam($i)
    {
        return $this->params[$i];
    }

    /**
     * Returns the number of parameters in the message.
     *
     * @return integer the number of parameters currently set
     */
    public function getNumParams()
    {
        return count($this->params);
    }

    /**
     * Given an open file handle, read all data available and parse it as an xmlrpc response.
     * NB: the file handle is not closed by this function.
     * NNB: might have trouble in rare cases to work on network streams, as we
     *      check for a read of 0 bytes instead of feof($fp).
     *      But since checking for feof(null) returns false, we would risk an
     *      infinite loop in that case, because we cannot trust the caller
     *      to give us a valid pointer to an open file...
     *
     * @param resource $fp stream pointer
     *
     * @return Response
     *
     * @todo add 2nd & 3rd param to be passed to ParseResponse() ???
     */
    public function parseResponseFile($fp)
    {
        $ipd = '';
        while ($data = fread($fp, 32768)) {
            $ipd .= $data;
        }
        return $this->parseResponse($ipd);
    }

    /**
     * Parse the xmlrpc response contained in the string $data and return a Response object.
     *
     * @param string $data the xmlrpc response, eventually including http headers
     * @param bool $headersProcessed when true prevents parsing HTTP headers for interpretation of content-encoding and consequent decoding
     * @param string $returnType decides return type, i.e. content of response->value(). Either 'xmlrpcvals', 'xml' or 'phpvals'
     *
     * @return Response
     */
    public function parseResponse($data = '', $headersProcessed = false, $returnType = 'xmlrpcvals')
    {
        if ($this->debug) {
            Logger::instance()->debugMessage("---GOT---\n$data\n---END---");
        }

        $this->httpResponse = array('raw_data' => $data, 'headers' => array(), 'cookies' => array());

        if ($data == '') {
            error_log('XML-RPC: ' . __METHOD__ . ': no response received from server.');
            return new Response(0, PhpXmlRpc::$xmlrpcerr['no_data'], PhpXmlRpc::$xmlrpcstr['no_data']);
        }

        // parse the HTTP headers of the response, if present, and separate them from data
        if (substr($data, 0, 4) == 'HTTP') {
            $httpParser = new Http();
            try {
                $this->httpResponse = $httpParser->parseResponseHeaders($data, $headersProcessed, $this->debug);
            } catch(\Exception $e) {
                $r = new Response(0, $e->getCode(), $e->getMessage());
                // failed processing of HTTP response headers
                // save into response obj the full payload received, for debugging
                $r->raw_data = $data;

                return $r;
            }
        }

        // be tolerant of extra whitespace in response body
        $data = trim($data);

        /// @todo return an error msg if $data=='' ?

        // be tolerant of junk after methodResponse (e.g. javascript ads automatically inserted by free hosts)
        // idea from Luca Mariano <luca.mariano@email.it> originally in PEARified version of the lib
        $pos = strrpos($data, '</methodResponse>');
        if ($pos !== false) {
            $data = substr($data, 0, $pos + 17);
        }

        // try to 'guestimate' the character encoding of the received response
        $respEncoding = XMLParser::guessEncoding(@$this->httpResponse['headers']['content-type'], $data);

        if ($this->debug) {
            $start = strpos($data, '<!-- SERVER DEBUG INFO (BASE64 ENCODED):');
            if ($start) {
                $start += strlen('<!-- SERVER DEBUG INFO (BASE64 ENCODED):');
                $end = strpos($data, '-->', $start);
                $comments = substr($data, $start, $end - $start);
                Logger::instance()->debugMessage("---SERVER DEBUG INFO (DECODED) ---\n\t" .
                    str_replace("\n", "\n\t", base64_decode($comments)) . "\n---END---", $respEncoding);
            }
        }

        // if user wants back raw xml, give it to him
        if ($returnType == 'xml') {
            $r = new Response($data, 0, '', 'xml');
            $r->hdrs = $this->httpResponse['headers'];
            $r->_cookies = $this->httpResponse['cookies'];
            $r->raw_data = $this->httpResponse['raw_data'];

            return $r;
        }

        if ($respEncoding != '') {

            // Since parsing will fail if charset is not specified in the xml prologue,
            // the encoding is not UTF8 and there are non-ascii chars in the text, we try to work round that...
            // The following code might be better for mb_string enabled installs, but
            // makes the lib about 200% slower...
            //if (!is_valid_charset($respEncoding, array('UTF-8')))
            if (!in_array($respEncoding, array('UTF-8', 'US-ASCII')) && !XMLParser::hasEncoding($data)) {
                if ($respEncoding == 'ISO-8859-1') {
                    $data = utf8_encode($data);
                } else {
                    if (extension_loaded('mbstring')) {
                        $data = mb_convert_encoding($data, 'UTF-8', $respEncoding);
                    } else {
                        error_log('XML-RPC: ' . __METHOD__ . ': invalid charset encoding of received response: ' . $respEncoding);
                    }
                }
            }
        }

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        // G. Giunta 2005/02/13: PHP internally uses ISO-8859-1, so we have to tell
        // the xml parser to give us back data in the expected charset.
        // What if internal encoding is not in one of the 3 allowed?
        // we use the broadest one, ie. utf8
        // This allows to send data which is native in various charset,
        // by extending xmlrpc_encode_entities() and setting xmlrpc_internalencoding
        if (!in_array(PhpXmlRpc::$xmlrpc_internalencoding, array('UTF-8', 'ISO-8859-1', 'US-ASCII'))) {
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        } else {
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, PhpXmlRpc::$xmlrpc_internalencoding);
        }

        $xmlRpcParser = new XMLParser();
        xml_set_object($parser, $xmlRpcParser);

        if ($returnType == 'phpvals') {
            xml_set_element_handler($parser, 'xmlrpc_se', 'xmlrpc_ee_fast');
        } else {
            xml_set_element_handler($parser, 'xmlrpc_se', 'xmlrpc_ee');
        }

        xml_set_character_data_handler($parser, 'xmlrpc_cd');
        xml_set_default_handler($parser, 'xmlrpc_dh');

        // first error check: xml not well formed
        if (!xml_parse($parser, $data, count($data))) {
            // thanks to Peter Kocks <peter.kocks@baygate.com>
            if ((xml_get_current_line_number($parser)) == 1) {
                $errStr = 'XML error at line 1, check URL';
            } else {
                $errStr = sprintf('XML error: %s at line %d, column %d',
                    xml_error_string(xml_get_error_code($parser)),
                    xml_get_current_line_number($parser), xml_get_current_column_number($parser));
            }
            error_log($errStr);
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['invalid_return'], PhpXmlRpc::$xmlrpcstr['invalid_return'] . ' (' . $errStr . ')');
            xml_parser_free($parser);
            if ($this->debug) {
                print $errStr;
            }
            $r->hdrs = $this->httpResponse['headers'];
            $r->_cookies = $this->httpResponse['cookies'];
            $r->raw_data = $this->httpResponse['raw_data'];

            return $r;
        }
        xml_parser_free($parser);
        // second error check: xml well formed but not xml-rpc compliant
        if ($xmlRpcParser->_xh['isf'] > 1) {
            if ($this->debug) {
                /// @todo echo something for user?
            }

            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['invalid_return'],
                PhpXmlRpc::$xmlrpcstr['invalid_return'] . ' ' . $xmlRpcParser->_xh['isf_reason']);
        }
        // third error check: parsing of the response has somehow gone boink.
        // NB: shall we omit this check, since we trust the parsing code?
        elseif ($returnType == 'xmlrpcvals' && !is_object($xmlRpcParser->_xh['value'])) {
            // something odd has happened
            // and it's time to generate a client side error
            // indicating something odd went on
            $r = new Response(0, PhpXmlRpc::$xmlrpcerr['invalid_return'],
                PhpXmlRpc::$xmlrpcstr['invalid_return']);
        } else {
            if ($this->debug > 1) {
                Logger::instance()->debugMessage(
                    "---PARSED---\n".var_export($xmlRpcParser->_xh['value'], true)."\n---END---"
                );
            }

            // note that using =& will raise an error if $xmlRpcParser->_xh['st'] does not generate an object.
            $v = &$xmlRpcParser->_xh['value'];

            if ($xmlRpcParser->_xh['isf']) {
                /// @todo we should test here if server sent an int and a string, and/or coerce them into such...
                if ($returnType == 'xmlrpcvals') {
                    $errNo_v = $v['faultCode'];
                    $errStr_v = $v['faultString'];
                    $errNo = $errNo_v->scalarval();
                    $errStr = $errStr_v->scalarval();
                } else {
                    $errNo = $v['faultCode'];
                    $errStr = $v['faultString'];
                }

                if ($errNo == 0) {
                    // FAULT returned, errno needs to reflect that
                    $errNo = -1;
                }

                $r = new Response(0, $errNo, $errStr);
            } else {
                $r = new Response($v, 0, '', $returnType);
            }
        }

        $r->hdrs = $this->httpResponse['headers'];
        $r->_cookies = $this->httpResponse['cookies'];
        $r->raw_data = $this->httpResponse['raw_data'];

        return $r;
    }

    /**
     * Kept the old name even if Request class was renamed, for compatibility.
     *
     * @return string
     */
    public function kindOf()
    {
        return 'msg';
    }

    /**
     * Enables/disables the echoing to screen of the xmlrpc responses received.
     *
     * @param integer $in values 0, 1, 2 are supported
     */
    public function setDebug($in)
    {
        $this->debug = $in;
    }
}
