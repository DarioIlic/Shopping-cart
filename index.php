<?php

class Input
{
    public function getCommand()
    {
        $f = fopen("php://stdin", "r");
        $input = fgets($f, 4096);
        $input = rtrim($input);
        fclose($f);
        $command = '';
        sscanf($input, "%s", $command);
        return $command;
    }

    public function cartInput()
    {
        $f = fopen("php://stdin", "r");
        $input = fgets($f, 4096);
        $input = rtrim($input);
        fclose($f);
        $sku = 0;
        $qty = 0;
        sscanf($input, "%d %d", $sku, $qty);
        return array(
            "sku" => $sku,
            "qty" => $qty
        );
    }

    public function productInput()
    {
        $f = fopen("php://stdin", "r");
        $input = fgets($f, 4096);
        $input = rtrim($input);
        fclose($f);
        $sku = 0;
        $name = '';
        $qty = 1;
        $price = 0;
        sscanf($input, "%d %s %f", $sku, $name, $price);
        return array(
            "sku" => $sku,
            "name" => $name,
            "qty" => $qty,
            "price" => $price
        );
    }
}


class Item
{
    public $item = [];

    public function setInvItem()
    {
        $Item = new Input();
        $item = $Item->productInput();
        $this->item = $item;
        return $this->item;
    }
}


class Inventory
{
    public $inventory = [];

    protected $trash = [];

    public function addItemToInventory(Item $item)
    {
        $item = $item->setInvItem();
        $items = $this->inventory;
        $items[] = $item;
        $this->inventory = $items;
        return $this->inventory;
    }

    public function countInventory($sku)
    {
        $count = 0;
        $inventory = $this->inventory;
        foreach ($inventory as $position => $invItem) {
            if ($invItem['sku'] === $sku) {
                $count++;
            }
        }
        return $count;
    }

    public function removeItemFromInventory(Inventory $inputInventory){
        $New = new Input();
        $new = $New->cartInput();
        $sku = $new['sku'];
        $inventoryCount = $inputInventory->countInventory($sku);
        $inventory = $inputInventory->inventory;
        $trash = $inputInventory->trash;
        $movedItems = 0;
        if ($inventoryCount < $new['qty']){
            echo "Not enough items in the inventory" . PHP_EOL;
        }
        else {
            foreach ($inventory as $position => $invItem){
                if ($movedItems < $new['qty'] && $invItem['sku'] === $new['sku']){
                    unset($inventory[$position]);
                    $trash[] = $invItem;
                    $movedItems++;
                }
                else {
                    ($invItem['sku'] !== $new['sku']);
                    echo "Item not found in the inventory" . PHP_EOL;
                }
            }
        }
        $inputInventory->inventory = $inventory;
        $inputInventory->trash = $trash;
        return $inputInventory->inventory;
    }
}


class Cart
{
    public $cart = [];

    public function addItemToCart(Inventory $inputInventory)
    {
        $New = new Input();
        $new = $New->cartInput();
        $sku = $new['sku'];
        $count = $inputInventory->countInventory($sku);
        $inventory = $inputInventory->inventory;
        $cart = $this->cart;
        $movedItems = 0;
        if ($count < $new['qty']) {
            echo "Not enough items in the inventory" . PHP_EOL;
        }
        else {
            foreach ($inventory as $position => $invItem) {
                if ($movedItems < $new['qty'] && $invItem['sku'] == $new['sku']) {
                    unset($inputInventory->inventory[$position]);
                    $cart[] = $invItem;
                    $movedItems++;
                }
                else {
                    ($invItem['sku'] !== $new['sku']);
                    echo "Item not found in the inventory" . PHP_EOL;
                }
            }
        }
        $this->cart = $cart;
        return $this->cart;
    }

    public function countCart($sku)
    {
        $cartCount = 0;
        $cart = $this->cart;
        foreach ($cart as $index => $cartItem) {
            if ($cartItem['sku'] === $sku) {
                $cartCount++;
            }
        }
        return $cartCount;
    }

    public function removeItemFromCart(Cart $inputCart, $inputInventory)
    {
        $New = new Input();
        $new = $New->cartInput();
        $sku = $new['sku'];
        $cartCount = $inputCart->countCart($sku);
        $cart = $inputCart->cart;
        $inventory = $inputInventory->inventory;
        $movedItems = 0;
        if ($cartCount < $new['qty']) {
            echo "Not enough items in the cart" . PHP_EOL;
        }
        else {
            foreach ($cart as $index => $cartItem) {
                if ($movedItems < $new['qty'] && $cartItem['sku'] === $new['sku']) {
                    unset($cart[$index]);
                    $inventory[] = $cartItem;
                    $movedItems++;
                }
                else {
                    ($cartItem['sku'] !== $new['sku']);
                    echo "Item not found in the cart" . PHP_EOL;
                }
            }
        }
        $inputCart->cart = $cart;
        $inputInventory->inventory = $inventory;
        return $inputCart->cart;
    }
}


class Checkout
{
    public $total = 0;

    public function setTotal(Cart $inputCart)
    {
        $total = $this->total;
        $cart = $inputCart->cart;
        if (empty($cart)) {
            echo "Nothing to sum" . PHP_EOL;
        } else {
            foreach ($cart as $index => $cartItem) {
                $sum = $cartItem['qty'] * $cartItem['price'];
                echo $cartItem['name'] . ' ' . $cartItem['qty'] . ' x ' . $cartItem['price'] . ' = ' . $sum . PHP_EOL;
                $total += $sum;
            }
            echo "Total is = " . $total . PHP_EOL;
        }
        $total = 0;
        $inputCart->cart = [];
        return $total;
    }
}


$Inventory = new Inventory();

$item = new Item();

$Cart = new Cart();
$cart = $Cart->cart;

$Checkout = new Checkout();

do {
    $input = new Input();
    $Command = $input->getCommand();
    switch ($Command) {
        case 'add';
            $inventory = $Inventory->addItemToInventory($item);
            print_r($inventory);
            break;
        case 'remove';
            $inventory = $Inventory->removeItemFromInventory($Inventory);
            print_r($inventory);
            break;
        case 'end';
            break;
        default:
            echo "Unknown command" . PHP_EOL;
    }
} while ($Command !== 'end');


do {
    $input = new Input();
    $Command = $input->getCommand();
    switch ($Command) {
        case 'add';
            $cart = $Cart->addItemToCart($Inventory);
            print_r($cart);
            break;
        case 'remove';
            $cart = $Cart->removeItemFromCart($Cart, $Inventory);
            print_r($cart);
            break;
        case 'checkout';
            $checkout = $Checkout->setTotal($Cart);
            break;
        case 'end';
            break;
        default:
            echo "Unknown command" . PHP_EOL;
    }
} while ($Command !== 'end');


// Code for review
