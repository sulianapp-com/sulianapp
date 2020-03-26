API Changes between library versions 3 and 4
============================================

Class loading
-------------

It is not necessary any more to include the files xmlrpc.inc, xmlrpcs.inc and xmlrpc_wrappers.inc to have the
library classes available.

Instead, it is recommended to rely on class autoloading.

* If you are using Composer, just install the library by declaring it as dependency for your project in composer.json

        "require": {
            ...,
            "phpxmlrpc/phpxmlrpc": "~4.0"
        },

* If you do not use Composer, an autoloader for the library can be found in src/Atuloader.php.
  The php example files in the demo/client folder do make use of it.
  Example code to set up the autoloader:

        include_once <path to library> . "/src/Autoloader.php";
        PhpXmlRpc\Autoloader::register();


* If you still include manually xmlrpc.inc, xmlrpcs.inc or xmlrpc_wrappers.inc, you will not need to set up
  class autoloading, as those files do include all the source files for the library classes


New class naming
----------------

All classes have ben renamed, are now properly namespaced and follow the CamelCase naming convention.
Existing class methods and members have been preserved; all new method names follow camelCase.

Conversion table:

| Old class     | New class          | Notes                                 |
| ------------- | ------------------ | ------------------------------------- |
| xmlrpc_client | PhpXmlRpc\Client   |                                       |
| xmlrpc_server | PhpXmlRpc\Server   | Removed method: echoInput             |
| xmlrpcmsg     | PhpXmlRpc\Request  |                                       |
| xmlrpcresp    | PhpXmlRpc\Response |                                       |
| xmlrpcval     | PhpXmlRpc\Value    | Removed methods: serializeval, getval |


New class methods
-----------------

In case you had extended the classes of the library and added methods to the subclasses, you might find that your
implementation clashes with the new one if you implemented:


| Class     | Method       | Notes                                   |
| --------- | ------------ | --------------------------------------- |
| xmlrpcval | count        | implements interface: Countable         |
| xmlrpcval | getIterator  | implements interface: IteratorAggregate |
| xmlrpcval | offsetExists | implements interface: ArrayAccess       |
| xmlrpcval | offsetGet    | implements interface: ArrayAccess       |
| xmlrpcval | offsetSet    | implements interface: ArrayAccess       |
| xmlrpcval | offsetUnset  | implements interface: ArrayAccess       |


Global variables cleanup
------------------------

All variables in the global scope have been moved into classes.

Conversion table:

| Old variable             | New variable                                | Notes     |
| ------------------------ | ------------------------------------------- | --------- |
| _xmlrpc_debuginfo        | PhpXmlRpc\Server::$_xmlrpc_debuginfo        | protected |
| _xmlrpcs_capabilities    | NOT AVAILABLE YET                           |           |
| _xmlrpcs_dmap            | NOT AVAILABLE YET                           |           |
| _xmlrpcs_occurred_errors | PhpXmlRpc\Server::$_xmlrpcs_occurred_errors | protected |
| _xmlrpcs_prev_ehandler   | PhpXmlRpc\Server::$_xmlrpcs_prev_ehandler   | protected |
| xmlrpcWPFObjHolder       | PhpXmlRpc\Wrapper::$objHolder               |           |
| ...                      |                                             |           |


Global functions cleanup
------------------------

Most functions in the global scope have been moved into classes.
Some have been slightly changed.

| Old function                     | New function                                | Notes                                                  |
| -------------------------------- | ------------------------------------------- | ------------------------------------------------------ |
| build_client_wrapper_code        | none                                        |                                                        |
| build_remote_method_wrapper_code | PhpXmlRpc\Wrapper::buildWrapMethodSource    | signature changed                                      |
| decode_chunked                   | PhpXmlRpc\Helper\Http::decodeChunked        |                                                        |
| guess_encoding                   | PhpXmlRpc\Helper\XMLParser::guessEncoding   |                                                        |
| has_encoding                     | PhpXmlRpc\Helper\XMLParser::hasEncoding     |                                                        |
| is_valid_charset                 | PhpXmlRpc\Helper\Charset::isValidCharset    |                                                        |
| iso8601_decode                   | PhpXmlRpc\Helper\Date::iso8601Decode        |                                                        |
| iso8601_encode                   | PhpXmlRpc\Helper\Date::iso8601Encode        |                                                        |
| php_2_xmlrpc_type                | PhpXmlRpc\Wrapper::php2XmlrpcType           |                                                        |
| php_xmlrpc_decode                | PhpXmlRpc\Encoder::decode                   |                                                        |
| php_xmlrpc_decode_xml            | PhpXmlRpc\Encoder::decodeXml                |                                                        |
| php_xmlrpc_encode                | PhpXmlRpc\Encoder::encode                   |                                                        |
| wrap_php_class                   | PhpXmlRpc\Wrapper::wrapPhpClass             | returns closures instead of function names by default  |
| wrap_php_function                | PhpXmlRpc\Wrapper::wrapPhpFunction          | returns closures instead of function names by default  |
| wrap_xmlrpc_method               | PhpXmlRpc\Wrapper::wrapXmrlpcMethod         | returns closures instead of function names by default  |
| wrap_xmlrpc_server               | PhpXmlRpc\Wrapper::wrapXmrlpcServer         | returns closures instead of function names by default; |
|                                  |                                             | returns an array ready for usage in dispatch map       |
| xmlrpc_2_php_type                | PhpXmlRpc\Wrapper::Xmlrpc2phpType           |                                                        |
| xmlrpc_debugmsg                  | PhpXmlRpc\Server::xmlrpc_debugmsg           |                                                        |
| xmlrpc_encode_entitites          | PhpXmlRpc\Helper\Charset::encodeEntitites   |                                                        |


