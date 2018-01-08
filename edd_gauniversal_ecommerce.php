<?php

/**
 * Plugin Name: EDD Measurement Protocol
 * Plugin URI: https://github.com/psrebniak/easy-digital-download-measurement-protocol
 * Description: Measurement Protocol support for Easy Digital Download. Handle enhanced e-commerce cart, create transaction and confirm order (create confirmed transaction).
 * Version: 1.0
 * Author: Piotr Srebniak
 * Author URI: https://github.com/psrebniak
 * Text Domain: edd_measurement_protocol
 * License: MIT
 *
 */

//define('EDD_MEASUREMENT_PROTOCOL_DEBUG', 1);

require_once('includes/EasyDigitalDownloadMeasurementProtocolPlugin.php');

if (!function_exists('edd_measurement_protocol_fetch')) {

    /**
     * @param MeasurementProtocol $measurement
     */
    function edd_measurement_protocol_fetch($measurement)
    {
        $content = file_get_contents($measurement->getUrl());
        if (defined('EDD_MEASUREMENT_PROTOCOL_DEBUG')) {
            echo '<code><pre>';
            print_r($content);
            echo '</pre></code>';
        }
    }
}

EasyDigitalDownloadMeasurementProtocolPlugin::getInstance()
    ->setHttpClient('edd_measurement_protocol_fetch');
