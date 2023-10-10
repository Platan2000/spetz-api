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
    
    if ($order) {
        $statusId = $order->getField('STATUS_ID');
        $status = StatusTable::getList(['filter' => ['ID' => $statusId]])->fetch();
    
        if ($status) {
            var_dump($status['ID']);
            $arStatus = CSaleStatus::GetByID($status['ID']);
            $orderStatus = $arStatus['NAME'];
        } 
    }
    
    return $orderStatus;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
   
    $orderId = $_POST['order_id'];
  
    $status = getOrderStatus($orderId);
    // var_dump($orderId);
    echo $status;
}


?>