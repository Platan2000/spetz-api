<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("sale");
use Bitrix\Sale\Order,
    Bitrix\Sale\BasketItem,
    Bitrix\Sale\Delivery\Services\Manager as DeliveryManager,
    Bitrix\Sale\PaySystem\Manager as PaySystemManager,
    Bitrix\Sale\PropertyValue,
    Bitrix\Sale\Internals\OrderPropsValueTable,
    Bitrix\Sale\Internals\PaymentTable,
	Bitrix\Catalog\PriceTable,
    Bitrix\Sale\Internals\ShipmentTable,
	Bitrix\Sale\Price;


//  Поля для формирования заказа

// delivery_id - id службы доставки
// payment_id - id способоа оплаты


function createOrder($orderParams) {
	
	[$products, $deliveryId, $phone, $address, $comment, $cityid] = $orderParams;
	
	
	$basket = Bitrix\Sale\Basket::create('s1'); // Создание корзины для заказа

	foreach ($products as $product) // Добавление товаров в корзину
	{

				
		$item = $basket->createItem("catalog", $product["id"]);
		
		$item->setFields(array(
            'QUANTITY' => $product['quantity'], // Количество товара
            'CURRENCY' => 'RUB',
            'LID' => 's1',
            'PRODUCT_PROVIDER_CLASS' => '\Bitrix\Catalog\Product\CatalogProvider', // Класс провайдера для работы с товарами каталога
            'PRODUCT_ID' => $product['id'], // ID товара
			'CUSTOM_PRICE' => 'Y',
			'PRICE' => $product['price'],

        ));
	}
	
	// Создание объекта заказp
	$siteId = 's1'; // код сайта
	$userId = 1; // ID пользователя
	$newOrder = \Bitrix\Sale\Order::create($siteId, $userId);

	// свойства заказа

	$propertyCollection = $newOrder->getPropertyCollection(); 
	
	$prop = $propertyCollection->getItemByOrderPropertyId(38); 
	$prop->setValue('rncb');

	// Код пункта самовывоза

	$prop = $propertyCollection->getItemByOrderPropertyId(29); 
	$prop->setValue($phone);

	// Город доставки или пункт выдачи
	$city = [
		3 => 'Симферополь',
		8 => 'Евпатория',
		9 => 'Керчь',
		10 => 'Красноперекопск',
		11 => 'Судак',
		12 => 'Феодосия',
	];

	
	// 2 - доставка курьером

	if($deliveryId === "2") { // если не самовывоз
		// var_dump($address);
		
		$getCurretnCity = array_filter($city, function($value, $key) use ($cityid) {
			return $key == $cityid;
		}, ARRAY_FILTER_USE_BOTH);
	
		$filteredCity = reset($getCurretnCity);
		
		// var_dump($address);
		
		// $addressProperty = $propertyCollection->getAddress();
		// $addressProperty->setValue($address);

		$prop = $propertyCollection->getItemByOrderPropertyId(37); 
		$prop->setValue($address);
		
		$prop = $propertyCollection->getItemByOrderPropertyId(40); 
		$prop->setValue($filteredCity);
		
	}
	
	if(($deliveryId !== "2")) {
		
		$getCurretnCity = array_filter($city, function($value, $key) use ($deliveryId) {
			return $key == $deliveryId;
		}, ARRAY_FILTER_USE_BOTH);
		
		$filteredCity = reset($getCurretnCity);
		// var_dump($filteredCity);

		// Код пункта самовывоза	
		$prop = $propertyCollection->getItemByOrderPropertyId(39); 
		$arDelivery = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
		$prop->setValue($filteredCity);
		

		$prop = $propertyCollection->getItemByOrderPropertyId(40); 
		$prop->setValue($filteredCity);
		
	}

	// Установка типа плательщика для заказа
	$newOrder->setPersonTypeId(1); // 1 - ID типа плательщика физ лицо
	$newOrder->setBasket($basket); // Установка корзины для заказа
	
	// коммент 
	$newOrder->setField('USER_DESCRIPTION', $comment);
	
	// Создание службы доставки для заказа
	$shipmentCollection = $newOrder->getShipmentCollection();

	$shipment = $shipmentCollection->createItem(
		Bitrix\Sale\Delivery\Services\Manager::getObjectById($deliveryId) // 1 - ID службы доставки
	);

	// оздание коллекции элементов отгрузки
	$shipmentItemCollection = $shipment->getShipmentItemCollection();

	//Добавление товаров из корзины в отгрузку
	foreach ($basket as $basketItem)
	{
		$item = $shipmentItemCollection->createItem($basketItem);
		$item->setQuantity($basketItem->getQuantity());		
	}

	// Создание коллекции платежей для заказа
	$paymentCollection = $newOrder->getPaymentCollection();

	// Создание платежа для заказа 
	$payment = $paymentCollection->createItem(
		Bitrix\Sale\PaySystem\Manager::getObjectById(13) // 13 - ID платежной системы RNCB
	);

	// Установка суммы и валюты для платежа:
	$payment->setField("SUM", $newOrder->getPrice());
	
	
	$payment->setField("CURRENCY", $newOrder->getCurrency());

	$result = $newOrder->save();
	if ($result->isSuccess())
	{ 
		$orderId = $newOrder->getId();
		return $orderId;
	} else {
		$errors = $result->getErrors();
		return false;
	}
}

function getOrderStatus($orderId) {
    // Получение статуса заказа
    $order = Order::load($orderId);
    if ($order) {
        $status = $order->getField('STATUS_ID');
        return $status;
    }

    return false;
}
// var_dump($_POST);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$log = '///////////////////////////////////////////////////////////////n';
	$log .= date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);
	file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/rncb.txt', $log . PHP_EOL, FILE_APPEND);

	$request = $_POST['request']; // получение строки JSON из $_POST

	$data = json_decode($request, true); // преобразование строки JSON в массив
	
    $products = $data['products'];
	
	$deliveryId = $data['delivery'] ?? 1;
	
	$phone = $data['phone'];

	$cityid = $data['city'];

	if($phone == 'undefined') {
		echo json_encode(['error' => 'phone mast be not empty']);
		return;
	}
	$orderParams = [$products, $deliveryId, $phone, $address, $comment, $cityid];
	
    if (!empty($products)) {
        $orderId = createOrder($orderParams);

        if ($orderId) {
            $status = getOrderStatus($orderId);
            echo json_encode(['order_id' => $orderId, 'status' => $status]);
        } else {
            echo json_encode(['error' => 'Failed to create order.']);
        }
    } else {
        echo json_encode(['error' => 'Empty product list.']);
    }
}

?>