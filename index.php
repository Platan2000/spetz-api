<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    выавыаааыа
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script>
    // // delivery_id:
    //     // 3 - симферополь
    //     //  8 - евпатория
    //     //  9 - керчь
    //     //  10 - красноперекопск
    //     //  11 - судак
    //     //  12 - феодосия
    //     // 2 - доставка

    // const order = {
    //     'phone': '+349023423424',
    //     'delivery': 2,
    //     'address': 'Москва, Пушкина 10',
    //     'comment': 'test',
    //     'city' :  3,
    //     products:  [
    //         {
    //             id: 42675,
    //             quantity: 2,
    //             price: 27,
                
                
    //         },
    //         {
    //             id: 56741,
    //             quantity : 1,
    //             price: 35,
    //         },
           
    //     ]
    // }

    // const order = {
    //     "phone": "+349023423424",
    //     "delivery": 2,
    //     "address": "Москва, Пушкина 10",
    //     "comment": "test",
    //     "city": 3,
    //     "products": [
    //         {
    //             "id": 42675,
    //             "quantity": 2,
    //             "price": 27
    //         },
    //         {
    //             "id": 56741,
    //             "quantity": 1,
    //             "price": 35
    //         }
    //     ]
    // }

    // const order = {
    //     "phone": "+349023423424#34324",
    //     "delivery":2,
    //     "address":null,
    //     "comment":null,
    //     "city":3,
    //     "products":[
    //         {
    //             "id":"60750",
    //             "name":null,
    //             "quantity":1,
    //             "price":600.00
    //         }
    //     ]
    // }

    const order = {
        "phone": "79852381880", 
        "delivery": 2, 
        "address": "Временная заглушка", 
        "comment": null, 
        "city": 3, 
        "products": [ 
            { 
                "id": "60750", 
                "name": null, 
                "quantity": 1, 
                "price": 600.00 
            } 
        ] 
        
    }

    
    // const params = new URLSearchParams()
    // params.append('phone', order.phone)
    // params.append('delivery', order.delivery)
    // params.append(`address`, order.address);
    // params.append(`comment`, order.comment);
    // params.append(`city`, order.city);
    // order.products.forEach((product, index) => {
    //     params.append(`products[${index}][id]`, product.id);
    //     params.append(`products[${index}][quantity]`, product.quantity);
    //     params.append(`products[${index}][price]`, product.price);
       
    // });

    let axiosConfig = {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    };

    axios.post('createOrder.php',order, axiosConfig )
    .then(res => console.log(res))

    // axios.post('createOrder.php',params)
    // .then(res => console.log(res))

    // const params = new URLSearchParams({id: 26827}).toString()
    // axios.get(`https://spetz-test.ru/php/api/order/?${params}`)
    // .then(res => console.log(res.data))

    // const params = new URLSearchParams()
    // params.append('id', 342344)
    // axios.post(`https://spetz-test.ru/php/api/orderPaid/`, params)
    // .then(res => console.log(res))
    
</script>
</html>

