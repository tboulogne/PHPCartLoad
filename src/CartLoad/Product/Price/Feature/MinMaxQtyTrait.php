<?php namespace CartLoad\Product\Price\Feature;


trait MinMaxQtyTrait {
	/** @var int $min_qty */
	protected $min_qty;

	/** @var int $max_qty */
	protected $max_qty;

	/** @var boolean $no_minimum_qty_limit */
	protected $no_minimum_qty_limit;

	/** @var boolean $no_maximum_qty_limit */
	protected $no_maximum_qty_limit;

	/**
	 * @return int
	 */
	public function getMinQty() {
		return $this->min_qty;
	}

	/**
	 * @param int $min_qty
	 * @return Bulk
	 */
	public function setMinQty(int $min_qty) {
		$this->min_qty = $min_qty;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxQty() {
		return $this->max_qty;
	}

	/**
	 * @param int $max_qty
	 * @return Bulk
	 */
	public function setMaxQty(int $max_qty) {
		$this->max_qty = $max_qty;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isNoMinimumQtyLimit() {
		return $this->no_minimum_qty_limit;
	}

	/**
	 * @param boolean $no_minimum_qty_limit
	 * @return MinMaxQtyTrait
	 */
	public function setNoMinimumQtyLimit(bool $no_minimum_qty_limit) {
		$this->no_minimum_qty_limit = $no_minimum_qty_limit;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isNoMaximumQtyLimit() {
		return $this->no_maximum_qty_limit;
	}

	/**
	 * @param boolean $no_maximum_qty_limit
	 * @return MinMaxQtyTrait
	 */
	public function setNoMaximumQtyLimit(bool $no_maximum_qty_limit) {
		$this->no_maximum_qty_limit = $no_maximum_qty_limit;

		return $this;
	}

    /**
     * @param int $qty
     * @return bool
     */
    public function inMinMaxQtyRange(int $qty) {
        $no_min_limit = $this->isNoMinimumQtyLimit();
        $no_max_limit = $this->isNoMaximumQtyLimit();
        $min_qty = $this->getMinQty();
        $max_qty = $this->getMaxQty();

        if ($no_min_limit && $no_max_limit) {
            return TRUE;
        }
        else if ($no_min_limit && !$no_max_limit && $qty <= $max_qty) {
            return TRUE;
        }
        else if (!$no_min_limit && $no_max_limit && $qty >= $min_qty) {
            return TRUE;
        }
        else if (!$no_min_limit && !$no_max_limit && $qty >= $min_qty && $qty <= $max_qty) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}