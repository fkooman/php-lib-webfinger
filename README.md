[![Build Status](https://travis-ci.org/fkooman/php-lib-webfinger.svg?branch=master)](https://travis-ci.org/fkooman/php-lib-webfinger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fkooman/php-lib-webfinger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fkooman/php-lib-webfinger/?branch=master)

# Introduction
This is a WebFinger (RFC 7033) client implementation written in PHP. It 
locates the WebFinger data based on a resource.

# Installation
You need [Composer](https://getcomposer.org) to install the dependencies to
run the included `finger.php` script. See below how to use it in your 
project.

    $ /path/to/composer.phar install

# Usage
The library `fkooman\WebFinger\WebFinger` can be used in your project.

You can also use `Composer` and make this library a dependency by putting
the following in your `composer.json` in the `require` section:

	"fkooman/webfinger": "^1.0.0"

Or if you want to use the development version:

	"fkooman/webfinger": "dev-master"

# API

    <?php
    require_once 'vendor/autoload.php';

    use fkooman\WebFinger\WebFinger;
    use fkooman\WebFinger\Exception\WebFingerException;

    try { 
        $w = new WebFinger();
        echo $w->finger("fkooman@5apps.com");
    } catch (WebFingerException $e) {
        echo $e->getMessage() . PHP_EOL;
    } 

The `WebFingerException` is thrown if an error occurs, like a specification
violation. There are a number of options that can be set to avoid some (fatal) 
errors:

    $w = new WebFinger();

    // disable TLS verification (default: true)
    $w->setOption('verify', false);

    // disable Content-Type check (default: false)
    $w->setOption('ignore_media_type', true);

    // disable check of the type of property value, introduced for the 
    // remoteStorage WebFinger specification issues
    // MUST be null or string according to specification (default: false)
    $w->setOption('ignore_property_value_type', true);

**NOTE**: DO NOT use any of these options in production environments!

# Simple Compliancy Testing
A script is included to check a WebFinger server implementation:

	$ php finger.php foo@example.org

# License
Licensed under the Apache License, Version 2.0;

   http://www.apache.org/licenses/LICENSE-2.0
