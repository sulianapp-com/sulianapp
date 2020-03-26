<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\XMLParser;

class Encoder
{
    /**
     * Takes an xmlrpc value in object format and translates it into native PHP types.
     *
     * Works with xmlrpc requests objects as input, too.
     *
     * Given proper options parameter, can rebuild generic php object instances
     * (provided those have been encoded to xmlrpc format using a corresponding
     * option in php_xmlrpc_encode())
     * PLEASE NOTE that rebuilding php objects involves calling their constructor function.
     * This means that the remote communication end can decide which php code will
     * get executed on your server, leaving the door possibly open to 'php-injection'
     * style of attacks (provided you have some classes defined on your server that
     * might wreak havoc if instances are built outside an appropriate context).
     * Make sure you trust the remote server/client before eanbling this!
     *
     * @author Dan Libby (dan@libby.com)
     *
     * @param Value|Request $xmlrpcVal
     * @param array $options if 'decode_php_objs' is set in the options array, xmlrpc structs can be decoded into php objects; if 'dates_as_objects' is set xmlrpc datetimes are decoded as php DateTime objects (standard is
     *
     * @return mixed
     */
    public function decode($xmlrpcVal, $options = array())
    {
        switch ($xmlrpcVal->kindOf()) {
            case 'scalar':
                if (in_array('extension_api', $options)) {
                    reset($xmlrpcVal->me);
                    list($typ, $val) = each($xmlrpcVal->me);
                    switch ($typ) {
                        case 'dateTime.iso8601':
                            $xmlrpcVal->scalar = $val;
                            $xmlrpcVal->type = 'datetime';
                            $xmlrpcVal->timestamp = \PhpXmlRpc\Helper\Date::iso8601Decode($val);

                            return $xmlrpcVal;
                        case 'base64':
                            $xmlrpcVal->scalar = $val;
                            $xmlrpcVal->type = $typ;

                            return $xmlrpcVal;
                        default:
                            return $xmlrpcVal->scalarval();
                    }
                }
                if (in_array('dates_as_objects', $options) && $xmlrpcVal->scalartyp() == 'dateTime.iso8601') {
                    // we return a Datetime object instead of a string
                    // since now the constructor of xmlrpc value accepts safely strings, ints and datetimes,
                    // we cater to all 3 cases here
                    $out = $xmlrpcVal->scalarval();
                    if (is_string($out)) {
                        $out = strtotime($out);
                    }
                    if (is_int($out)) {
                        $result = new \Datetime();
                        $result->setTimestamp($out);

                        return $result;
                    } elseif (is_a($out, 'Datetime')) {
                        return $out;
                    }
                }

                return $xmlrpcVal->scalarval();
            case 'array':
                $arr = array();
                foreach($xmlrpcVal as $value) {
                    $arr[] = $this->decode($value, $options);
                }

                return $arr;
            case 'struct':
                // If user said so, try to rebuild php objects for specific struct vals.
                /// @todo should we raise a warning for class not found?
                // shall we check for proper subclass of xmlrpc value instead of
                // presence of _php_class to detect what we can do?
                if (in_array('decode_php_objs', $options) && $xmlrpcVal->_php_class != ''
                    && class_exists($xmlrpcVal->_php_class)
                ) {
                    $obj = @new $xmlrpcVal->_php_class();
                    foreach ($xmlrpcVal as $key => $value) {
                        $obj->$key = $this->decode($value, $options);
                    }

                    return $obj;
                } else {
                    $arr = array();
                    foreach ($xmlrpcVal as $key => $value) {
                        $arr[$key] = $this->decode($value, $options);
                    }

                    return $arr;
                }
            case 'msg':
                $paramCount = $xmlrpcVal->getNumParams();
                $arr = array();
                for ($i = 0; $i < $paramCount; $i++) {
                    $arr[] = $this->decode($xmlrpcVal->getParam($i), $options);
                }

                return $arr;
        }
    }

