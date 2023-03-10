<?php

// Importante: importar la libería Automattic de woocommerce utilizando composer o importar la carpeta vendor de automattic en la misma ubicación de este archivo
require __DIR__ . '/vendor/autoload.php';

//Importamos las clases de Automattic que nos permitirán la conexión con la API de Woocommerce
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;


// Abrimos la conexión de la API de Woocommerce con nuestro sitio. Donde la URL debe ser el sitio final donde ejecutaremos las consultas de la API. Utilizamos la consumer key y consumer secret de woocommerce (Woocommerce/Settings/Advanced)
$woocommerce = new Client(
    'https://sync.javcalderon.com/', 
    'ck_3a44180a3f0b2a603a2c29cb4fe0d2998a719338', 
    'cs_e9483dd66fc007e6b31ef9dd968efd8176788b71',
    [
        'wp_api' => true,
        //Utilizamos la versión 3 de Woocommerce API
        'version' => 'wc/v3',
    ]
);

// Nos conectamos a la base de datos donde se encuentra la tabla con los campos a sincronizar
$mysqli = new mysqli("localhost", "root", "", "basededatos");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

// Guardamos el select de nuestra tabla donde se encuentran los productos con los campos a sinzronizar. En mi caso creé una tabla "products"
$products = $mysqli->query("SELECT * FROM products");


// de nuestra consulta, recorremos cada producto
while ($product = $products->fetch_assoc()) {

    // en mi tabla "products" tengo un ID que debe coincidir con el product ID de woocommerce que quiero actualizar. Por ejemplo si mi tabla products el "ID" es 50, entonces el producto con ID 50 de mi tienda se actualizará.
    $id = $product['Id'];


    // utilizando la API de woocommerce, actualizo el precio de producto de la utilizando el endpoint /products y el ID de mi tabla personalizada que coincide con el del producto a actualizar
    print_r($woocommerce->put('products/' . $id, [
        //actualizo precio. Aquí mi campo de la tabla personalizada se llama "ListPrice1" que contiene el valor del precio a sincronizar
        'regular_price' => $product['ListPrice1']
        //aquí puedo agregar más campos a actualizar como por ejemplo stock
    ]));


}

?>
