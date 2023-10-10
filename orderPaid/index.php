<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule("sale");
use Bitrix\Sale\Order;
use Bitrix\Sale\Internals\StatusTable;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
   
    $orderId = $_POST['id'];

    $order = \Bitrix\Sale\Order::load($orderId); 
    var_dump(!$order);

    if(!$order){
        echo('Заказ не найден'); 
        return;
    }
   
    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
        $r = $payment->setPaid('Y');
        if (!$r->isSuccess())
        {
            var_dump($r->getErrorMessages());
        }  
    }
    $order->save(); 
    echo 'Заказ успешкно оплачен';

}



?>