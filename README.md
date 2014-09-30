# Introduction
This is a WebFinger (RFC 7033) client implementation written in PHP.

# Installation
You need [Composer](https://getcomposer.org) to install the dependencies:

    $ /path/to/composer.phar install

# Usage
The library `fkooman\WebFinger\WebFinger` can be used in your project.

# API

    <?php
    require_once 'vendor/autoload.php';

    use fkooman\WebFinger\WebFinger;
    use fkooman\WebFinger\Exception\WebFingerException;

    try { 
        $w = new WebFinger(
            array(
                "verify" => true,
                "ignore_media_type" => false
            )
        );
        var_dump($w->discover("fkooman@5apps.com"));
    } catch (WebFingerException $e) {
        echo $e->getMessage() . PHP_EOL;
    } 

# Compliancy Testing
A script `compliancy.php` is included to check a WebFinger server
implementation:

	$ php compliancy.php foo@example.org

The script `rs-compliancy.php` is included that can be used to validate
WebFinger implementation of identity providers claiming to support
RemoteStorage:

    $ php rs-compliancy.php fkooman@5apps.com
    WebFinger Exception: invalid media type, expected 'application/jrd+json', got 'application/json; charset=utf-8'

Requesting a non existing user should throw an exception as well:

    $ php rs-compliancy.php foo@5apps.com
    WebFinger Exception: resource not found

