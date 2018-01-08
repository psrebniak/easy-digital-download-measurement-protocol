<?php

require_once('ParametrizedObject.php');


/**
 * Class MeasurementProtocolProduct
 * @method MeasurementProtocolProduct setSku($value)
 * @method MeasurementProtocolProduct setName($value)
 * @method MeasurementProtocolProduct setBrand($value)
 * @method MeasurementProtocolProduct setCategory($value)
 * @method MeasurementProtocolProduct setVariant($value)
 * @method MeasurementProtocolProduct setPrice($value)
 * @method MeasurementProtocolProduct setQuantity($value)
 * @method MeasurementProtocolProduct setCouponCode($value)
 *
 */
class MeasurementProtocolProduct extends ParametrizedObject
{
    protected $params = [
        'Sku' => 'id',
        'Name' => 'nm',
        'Brand' => 'br',
        'Category' => 'ca',
        'Variant' => 'va',
        'Price' => 'pr',
        'Quantity' => 'qt',
        'CouponCode' => 'cc',
    ];
}