<?php

namespace Mic2100\Cart;

/**
 * Class Cart
 *
 * @package Mic2100\Cart
 * @author Michael Bardsley
 */
class Cart
{
    const DEFAULT_ITEM_CLASS = 'Mic2100\Cart\Item';

    /**
     * If this is set it will use this class when creating new items. This
     * allows you to implement the interface and create your own item classes.
     *
     * @var ItemInterface
     */
    private $customItemClass;

    /**
     * @var Item[]
     */
    private $items = array();

    /**
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item = null)
    {
        $item and $this->customItemClass = $item;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $id
     * @param null $hash
     * @return Item|null
     */
    public function getItem($id, $hash = null)
    {
        !$hash and $hash = $this->generateHash($id);

        return isset($this->items[$hash]) ? $this->items[$hash] : null;
    }

    /**
     * @param integer|string $id
     *
     * @throws \InvalidArgumentException
     */
    public function removeItem($id)
    {
        if (!preg_match('/^[a-z0-9]+$/i', $id)) {
            throw new \InvalidArgumentException('Id must be an integer or a string : ' . var_export($id, true));
        }

        if (isset($this->items[$this->generateHash($id)])) {
            unset($this->items[$this->generateHash($id)]);
        }
    }

    public function clear()
    {
        $this->items = array();
    }

    /**
     * Add a new item
     *
     * @param integer|string $id
     * @param string $name
     * @param integer|float $cost
     * @param integer|null $quantity
     * @param array|null $options
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function addItem($id, $name, $cost, $quantity, array $options = array())
    {
        $this->isValidId($id);
        $this->isValidName($name);
        $this->isValidCost($cost);
        $this->isValidQuantity($quantity);

        $hash = $this->generateHash($id);
        if (isset($this->items[$hash])) {
            $this->updateItem($id, $name, $cost, $quantity, $options, $hash);
        }

        $itemClass = $this->customItemClass ?: self::DEFAULT_ITEM_CLASS;

        if (!is_object($itemClass) && !class_exists($itemClass)) {
            throw new \LogicException('Class does not exist : ' . $itemClass);
        }
        $item = new $itemClass($id, $name, $cost, $quantity, $options);
        $item->setItemId($hash);

        $this->items[$hash] = $item;
    }

    /**
     * Update an item that already exists
     *
     * @param integer|string $id
     * @param string|null $name
     * @param integer|float|null $cost
     * @param integer|null $quantity
     * @param array|null $options
     * @param string|null $hash
     *
     * @throws \InvalidArgumentException
     */
    public function updateItem(
        $id,
        $name = null,
        $cost = null,
        $quantity = null,
        array $options = null,
        $hash = null
    ) {
        $this->isValidId($id);
        $name     and $this->isValidName($name);
        $cost     and $this->isValidCost($cost);
        $quantity and $this->isValidQuantity($quantity);
        !$hash    and $hash = $this->generateHash($id);

        if (!isset($this->items[$hash])) {
            throw new \InvalidArgumentException('Invalid Id when trying to update : ' . var_export($id, true));
        }

        $item = $this->items[$hash];

        $name     and $item->setName($name);
        $cost     and $item->setCost($cost);
        $quantity and $item->setQuantity($quantity);
        $options  and $item->setOptions($options);
    }

    /**
     * @return int
     */
    public function totalQuantity()
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    /**
     * @return int
     */
    public function totalItems()
    {
        return count($this->items);
    }

    /**
     * @return float|int
     */
    public function totalCost()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    /**
     * Generate the unique item Id hash
     *
     * @param integer|string $id
     * @return string
     */
    private function generateHash($id)
    {
        return md5($id);
    }

    /**
     * @param integer|string $id
     * @param string|null $name
     * @param integer|float|null $cost
     * @param integer|null $quantity
     */
    public function validateParameters($id, $name, $cost, $quantity)
    {
        if (!preg_match('/^[a-z0-9]+$/i', $id)) {
            throw new \InvalidArgumentException('Id must be an integer or a string : ' . var_export($id, true));
        } elseif (!is_string($name)) {
            throw new \InvalidArgumentException('Invalid name must be a string : ' . var_export($name, true));
        } elseif (!filter_var($cost, FILTER_VALIDATE_FLOAT)) {
            throw new \InvalidArgumentException('Invalid cost must be a numeric : ' . var_export($cost, true));
        } elseif (!filter_var($quantity, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
            throw new \InvalidArgumentException('Invalid quantity must be an integer : ' . var_export($quantity, true));
        }
    }

    private function isValidId($id)
    {
        if (!preg_match('/^[a-z0-9]+$/i', $id)) {
            throw new \InvalidArgumentException('Id must be an integer or a string : ' . var_export($id, true));
        }

        return true;
    }

    private function isValidName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Invalid name must be a string : ' . var_export($name, true));
        }

        return true;
    }

    private function isValidCost($cost)
    {
        if (!filter_var($cost, FILTER_VALIDATE_FLOAT)) {
            throw new \InvalidArgumentException('Invalid cost must be a numeric : ' . var_export($cost, true));
        }

        return true;
    }

    private function isValidQuantity($quantity)
    {
        if (!filter_var($quantity, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
            throw new \InvalidArgumentException('Invalid quantity must be an integer : ' . var_export($quantity, true));
        }

        return true;
    }
}
