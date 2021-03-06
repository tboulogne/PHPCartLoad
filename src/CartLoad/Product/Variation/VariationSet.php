<?php namespace CartLoad\Product\Variation;

use CartLoad\Cart\Item;
use CartLoad\Product\Feature\PriceInterface;
use CartLoad\Product\Feature\SkuInterface;
use CartLoad\Product\Feature\SkuTrait;

class VariationSet implements SkuInterface
{
    use SkuTrait;

    protected $id;
    protected $name;
    protected $required;
    protected $order;
    /**
     * @var Variation[]
     */
    protected $items;

    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->fromArray($data);
        } else {
            $this->setSkuDelimiter('-');
            $this->setSkuEffect(SkuInterface::SKU_END_OF);
        }
    }

    /**
     * @param $value
     * @return $this
     */
    public function fromArray($value)
    {
        if (isset($value['id'])) {
            $this->setId($value['id']);
        }
        if (isset($value['name'])) {
            $this->setName($value['name']);
        }
        if (isset($value['required'])) {
            $this->setRequired($value['required']);
        }
        if (isset($value['order'])) {
            $this->setOrder($value['order']);
        }
        if (isset($value['items'])) {
            $items = array_map(function ($item) {
                return new Variation($item);
            }, $value['items']);
            $this->setItems($items);
        }
        $this->skuFromArray($value);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Variation
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Variation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param mixed $required
     * @return Variation
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     * @return Variation
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @param $qty
     * @param \DateTime|NULL $now
     * @return array
     */
    public function calculatePrice($qty, \DateTime $now = null)
    {
        if ($now === null) {
            $now = new \DateTime();
        }

        if (is_object($qty) && $qty instanceof Item) {
            $cart_item = $qty;
            $qty = $cart_item->getQty();
            $variation_ids = $cart_item->getVariations();

            $prices = [
                'replaces' => [],
                'combines' => [],
            ];

            foreach ($this->getItems() as $item) {
                foreach ($variation_ids as $variation_id) {
                    if ($item->getId() == $variation_id) {
                        if ($item->getPriceEffect() === PriceInterface::PRICE_COMBINE) {
                            $prices['combines'] [] = $item->getPrice();
                        } else {
                            if ($item->getPriceEffect() === PriceInterface::PRICE_REPLACE_ALL) {
                                $prices['replaces'] [] = $item->getPrice();
                            }
                        }
                    }
                }
            }

            return $prices;
        }

        return [];
    }

    /**
     * @return Variation[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Variation[] $items
     * @return VariationSet
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    public function calculateSkus($qty, \DateTime $now = null)
    {
        if ($now === null) {
            $now = new \DateTime();
        }

        if (is_object($qty) && $qty instanceof Item) {
            $cart_item = $qty;
            $qty = $cart_item->getQty();
            $variation_ids = $cart_item->getVariations();

            $skus = [
                'replaces' => [],
                'starts' => [],
                'ends' => [],
            ];

            foreach ($this->getItems() as $item) {
                foreach ($variation_ids as $variation_id) {
                    if ($item->getId() == $variation_id) {
                        $sku_effect = $item->getSkuEffect() !== null ? $item->getSkuEffect() : $this->getSkuEffect();
                        $sku_delimiter = $item->getSkuDelimiter() !== null ? $item->getSkuDelimiter() : $this->getSkuDelimiter();

                        if ($sku_effect === SkuInterface::SKU_REPLACE_ALL) {
                            $skus['replaces'] [] = [$item->getSku(), $sku_delimiter];
                        } else {
                            if ($sku_effect === SkuInterface::SKU_START_OF) {
                                $skus['starts'] [] = [$item->getSku(), $sku_delimiter];
                            } else {
                                if ($sku_effect === SkuInterface::SKU_END_OF) {
                                    $skus['ends'] [] = [$item->getSku(), $sku_delimiter];
                                }
                            }
                        }
                    }
                }
            }

            return $skus;
        }

        return [];
    }

    /**
     * @param $getVariations
     * @return bool
     */
    public function hasVariationIds($getVariations)
    {
        foreach ($this->items as $item) {
            if (in_array($item->getId(), $getVariations)) {
                return true;
            }
        }

        return false;
    }
}