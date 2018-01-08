# Easy Digital Download - Google Measurement Protocol tracking library

This plugin allows you to add Measurement Protocol tracking (server side version of Google Analytics) to your Easy Digital Download.
Server side events cannot be blocked with AdBlock or other scripts and you have reliable data with almost 100% accuracy. 

You need to enable Enhanced e-Commerce to use this plugin.

## Features 

* Tracking all sessions, even session with blocked google analytics (randomly generated clientId will be taken if \_ga cookie is not set).
* Three steps of tracking - cart, order and confirmed order
* Support for enhanced e-commerce
* Support for vouchers

## Configuration

* Install plugin
* Go to Easy Digital Download -> Settings -> Extension and fill "Tracking ID" field. [More info](https://support.google.com/analytics/answer/7372977?hl=en])
* Test by adding items to your cart and watching analytics real-time event dashboard - you should see "MeasurementProtocol" event category.

## Advanced 

if you have problems with sending events, you can debug this plugin by defining `define('EDD_MEASUREMENT_PROTOCOL_DEBUG', 1);` in your `wp-config.php`.
Moreover you can define own way to make a request by defining own `edd_measurement_protocol_fetch` function. 

By default `file_get_contents` function is used.

## Contribution

You're welcomed.
