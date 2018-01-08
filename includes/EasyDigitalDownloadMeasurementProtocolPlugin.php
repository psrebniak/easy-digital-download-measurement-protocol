<?php

require_once('MeasurementProtocol.php');
require_once('MeasurementProtocolProduct.php');

class EasyDigitalDownloadMeasurementProtocolPlugin
{
    const SESSION_KEY = 'EDD_MP_SK';
    static $instance = null;

    private $trackingId = '';
    private $debug = false;
    private $client;

    /**
     * @return EasyDigitalDownloadMeasurementProtocolPlugin|null
     */
    public static function getInstance()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }
        return self::$instance = new EasyDigitalDownloadMeasurementProtocolPlugin();
    }

    /**
     * @param string|array $callable
     */
    public function setHttpClient($callable)
    {
        $this->client = $callable;
    }


    protected function __construct()
    {
        $this->debug = defined('EDD_MEASUREMENT_PROTOCOL_DEBUG');
        $this->trackingId = edd_get_option('edd_measurement_protocol_tracking_id', '');
        $this->registerActions();
        $this->registerHooks();
    }

    protected function getClientIdFromCookie()
    {
        if (!isset($_COOKIE['_ga'])) {
            return null;
        }

        $clientId = substr($_COOKIE['_ga'], 6);
        if (is_string($clientId)) {
            return $clientId;
        }

        return null;
    }

    protected function getClientId()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $clientId = $this->getClientIdFromCookie();
        if (isset($clientId)) {
            if (!isset($_SESSION[self::SESSION_KEY]) || $_SESSION[self::SESSION_KEY] !== $clientId) {
                $_SESSION[self::SESSION_KEY] = filter_var($clientId);
            }
            return $clientId;
        }

        if (isset($_SESSION[self::SESSION_KEY])) {
            return $clientId;
        }

        return $_SESSION[self::SESSION_KEY] = sha1((new \DateTime())->format(DATE_W3C));
    }

    protected function registerActions()
    {
        add_action('edd_purchase_form_before_submit', array(&$this, 'actionShowCart'));
        add_action('edd_insert_payment', array(&$this, 'actionCreatePayment'));
        add_action('edd_complete_purchase', array(&$this, 'actionCompletePurchase'));
    }

    protected function registerHooks()
    {
        add_filter('edd_settings_extensions', array($this, 'hookSettingsExtension'));
    }

    public function hookSettingsExtension($settings)
    {
        $pluginSettings = array(
            array(
                'id' => 'edd_measurement_protocol_header',
                'name' => '<strong>' . __('Measurement Protocol', 'edd_measurement_protocol') . '</strong>',
                'type' => 'header',
            ),
            array(
                'id' => 'edd_measurement_protocol_tracking_id',
                'name' => __('Tracking ID', 'edd_measurement_protocol'),
                'desc' => __('Google Analytics tracking ID', 'edd_measurement_protocol'),
                'type' => 'text',
            )
        );

        return array_merge($settings, $pluginSettings);
    }

    public function actionShowCart()
    {
        $measurement = new MeasurementProtocol();

        $measurement
            ->setProtocolVersion(1)
            ->setDebug($this->debug)
            ->setDocumentHost(filter_input(INPUT_SERVER, 'HTTP_HOST'))
            ->setDocumentPath(filter_input(INPUT_SERVER, 'REQUEST_URI'))
            ->setTrackingId($this->trackingId)
            ->setClientId($this->getClientId())
            ->setHitType('event')
            ->setEventCategory('MeasurementProtocol')
            ->setEventAction('Checkout')
            ->setEventLabel('Step1')
            ->setProductAction('checkout')
            ->setCheckoutStep(1);

        foreach (edd_get_cart_content_details() as $item) {

            $product = new MeasurementProtocolProduct();
            $product
                ->setName($item['name'])
                ->setSku(edd_use_skus() ? edd_get_download_sku($item['id']) : $item['id'])
                ->setQuantity($item['quantity'])
                ->setPrice($item['item_price']);

            $measurement->addProduct($product);
        }

        $measurement->setCacheBuster(sha1((new \DateTime())->format(DATE_W3C)));
        call_user_func($this->client, $measurement);
    }

    public function actionCreatePayment($paymentId)
    {
        $measurement = new MeasurementProtocol();

        $measurement
            ->setProtocolVersion(1)
            ->setDebug($this->debug)
            ->setDocumentHost(filter_input(INPUT_SERVER, 'HTTP_HOST'))
            ->setDocumentPath(filter_input(INPUT_SERVER, 'REQUEST_URI'))
            ->setTrackingId($this->trackingId)
            ->setClientId($this->getClientId())
            ->setHitType('event')
            ->setEventCategory('MeasurementProtocol')
            ->setEventAction('Checkout')
            ->setEventLabel('Step2')
            ->setProductAction('checkout')
            ->setCheckoutStep(2);

        foreach (edd_get_payment_meta_cart_details($paymentId, true) as $item) {
            $product = new MeasurementProtocolProduct();
            $product
                ->setName($item['name'])
                ->setSku(edd_use_skus() ? edd_get_download_sku($item['id']) : $item['id'])
                ->setQuantity($item['quantity'])
                ->setPrice($item['item_price']);

            $measurement->addProduct($product);
        }

        $measurement->setCacheBuster(sha1((new \DateTime())->format(DATE_W3C)));
        call_user_func($this->client, $measurement);
    }

    public function actionCompletePurchase($paymentId)
    {
        $measurement = new MeasurementProtocol();
        $meta = edd_get_payment_meta($paymentId);

        $measurement
            ->setProtocolVersion(1)
            ->setDebug($this->debug)
            ->setDocumentHost(filter_input(INPUT_SERVER, 'HTTP_HOST'))
            ->setDocumentPath(filter_input(INPUT_SERVER, 'REQUEST_URI'))
            ->setTrackingId($this->trackingId)
            ->setClientId($this->getClientId())
            ->setHitType('event')
            ->setEventCategory('MeasurementProtocol')
            ->setEventAction('Purchase')
            ->setProductAction('purchase')
            ->setTransactionRevenue(edd_get_cart_total())
            ->setTransactionId($meta['key']);

        foreach (edd_get_payment_meta_cart_details($paymentId, true) as $item) {
            $product = new MeasurementProtocolProduct();
            $product
                ->setName($item['name'])
                ->setSku(edd_use_skus() ? edd_get_download_sku($item['id']) : $item['id'])
                ->setQuantity($item['quantity'])
                ->setPrice($item['item_price']);

            $measurement->addProduct($product);
        }

        if (isset($meta['user_info']['discount'])) {
            $measurement->setCouponCode($meta['user_info']['discount']);
        }

        $measurement->setCacheBuster(sha1((new \DateTime())->format(DATE_W3C)));
        call_user_func($this->client, $measurement);
    }
}