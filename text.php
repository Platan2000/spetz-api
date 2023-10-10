
<?
use Bitrix\Sale\Order,
    Bitrix\Sale\BasketItem,
    Bitrix\Sale\Delivery\Services\Manager as DeliveryManager,
    Bitrix\Sale\PaySystem\Manager as PaySystemManager,
    Bitrix\Sale\PropertyValue,
    Bitrix\Sale\Internals\OrderPropsValueTable,
    Bitrix\Sale\Internals\PaymentTable,
    Bitrix\Sale\Internals\ShipmentTable;

// Создание заказа из 2 и более товаров
function createOrder($products) {
    $order = Order::create('s1', 1); // SITE_ID и ID типа плательщика (1 - физическое лицо)
    echo '<pre>';
    var_dump($order);
    // Добавление товаров в корзину
    $basket = $order->getBasket();

    var_dump($basket);
    foreach ($products as $product) {
        var_dump($product['id']);
        $item = $basket->createItem('catalog', $product['id']); // 'catalog' - код хранилища корзины, $product['id'] - ID товара
        $item->setFields(array(
            'QUANTITY' => $product['quantity'], // Количество товара
            'CURRENCY' => 'RUB',
            'LID' => 's1',
            'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider', // Класс провайдера для работы с товарами каталога
            'PRODUCT_ID' => $product['id'] // ID товара
        ));
    }

    // Установка свойств заказа
    $propertyCollection = $order->getPropertyCollection();

    // Добавление свойства "Телефон"
    $phonePropValue = PropertyValue::create($propertyCollection, 'PHONE', $phone);
    $phonePropValue->setField('VALUE', $phone);

    // Добавление свойства "Email"
    $emailPropValue = PropertyValue::create($propertyCollection, 'EMAIL', $email);
    $emailPropValue->setField('VALUE', $email);

    // Сохранение заказа
    $result = $order->save();
    if ($result->isSuccess()) {
        $orderId = $order->getId();
        return $orderId;
    } else {
        $errors = $result->getErrors();
        return false;
    }
}

function getOrderStatus($orderId) {
    // Получение статуса заказа
    var_dump($orderId);
    $order = Order::load($orderId);
    if ($order) {
        $status = $order->getField('STATUS_ID');
        return $status;
    }

    return false;
}

// Обработка POST-запроса для создания заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = $_POST['products'];
   

    if (!empty($products)) {
        $orderId = createOrder($products);
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