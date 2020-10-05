<?php

//Принцип единственной ответственности (Single responsibility)
class Order
{
    public function calculateTotalSum(){/*...*/}
    public function getItems(){/*...*/}
    public function getItemCount(){/*...*/}
    public function addItem($item){/*...*/}
    public function deleteItem($item){/*...*/}
    
    public function printOrder(){/*...*/}
    public function showOrder(){/*...*/}
    
    public function load(){/*...*/}
    public function save(){/*...*/}
    public function update(){/*...*/}
    public function delete(){/*...*/}
}

class Order1
{
    public function calculateTotalSum(){/*...*/}
    public function getItems(){/*...*/}
    public function getItemCount(){/*...*/}
    public function addItem($item){/*...*/}
    public function deleteItem($item){/*...*/}
}

class OrderRepository
{
    public function load($orderID){/*...*/}
    public function save($order){/*...*/}
    public function update($order){/*...*/}
    public function delete($order){/*...*/}
}

class OrderViewer
{
    public function printOrder($order){/*...*/}
    public function showOrder($order){/*...*/}
}


//Принцип открытости/закрытости (Open-closed)


class OrderRepository1
{
    public function load($orderID)
    {
        $pdo = new PDO($this->config->getDsn(), $this->config->getDBUser(), $this->config->getDBPassword());
        $statement = $pdo->prepare('SELECT * FROM `orders` WHERE id=:id');
        $statement->execute(array(':id' => $orderID));
        return $query->fetchObject('Order');
    }
    public function save($order){/*...*/}
    public function update($order){/*...*/}
    public function delete($order){/*...*/}
}

class OrderRepository2
{
    private $source;
    
    public function setSource(IOrderSource $source)
    {
        $this->source = $source;
    }
    
    public function load($orderID)
    {
        return $this->source->load($orderID);
    }
    public function save($order){/*...*/}
    public function update($order){/*...*/}
}

interface IOrderSource
{
    public function load($orderID);
    public function save($order);
    public function update($order);
    public function delete($order);
}

class MySQLOrderSource implements IOrderSource
{
    public function load($orderID){}
    public function save($order){/*...*/}
    public function update($order){/*...*/}
    public function delete($order){/*...*/}
}

class ApiOrderSource implements IOrderSource
{
    public function load($orderID){}
    public function save($order){/*...*/}
    public function update($order){/*...*/}
    public function delete($order){/*...*/}
}

//Принцип подстановки Барбары Лисков (Liskov substitution)

class Rectangle
{
    private $width;
    private $height;
    
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    public function getWidth()
    {
        return $this->width;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
}

class Square extends Rectangle
{
    public function setWidth($width)
    {
        parent::setWidth($width);
        parent::setHeight($width);
    }
    
    public function setHeight($height)
    {
        parent::setHeight($height);
        parent::setWidth($height);
    }
}

function calculateRectangleSquare(Rectangle $rectangle, $width, $height)
{
    $rectangle->setWidth($width);
    $rectangle->setHeight($height);
    return $rectangle->getHeight * $rectangle->getWidth;
}

calculateRectangleSquare(new Rectangle, 4, 5); // 20
calculateRectangleSquare(new Square, 4, 5); // 25 ???


class Rectangle1
{
    protected $width;
    protected $height;
    
    public function setWidth($width)
    {
        $this->width = $width;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
    }
    
    public function getWidth()
    {
        return $this->width;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
}

class Square1
{
    protected $size;
    
    public function setSize($size)
    {
        $this->size = $size;
    }
    
    public function getSize()
    {
        return $this->size;
    }
}

//Принцип разделения интерфейса (Interface segregation)

interface IItem
{
    public function applyDiscount($discount);
    public function applyPromocode($promocode);
    
    public function setColor($color);
    public function setSize($size);
    
    public function setCondition($condition);
    public function setPrice($price);
}

interface IItem1
{
    public function setCondition($condition);
    public function setPrice($price);
}

interface IClothes
{
    public function setColor($color);
    public function setSize($size);
    public function setMaterial($material);
}

interface IDiscountable
{
    public function applyDiscount($discount);
    public function applyPromocode($promocode);
}

class Book implements IItem1, IDiscountable
{
    public function setCondition($condition){/*...*/}
    public function setPrice($price){/*...*/}
    public function applyDiscount($discount){/*...*/}
    public function applyPromocode($promocode){/*...*/}
}

class KidsClothes implements IItem1, IClothes
{
    public function setCondition($condition){/*...*/}
    public function setPrice($price){/*...*/}
    public function setColor($color){/*...*/}
    public function setSize($size){/*...*/}
    public function setMaterial($material){/*...*/}
}


//Принцип инверсии зависимостей (Dependency Invertion)

class OrderProcessor
{
    public function checkout($order){/*...*/}
}

class Customer
{
    private $currentOrder = null;
    
    public function buyItems()
    {
        if(is_null($this->currentOrder)){
            return false;
        }
        
        $processor = new OrderProcessor();
        return $processor->checkout($this->currentOrder);
    }
    
    public function addItem($item){
        if(is_null($this->currentOrder)){
            $this->currentOrder = new Order();
        }
        return $this->currentOrder->addItem($item);
    }
    public function deleteItem($item){
        if(is_null($this->currentOrder)){
            return false;
        }
        return $this->currentOrder ->deleteItem($item);
    }
}

//right variant

interface IOrderProcessor
{
    public function checkout($order);
}

class OrderProcessor1 implements IOrderProcessor
{
    public function checkout($order){/*...*/}
}

class Customer1
{
    private $currentOrder = null;
    
    public function buyItems(IOrderProcessor $processor)
    {
        if(is_null($this->currentOrder)){
            return false;
        }
        
        return $processor->checkout($this->currentOrder);
    }
    
    public function addItem($item){
        if(is_null($this->currentOrder)){
            $this->currentOrder = new Order();
        }
        return $this->currentOrder->addItem($item);
    }
    public function deleteItem($item){
        if(is_null($this->currentOrder)){
            return false;
        }
        return $this->currentOrder ->deleteItem($item);
    }
}