Character sets and encoding
---------------------------

The default character set used by the library to deliver data to your app is now UTF8.
It is also the character set that the library expects data from your app to be in (including method names).
The value can be changed (to either US-ASCII or ISO-8859-1) by setting the desired value to
    PhpXmlRpc\PhpXmlRpc::$xmlrpc_internalencoding

Usage of closures for wrapping
------------------------------

...


Differences in server behaviour
-------------------------------

The results for calls to system.listMethods and system.getCapabilities can not be set anymore via changes to
global variables.


Other
-----

* when serialize() is invoked on a response and its payload can not be serialized, an exception is thrown instead of
  ending all execution

* all error messages now mention the class and method which generated them

* all library source code has been moved to the src/ directory

* all source code has been reformatted according to modern PSR standards


Enabling compatibility with legacy code
---------------------------------------

If you have code which relies on version 3 of the phpxmlrpc API, you *should* be able to use version 4 as a drop-in
replacement, regardless of all of the changes mentioned above.

The magic happens via the xmlrpc.inc, xmlrpcs.inc and xmlrpc_wrappers.inc files, which have been kept solely for
the purpose of backwards compatibility (you might notice that they are still in the 'lib' directory, whereas all of
the refactored code now sits in the 'src' directory).

Of course, some minor changes where inevitable, and backwards compatibility can not be guaranteed at 100%.
Below is the list of all known changes and possible pitfalls when enabling 'compatibility mode'.

### Default character set used for application data

* when including the xmlrpc.inc file, the defalt character set used by the lib to give data to your app gets switched
  back to ISO-8859-1, as it was in previous versions

* if yor app used to change that value, you will need to add one line to your code, to make sure it is properly used

        // code as was before
        include('xmlrpc.inc');
        $GLOBALS['xmlrpc_internalencoding'] = 'UTF-8';
        // new line needed now
        PhpXmlRpc\PhpXmlRpc::importGlobals();

### Usage of global variables

* ALL global variables which existed after including xmlrpc.inc in version 3 still do exist after including it in v. 4

* Code which relies on using (as in 'reading') their value will keep working unchanged

* Changing the value of some of those variables does not have any effect anymore on library operation.
  This is true for:

        $GLOBALS['xmlrpcI4']
        $GLOBALS['xmlrpcInt']
        $GLOBALS['xmlrpcBoolean']
        $GLOBALS['xmlrpcDouble']
        $GLOBALS['xmlrpcString']
        $GLOBALS['xmlrpcDatetTme']
        $GLOBALS['xmlrpcBase64']
        $GLOBALS['xmlrpcArray']
        $GLOBALS['xmlrpcStruct']
        $GLOBALS['xmlrpcValue']
        $GLOBALS['xmlrpcNull']
        $GLOBALS['xmlrpcTypes']
        $GLOBALS['xmlrpc_valid_parents']
        $GLOBALS['xml_iso88591_Entities']

* Changing the value of the other global variables will still have an effect on operation of the library, but only after
  a call to PhpXmlRpc::importGlobals()

    Example:

        // code as was before
        include('xmlrpc.inc');
        $GLOBALS['xmlrpc_null_apache_encoding'] = true;
        // new line needed now
        PhpXmlRpc\PhpXmlRpc::importGlobals();

    Alternative solution:

        include('xmlrpc.inc');
        PhpXmlRpc\PhpXmlRpc::$xmlrpc_null_apache_encoding = true;

* Not all variables which existed after including xmlrpcs.inc in version 3 are available

    - $GLOBALS['_xmlrpcs_prev_ehandler'] has been replaced with protected static var PhpXmlRpc\Server::$_xmlrpcs_prev_ehandler
        and is thus not available any more

    - same for $GLOBALS['_xmlrpcs_occurred_errors']

    - same for $GLOBALS['_xmlrpc_debuginfo']

    - $GLOBALS['_xmlrpcs_capabilities'] and $GLOBALS['_xmlrpcs_dmap'] have been removed

### Using typeof/class-name checks in your code

* if you are checking the types of returned objects, your checks will most likely fail.
  This is due to the fact that 'old' classes extend the 'new' versions, but library code that creates object
  instances will return the new classes.

    Example:

        is_a(php_xmlrpc_encode('hello world'), 'xmlrpcval') => false
        is_a(php_xmlrpc_encode('hello world'), 'PhpXmlRpc\Value') => true

### server behaviour can not be changed by setting global variables (the ones starting with _xmlrpcs_ )

might be fixed later?
