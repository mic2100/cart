<?php

namespace Mic2100\Cart;

interface ItemInterface
{
    public function getItemId();
    public function setItemId($itemId);
    public function getId();
    public function setId($id);
    public function getName();
    public function setName($name);
    public function getCost();
    public function setCost($cost);
    public function getQuantity();
    public function setQuantity($quantity);
    public function getOptions();
    public function setOptions(array $options);
    public function getTotal();
}
