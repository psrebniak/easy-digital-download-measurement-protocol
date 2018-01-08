<?php

require_once('ParametrizedObject.php');

/**
 *
 * @method MeasurementProtocol setProtocolVersion($value)
 * @method MeasurementProtocol setTrackingId($value)
 * @method MeasurementProtocol setClientId($value)
 * @method MeasurementProtocol setHitType($value)
 * @method MeasurementProtocol setDocumentLocation($value)
 * @method MeasurementProtocol setDocumentHost($value)
 * @method MeasurementProtocol setDocumentPath($value)
 * @method MeasurementProtocol setEventCategory($value)
 * @method MeasurementProtocol setEventAction($value)
 * @method MeasurementProtocol setEventLabel($value)
 * @method MeasurementProtocol setEventValue($value)
 * @method MeasurementProtocol setTransactionId($value)
 * @method MeasurementProtocol setTransactionTax($value)
 * @method MeasurementProtocol setTransactionRevenue($value)
 * @method MeasurementProtocol setCouponCode($value)
 * @method MeasurementProtocol setCheckoutStep($value)
 * @method MeasurementProtocol setProductAction($value)
 *
 * @method MeasurementProtocol setCacheBuster($value)
 */
class MeasurementProtocol extends ParametrizedObject
{
    protected $debug = false;

    protected $params = [
        'ProtocolVersion' => 'v',
        'TrackingId' => 'tid',
        'ClientId' => 'cid',
        'HitType' => 't',

        'DocumentLocation' => 'dl',
        'DocumentHost' => 'dh',
        'DocumentPath' => 'dp',

        'EventCategory' => 'ec',
        'EventAction' => 'ea',
        'EventLabel' => 'el',
        'EventValue' => 'ev',

        'TransactionId' => 'ti',
        'TransactionTax' => 'tt',
        'TransactionRevenue' => 'tr',
        'CouponCode' => 'tcc',
        'CheckoutStep' => 'cos',
        'ProductAction' => 'pa',

        'CacheBuster' => 'z'
    ];

    protected $productIndex = 1;

    public function setDebug($value)
    {
        $this->debug = (bool)$value;

        return $this;
    }

    /**
     * @param MeasurementProtocolProduct $product
     * @return $this
     */
    public function addProduct($product)
    {
        $data = $product->jsonSerialize();

        $productKey = "pr{$this->productIndex}";

        foreach ($data as $key => $value) {
            $this->data["{$productKey}{$key}"] = $value;
        }

        $this->productIndex++;
        return $this;
    }

    public function getUri()
    {
        return $this->debug
            ? "https://www.google-analytics.com/debug/collect"
            : "https://www.google-analytics.com/collect";
    }

    public function getUrl()
    {
        return $this->getUri() . "?" . http_build_query($this->jsonSerialize());
    }
}
