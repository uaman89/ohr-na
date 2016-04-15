<?php

/*
 * Ограничения

Не более 2 одновременных запросов с одного адреса от одного пользователя.
Не более 5 запросов в секунду с одного адреса от одного пользователя.
Не более 10 Мб данных в одном запросе, отправляемом на сервер.
Длительность одного запроса — не более 1 минуты.
Листание (paging)

Большинство списков и отчетов поддерживают листание. За него отвечают два параметра:

offset — отступ от первого элемента (считается с нуля);
limit — кол-во элементов на странице (по умолчанию 25, максимум 100).
 * */

$username = 'admin@ohrana-ua';
$password = 'ptashkinsv';


$apiUrl = 'https://online.moysklad.ru/api/remap/1.0/';
$getAll = 'report/stock/all';
$byStore = 'report/stock/bystore';// Получить все остатки по складам

$limit = '100'; //limit — кол-во элементов на странице (по умолчанию 25, максимум 100).


$context = stream_context_create(array(
    'http' => array(
        'header'  => "Authorization: Basic " . base64_encode("$username:$password")
    )
));

//GET /report/stock/all Получить все остатки

$start = microtime(true);

$products = getAllProducts();
echo '<br>count($products):'.count($products);
//var_dump($products);
$time1 = microtime(true);
echo '<br>time in work: '.number_format($time1 - $start, 2, '.', '').'sec';

$arrStock = getStockByStore( $products );
$time2 = microtime(true);
echo '<br>time in work: '.number_format($time2 - $time1, 2, '.', '').'sec';
var_dump($arrStock);

//--------------------------------------------------------------------------------------------------------------------------------------------------------------

function getAllProducts(){
    global $apiUrl, $context, $limit;

    $arrProducts = null;

    $url = $apiUrl.'entity/product';

    $offset = 0;

//    $i=0;
    do{
        $json = file_get_contents( $url.'?offset='.$offset.'&limit='.$limit, false, $context );
        $data = json_decode($json,true);

        foreach ($data['rows'] as $product) {
            $arrProducts[ $product['id'] ] = array(
                'name' => $product['name'],
                'code' => $product['code'],
            );
        }
        $size = $data['metaTO']['size'];
        $offset = $data['metaTO']['offset'] + $limit;
        $remain = $size - $offset;

        echo '<br>size:'.$size.', offset: '.$offset.', remain: ', $remain;


//        if ( $i >= 5 ) break;
//        $i++;
    }
    while ( $remain > 0 );

    return $arrProducts;
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------

function getStockByStore( $arrProducts ){

    if ( empty($arrProducts) ) exit( 'empty $arrProducts at '.__LINE__.'!' );

    global $apiUrl, $context, $limit;

    $url = $apiUrl.'report/stock/bystore';
    $offset = 0;

    $arrProdStock = null;

    $i=0;
    do{
        $json = file_get_contents( $url.'?offset='.$offset.'&limit='.$limit, false, $context );
        $data = json_decode($json, true);

        foreach ($data['rows'] as $row) {
            $href = $row['meta']['href'];
            $start = strpos($href,'product/') + 8;
            $prod_id = substr( $href, $start, 36 ); //36 - length of uuid

            if ( isset($arrProducts[$prod_id]) ){
                $prod_name = $arrProducts[$prod_id]['name'];
                $prod_code = $arrProducts[$prod_id]['code'];
            }

            $arrProdStock[ $prod_id ] = array(
                'prod_name' => $prod_name,
                'prod_code' => $prod_code,

            );

            foreach ($row['stockByStore'] as $store) {
                $store_id = $store['meta']['href'];
                $arrProdStock[ $prod_id ]['stockByStores'][] = array(
                    'storeId' => $store_id,
                    'storeName' => $store['name'],
                    'stock' => $store['stock'],
                );
            }
        }
        $size = $data['metaTO']['size'];
        $offset = $data['metaTO']['offset'] + $limit;
        $remain = $size - $offset;

        echo '<br>size:'.$size.', offset: '.$offset.', remain: ', $remain;

//        if ( $i >= 2 ) break;
//        $i++;

    }
    while ( $remain > 0 );

    echo 'получено товаров: '.count($arrProdStock);
    return $arrProdStock;
}