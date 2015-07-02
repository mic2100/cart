<?php

namespace Mic2100\Cart;

class Item implements ItemInterface
{
    /**
     * An has that represents this item
     *
     * @var string
     */
    private $itemId;

    /**
     * @var string|int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $cost = 0;

    /**
     * @var int
     */
    private $quantity = 0;

    /**
     * @var float
     */
    private $total = 0;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @param int|string $id
     * @param string $name
     * @param $cost
     * @param $quantity
     * @param $options
     */
    public function __construct($id, $name, $cost, $quantity, $options)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setCost($cost);
        $this->setQuantity($quantity);
        $this->setOptions($options);
    }

    /**
     * @return string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param string $itemId
     *
     * @throws \InvalidArgumentException
     */

    public function setItemId($itemId)
    {
        if (!is_string($itemId)) {
            throw new \InvalidArgumentException('Item Id must be a string value : ' . var_export($itemId, true));
        }

        $this->itemId = $itemId;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id string|int
     *
     * @throws \InvalidArgumentException
     */
    public function setId($id)
    {
        if (!preg_match('/^[a-z0-9]+$/i', $id)) {
            throw new \InvalidArgumentException('Id must be an integer or a string value : ' . var_export($id, true));
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param float|int $cost
     *
     * @throws \InvalidArgumentException
     */
    public function setCost($cost)
    {
        if (!filter_var($cost, FILTER_VALIDATE_FLOAT)) {
            throw new \InvalidArgumentException('Cost must be a numeric value : ' . var_export($cost, true));
        }

        $this->cost = (float) $cost;
        $this->calculateTotal();
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     *
     * @throws \InvalidArgumentException
     */
    public function setQuantity($quantity)
    {
        if (!filter_var($quantity, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
            throw new \InvalidArgumentException('Quantity must be an integer value : ' . var_export($quantity, true));
        }

        $this->quantity = (int) $quantity;
        $this->calculateTotal();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Calculate the total based on the vars being used
     */
    private function calculateTotal()
    {
        $this->total = bcmul($this->quantity, $this->cost, 2);
    }
}
