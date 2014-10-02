[![Build Status](https://travis-ci.org/fkooman/php-lib-webfinger.svg?branch=master)](https://travis-ci.org/fkooman/php-lib-webfinger)

# Introduction
This is a WebFinger (RFC 7033) client implementation written in PHP. It 
locates the WebFinger data based on a resource.

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
        $w = new WebFinger()
        echo $w->finger("fkooman@5apps.com");
    } catch (WebFingerException $e) {
        echo $e->getMessage() . PHP_EOL;
    } 

The `WebFingerException` is thrown if an error occurs, like a specification
violation. There are a number of options that can be provided to the 
`WebFinger` contructor to avoid some (fatal) errors:

    $w = new WebFinger(
        array(
            // enable SSL verification (default: true)
            "verify" => true,
            // disable Content-Type check (default: false)
            "ignore_media_type" => false
        )
    );

**Please DO NOT use any of these options in production environments!**

# Compliancy Testing
A script is included to check a WebFinger server implementation:

	$ php finger.php foo@example.org

