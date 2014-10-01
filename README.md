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
                // enable SSL verification (default: true)
                "verify" => true,
                // disable Content-Type check (default: false)
                "ignore_media_type" => false
            )
        );
        echo $w->finger("fkooman@5apps.com");
    } catch (WebFingerException $e) {
        echo $e->getMessage() . PHP_EOL;
    } 

# Compliancy Testing
A script is included to check a WebFinger server implementation:

	$ php finger.php foo@example.org

