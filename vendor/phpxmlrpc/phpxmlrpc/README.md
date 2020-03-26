XMLRPC for PHP
==============
A php library for building xml-rpc clients and servers.

Installation
------------
The recommended way to install this library is using Composer.

Detailed installation instructions are in the [INSTALL.md](INSTALL.md) file, along with system requirements listing.

Documentation
-------------

*NB: the user manual has not been updated yet with all the changes made in version 4. Please consider it unreliable!*

*You are encouraged to look instead the code examples found in the demo/ directory*

The user manual can be found in the doc/manual directory, in Asciidoc format: [phpxmlrpc_manual.adoc](doc/manual/phpxmlrpc_manual.adoc)

Release tarballs also contain the HTML and PDF versions, as well as an automatically generated API documentation.

Upgrading
---------
If you are upgrading from version 3 or earlier you have two options:

1. adapt your code to the new API (all changes needed are described in [doc/api_changes_v4.md](doc/api_changes_v4.md))

2. use instead the *compatibility layer* which is provided. Instructions and pitfalls described in [doc/api_changes_v4.md](doc/api_changes_v4.md##enabling-compatibility-with-legacy-code)

In any case, read carefully the docs in [doc/api_changes_v4.md](doc/api_changes_v4.md) and report back any undocumented
issue using GitHub.

License
-------
Use of this software is subject to the terms in the [license.txt](license.txt) file

SSL-certificate
---------------
The passphrase for the rsakey.pem certificate is 'test'.


[![License](https://poser.pugx.org/phpxmlrpc/phpxmlrpc/license)](https://packagist.org/packages/phpxmlrpc/phpxmlrpc)
[![Latest Stable Version](https://poser.pugx.org/phpxmlrpc/phpxmlrpc/v/stable)](https://packagist.org/packages/phpxmlrpc/phpxmlrpc)
[![Total Downloads](https://poser.pugx.org/phpxmlrpc/phpxmlrpc/downloads)](https://packagist.org/packages/phpxmlrpc/phpxmlrpc)

[![Build Status](https://travis-ci.org/gggeek/phpxmlrpc.svg?branch=php53)](https://travis-ci.org/gggeek/phpxmlrpc)
[![Test Coverage](https://codeclimate.com/github/gggeek/phpxmlrpc/badges/coverage.svg)](https://codeclimate.com/github/gggeek/phpxmlrpc)
[![Code Coverage](https://scrutinizer-ci.com/g/gggeek/phpxmlrpc/badges/coverage.png?b=php53)](https://scrutinizer-ci.com/g/gggeek/phpxmlrpc/?branch=php53)
