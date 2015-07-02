<?php

namespace Mic2100\CartTest;

use Mic2100\Cart\Cart;
use Mic2100\Cart\Item;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Item
     */
    private $item;

    public function setUp()
    {
        $this->cart = new Cart($this->item);
    }

    /**
     * @dataProvider dataInvalidItemArguments
     * @expectedException \InvalidArgumentException
     */
    public function testAddItemThrowsInvalidArgumentsException($id, $name, $cost, $quantity)
    {
        $this->cart->addItem($id, $name, $cost, $quantity);
    }

    /**
     * @dataProvider dataInvalidItemArguments
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateItemThrowsInvalidArgumentsException($id, $name, $cost, $quantity)
    {
        $this->cart->updateItem($id, $name, $cost, $quantity);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateItemThrowsInvalidArgumentsExceptionWithInvalidHash()
    {
        $hash = 'wehnuiefh322i239092q1rejhq893trhs';
        $this->cart->updateItem(123, 'valid name', 2.34, 1, array(), $hash);
    }

    /**
     * @dataProvider dataInvalidIds
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateItemThrowsInvalidArgumentExceptionWithInvalidId($id)
    {
        $this->cart->updateItem($id);
    }

    /**
     * @dataProvider dataInvalidIds
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveItemThrowsInvalidArgumentExceptionWithInvalidId($id)
    {
        $this->cart->removeItem($id);
    }

    public function testAddItem()
    {
        $data = $this->dataItemToAdd();
        $hashes = array();

        foreach ($data as $item) {
            list($id, $name, $cost, $quantity) = $item;

            $this->cart->addItem($id, $name, $cost, $quantity);
            $hashes[md5($id)] = $item;
        }

        $items = $this->cart->getItems();

        $this->assertSame(count($data), count($items));

        foreach ($hashes as $hash => $itemData) {
            $this->assertArrayHasKey($hash, $items);

            $returnedItem = $items[$hash];
            $this->assertInstanceOf('Mic2100\Cart\Item', $returnedItem);

            $this->assertSame($itemData[0], $returnedItem->getId());
            $this->assertSame($itemData[1], $returnedItem->getName());
            $this->assertSame((float) $itemData[2], $returnedItem->getCost());
            $this->assertSame($itemData[3], $returnedItem->getQuantity());

            $total = bcmul($itemData[2], $itemData[3], 2);
            $this->assertSame($total, $returnedItem->getTotal());
        }
    }

    public function testClear()
    {
        $this->assertSame(0, $this->cart->totalItems());

        foreach ($this->dataItemToAdd() as $item) {
            list($id, $name, $cost, $quantity) = $item;
            $this->cart->addItem($id, $name, $cost, $quantity);
        }
        $this->cart->clear();

        $this->assertSame(0, $this->cart->totalItems());
    }

    public function testTotalQuantity()
    {
        $totalQuantity = 0;
        $this->assertSame($totalQuantity , $this->cart->totalQuantity());

        foreach ($this->dataItemToAdd() as $item) {
            list($id, $name, $cost, $quantity) = $item;
            $this->cart->addItem($id, $name, $cost, $quantity);
            $totalQuantity += $quantity;
        }

        $this->assertSame($totalQuantity, $this->cart->totalQuantity());
    }

    public function testTotalItems()
    {
        $numberOfItems = 0;
        $this->assertSame($numberOfItems, $this->cart->totalItems());

        foreach ($this->dataItemToAdd() as $item) {
            list($id, $name, $cost, $quantity) = $item;
            $this->cart->addItem($id, $name, $cost, $quantity);
            $numberOfItems++;
        }

        $this->assertSame($numberOfItems, $this->cart->totalItems());
    }

    public function testTotalCost()
    {
        $totalCost = 0;
        $this->assertSame($totalCost, $this->cart->totalCost());

        foreach ($this->dataItemToAdd() as $item) {
            list($id, $name, $cost, $quantity) = $item;
            $this->cart->addItem($id, $name, $cost, $quantity);
            $totalCost += bcmul($cost, $quantity, 2);
        }

        $this->assertSame($totalCost, $this->cart->totalCost());
    }

    public function dataInvalidIds()
    {
        return array(
            array('!"£'),
            array(3.456),
            array('invalid id'),
            array('invalid-id'),
        );
    }

    /**
     * @return array
     */
    private function dataItemToAdd()
    {
        return array(
            array('abc123', 'item 1', 5, 1),
            array(433, 'item 2', 2.51, 1),
            array('123abc', 'item 3', 3, 1),
            array(435, 'item 4', 6.1, 1),
            array('a1b2c3', 'item 5', 2, 1),
            array(437, 'item 6', 4.65, 1),
            array('1a2b3c', 'item 7', 1, 1),
            array(439, 'item 8', 5.2, 1),
            array(440, 'item 9', 3.45, 1),
            array(441, 'item 0', 2, 1),
        );
    }

    public function dataInvalidItemArguments()
    {
        return array(
            array('£$%', 'valid name', 10, 5),
            array(123, 0, 10, 5),
            array('abc123', 'valid name', '^&*', 5),
            array(432, 'valid name', 10, 5.5),
            array(432, 'valid name', 10, 'abc'),
        );
    }

    /**
     * @return \Mic2100\Cart\Item
     */
    private function mockItem()
    {
        $this->item = $this->getMockBuilder('Mic2100\Cart\Item')
                           ->disableOriginalConstructor()
                           ->setMethods(array(
                               'getItemId',
                               'setItemId',
                               'getId',
                               'setId',
                               'getName',
                               'setName',
                               'getCost',
                               'setCost',
                               'getQuantity',
                               'setQuantity',
                               'getOptions',
                               'setOptions',
                               'getTotal',
                           ))
                           ->getMock();
    }
}
