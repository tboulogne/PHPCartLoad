<?php namespace CartLoad\Product\Feature;


interface PriceInterface
{
    const PRICE_REPLACE_ALL = 0;
    const PRICE_COMBINE = 1;

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $price
     * @return self
     */
    public function setPrice($price);
}