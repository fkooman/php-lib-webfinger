# Introduction
This is a WebFinger (RFC 7033) client implementation written in PHP.

# Installation
You need [Composer](https://getcomposer.org) to install the dependencies:

    $ /path/to/composer.phar install

# Usage
The library `fkooman\WebFinger\WebFinger` can be used in your project.

# Compliancy Testing
A small script `rs-compliancy.php` is included that can be used to validate
WebFinger implementation of identity providers claiming to support
RemoteStorage:

	$ php rs-compliancy.php foo@example.org

