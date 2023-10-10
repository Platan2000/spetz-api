<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule("sale");
use Bitrix\Sale\Order;
use Bitrix\Sale\Internals\StatusTable;

// Функция, которая будет возвращать статус заказа по его ID
function getOrderStatus($orderId) {
    $orderStatus = '';
    $order = Order::loadByAccountNumber($orderId);

    
    $basket = $order->getBasket();
    $basketItems = $basket->getBasketItems();
    $products = [];
    foreach ($basketItems as $basketItem) {
       
        $product = [
            'id' => $basketItem->getField('ID'),
            'name' => $basketItem->getField('NAME'),
            'quantity' => $basketItem->getField('QUANTITY'),
            'price' => $basketItem->getField('PRICE'),
        ];

        array_push($products,$product);
    };

    if ($order) {
        $statusId = $order->getField('STATUS_ID');
        $status = StatusTable::getList(['filter' => ['ID' => $statusId]])->fetch();
    
        if ($status) {
            // var_dump($status['ID']);
            $arStatus = CSaleStatus::GetByID($status['ID']);
            $orderStatus = ['name' => $arStatus['NAME'], 'id' => $status['ID']];
        } 
    }
    $response = [
        [$products, 'status' => $orderStatus]
    ];

    return $response;
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'])) {
   
    $orderId = $_GET['id'];
    $status = getOrderStatus($orderId);
}


?>