# Indigo Supervisor

[![Latest Version](https://img.shields.io/github/release/indigophp/supervisor.svg?style=flat-square)](https://github.com/indigophp/supervisor/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/indigophp/supervisor.svg?style=flat-square)](https://travis-ci.org/indigophp/supervisor)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/indigophp/supervisor.svg?style=flat-square)](https://scrutinizer-ci.com/g/indigophp/supervisor)
[![Quality Score](https://img.shields.io/scrutinizer/g/indigophp/supervisor.svg?style=flat-square)](https://scrutinizer-ci.com/g/indigophp/supervisor)
[![HHVM Status](https://img.shields.io/hhvm/indigophp/supervisor.svg?style=flat-square)](http://hhvm.h4cc.de/package/indigophp/supervisor)
[![Total Downloads](https://img.shields.io/packagist/dt/indigophp/supervisor.svg?style=flat-square)](https://packagist.org/packages/indigophp/supervisor)
[![Dependency Status](https://img.shields.io/versioneye/d/php/indigophp:supervisor.svg?style=flat-square)](https://www.versioneye.com/php/indigophp:supervisor)

**PHP library for managing supervisord through XML-RPC API.**


## Install

Via Composer

``` bash
$ composer require indigophp/supervisor
```


## Usage

``` php
use Indigo\Supervisor\Supervisor;
use Indigo\Supervisor\Connector\XmlRpc;
use fXmlRpc\Client;
use fXmlRpc\Transport\Guzzle4Bridge;

// Pass the url and the bridge to the XmlRpc Client
$client = new Client(
	'http://127.0.0.1:9001/RPC2',
	new Guzzle4Bridge(new \GuzzleHttp\Client(['defaults' => ['auth' => ['user', '123']]]))
);

// Pass the client to the connector
// See the full list of connectors bellow
$connector = new XmlRpc($client);

$supervisor = new Supervisor($connector);

// returns Process object
$process = $supervisor->getProcess('test_process');

// returns array of process info
$supervisor->getProcessInfo('test_process');

// same as $supervisor->stopProcess($process);
$supervisor->stopProcess('test_process');

// Don't wait for process start, return immediately
$supervisor->startProcess($process, false);

// returns true if running
// same as $process->checkState(Process::RUNNING);
$process->isRunning();

// returns process name
echo $process;

// returns process information
$process->getPayload();
```

**Currently available connectors:**

* [fXmlRpc](https://github.com/lstrojny/fxmlrpc)
* Zend XML-RPC

**Note:** fXmlRpc can be used with several HTTP Clients. See the list on it's website. This is the reason why Client specific connectors has been removed.


### Authentication

As of version 3.0.0 `setCredentials` is no longer part of the `Connector` interface (meaning responsibility has been fully removed).You have to provide authentication data to the HTTP Client of your choice. (For example Guzzle supports it out-of-the-box) Also, Bridges implemented by fXmlRpc supports to set custom headers.


### Exception handling

For each possible fault response there is an exception. These exceptions extend a [common exception](src/Exception/Fault.php), so you are able to catch a specific fault or all. When an unknown fault is returned from the server, an instance if the common exception is thrown. The list of fault responses and the appropriate exception can be found in the class.

``` php
use Indigo\Supervisor\Exception\Fault;
use Indigo\Supervisor\Exception\Fault\BadName;

try {
	$supervisor->restart('process');
} catch (BadName $e) {
	// handle bad name error here
} catch (Fault $e) {
	// handle any other errors here
}
```

**For developers:** Fault exceptions are automatically generated, there is no need to manually modify them.


## Configuration and Event listening

[Configuration](https://github.com/indigophp/supervisor-configuration) and [Event](https://github.com/indigophp/supervisor-event) components have been moved into their own repository. See [#24](https://github.com/indigophp/supervisor/issues/24) for explanation.


## Further info

You can find the XML-RPC documentation here:
[http://supervisord.org/api.html](http://supervisord.org/api.html)


## Notice

If you use PHP XML-RPC extension to parse responses (which is marked as *EXPERIMENTAL*). This can cause issues when you are trying to read/tail log of a PROCESS. Make sure you clean your log messages. The only information I found about this is a [comment](http://www.php.net/function.xmlrpc-decode#44213).

You will also have to make sure that you always call the functions with correct parameters. `Zend` connector will trigger an error when incorrect parameters are passed. See [this](https://github.com/zendframework/zf2/issues/6455) issue for details. (Probably this won't change in near future based on my inspections of the code.) Other connectors will throw a `Fault` exception.


## Bundles

Here is a list of framework specific bundle packages:

- [HumusSupervisorModule](https://github.com/prolic/HumusSupervisorModule) *(Zend Framework 2)*
- [Fuel Supervisor](https://github.com/indigophp/fuel-supervisor) *(FuelPHP 1.x)*


## Testing

``` bash
$ phpspec run
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/supervisor/contributors)


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