    /**
     * Takes native php types and encodes them into xmlrpc PHP object format.
     * It will not re-encode xmlrpc value objects.
     *
     * Feature creep -- could support more types via optional type argument
     * (string => datetime support has been added, ??? => base64 not yet)
     *
     * If given a proper options parameter, php object instances will be encoded
     * into 'special' xmlrpc values, that can later be decoded into php objects
     * by calling php_xmlrpc_decode() with a corresponding option
     *
     * @author Dan Libby (dan@libby.com)
     *
     * @param mixed $phpVal the value to be converted into an xmlrpc value object
     * @param array $options can include 'encode_php_objs', 'auto_dates', 'null_extension' or 'extension_api'
     *
     * @return \PhpXmlrpc\Value
     */
    public function encode($phpVal, $options = array())
    {
        $type = gettype($phpVal);
        switch ($type) {
            case 'string':
                if (in_array('auto_dates', $options) && preg_match('/^[0-9]{8}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $phpVal)) {
                    $xmlrpcVal = new Value($phpVal, Value::$xmlrpcDateTime);
                } else {
                    $xmlrpcVal = new Value($phpVal, Value::$xmlrpcString);
                }
                break;
            case 'integer':
                $xmlrpcVal = new Value($phpVal, Value::$xmlrpcInt);
                break;
            case 'double':
                $xmlrpcVal = new Value($phpVal, Value::$xmlrpcDouble);
                break;
            // <G_Giunta_2001-02-29>
            // Add support for encoding/decoding of booleans, since they are supported in PHP
            case 'boolean':
                $xmlrpcVal = new Value($phpVal, Value::$xmlrpcBoolean);
                break;
            // </G_Giunta_2001-02-29>
            case 'array':
                // PHP arrays can be encoded to either xmlrpc structs or arrays,
                // depending on wheter they are hashes or plain 0..n integer indexed
                // A shorter one-liner would be
                // $tmp = array_diff(array_keys($phpVal), range(0, count($phpVal)-1));
                // but execution time skyrockets!
                $j = 0;
                $arr = array();
                $ko = false;
                foreach ($phpVal as $key => $val) {
                    $arr[$key] = $this->encode($val, $options);
                    if (!$ko && $key !== $j) {
                        $ko = true;
                    }
                    $j++;
                }
                if ($ko) {
                    $xmlrpcVal = new Value($arr, Value::$xmlrpcStruct);
                } else {
                    $xmlrpcVal = new Value($arr, Value::$xmlrpcArray);
                }
                break;
            case 'object':
                if (is_a($phpVal, 'PhpXmlRpc\Value')) {
                    $xmlrpcVal = $phpVal;
                } elseif (is_a($phpVal, 'DateTime')) {
                    $xmlrpcVal = new Value($phpVal->format('Ymd\TH:i:s'), Value::$xmlrpcStruct);
                } else {
                    $arr = array();
                    reset($phpVal);
                    while (list($k, $v) = each($phpVal)) {
                        $arr[$k] = $this->encode($v, $options);
                    }
                    $xmlrpcVal = new Value($arr, Value::$xmlrpcStruct);
                    if (in_array('encode_php_objs', $options)) {
                        // let's save original class name into xmlrpc value:
                        // might be useful later on...
                        $xmlrpcVal->_php_class = get_class($phpVal);
                    }
                }
                break;
            case 'NULL':
                if (in_array('extension_api', $options)) {
                    $xmlrpcVal = new Value('', Value::$xmlrpcString);
                } elseif (in_array('null_extension', $options)) {
                    $xmlrpcVal = new Value('', Value::$xmlrpcNull);
                } else {
                    $xmlrpcVal = new Value();
                }
                break;
            case 'resource':
                if (in_array('extension_api', $options)) {
                    $xmlrpcVal = new Value((int)$phpVal, Value::$xmlrpcInt);
                } else {
                    $xmlrpcVal = new Value();
                }
            // catch "user function", "unknown type"
            default:
                // giancarlo pinerolo <ping@alt.it>
                // it has to return
                // an empty object in case, not a boolean.
                $xmlrpcVal = new Value();
                break;
        }

        return $xmlrpcVal;
    }

    /**
     * Convert the xml representation of a method response, method request or single
     * xmlrpc value into the appropriate object (a.k.a. deserialize).
     *
     * @param string $xmlVal
     * @param array $options
     *
     * @return mixed false on error, or an instance of either Value, Request or Response
     */
    public function decodeXml($xmlVal, $options = array())
    {
        // 'guestimate' encoding
        $valEncoding = XMLParser::guessEncoding('', $xmlVal);
        if ($valEncoding != '') {

            // Since parsing will fail if charset is not specified in the xml prologue,
            // the encoding is not UTF8 and there are non-ascii chars in the text, we try to work round that...
            // The following code might be better for mb_string enabled installs, but
            // makes the lib about 200% slower...
            //if (!is_valid_charset($valEncoding, array('UTF-8'))
            if (!in_array($valEncoding, array('UTF-8', 'US-ASCII')) && !XMLParser::hasEncoding($xmlVal)) {
                if ($valEncoding == 'ISO-8859-1') {
                    $xmlVal = utf8_encode($xmlVal);
                } else {
                    if (extension_loaded('mbstring')) {
                        $xmlVal = mb_convert_encoding($xmlVal, 'UTF-8', $valEncoding);
                    } else {
                        error_log('XML-RPC: ' . __METHOD__ . ': invalid charset encoding of xml text: ' . $valEncoding);
                    }
                }
            }
        }

        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
        // What if internal encoding is not in one of the 3 allowed?
        // we use the broadest one, ie. utf8!
        if (!in_array(PhpXmlRpc::$xmlrpc_internalencoding, array('UTF-8', 'ISO-8859-1', 'US-ASCII'))) {
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
        } else {
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, PhpXmlRpc::$xmlrpc_internalencoding);
        }

        $xmlRpcParser = new XMLParser();
        xml_set_object($parser, $xmlRpcParser);

        xml_set_element_handler($parser, 'xmlrpc_se_any', 'xmlrpc_ee');
        xml_set_character_data_handler($parser, 'xmlrpc_cd');
        xml_set_default_handler($parser, 'xmlrpc_dh');
        if (!xml_parse($parser, $xmlVal, 1)) {
            $errstr = sprintf('XML error: %s at line %d, column %d',
                xml_error_string(xml_get_error_code($parser)),
                xml_get_current_line_number($parser), xml_get_current_column_number($parser));
            error_log($errstr);
            xml_parser_free($parser);

            return false;
        }
        xml_parser_free($parser);
        if ($xmlRpcParser->_xh['isf'] > 1) {
            // test that $xmlrpc->_xh['value'] is an obj, too???

            error_log($xmlRpcParser->_xh['isf_reason']);

            return false;
        }
        switch ($xmlRpcParser->_xh['rt']) {
            case 'methodresponse':
                $v = &$xmlRpcParser->_xh['value'];
                if ($xmlRpcParser->_xh['isf'] == 1) {
                    $vc = $v['faultCode'];
                    $vs = $v['faultString'];
                    $r = new Response(0, $vc->scalarval(), $vs->scalarval());
                } else {
                    $r = new Response($v);
                }

                return $r;
            case 'methodcall':
                $req = new Request($xmlRpcParser->_xh['method']);
                for ($i = 0; $i < count($xmlRpcParser->_xh['params']); $i++) {
                    $req->addParam($xmlRpcParser->_xh['params'][$i]);
                }

                return $req;
            case 'value':
                return $xmlRpcParser->_xh['value'];
            default:
                return false;
        }
    }

}
