<?php

namespace wrdickson\hms;

use \Exception;

$f3->route('POST /payments/quick-folio-sale', function ( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $response['auth'] = $f3auth;
  
  $params = json_decode($f3->get('BODY'), true );
  $response['params'] = $params;

  //  get useful variables
  $customer = $params['resCustomer'];
  $payment_type = $params['paymentType'];
  $res_folio = $params['resFolio'];
  $res_id = $params['resId'];
  $sale_items = $params['saleItems'];
  $sale_subtotal = $params['saleSubtotal'];
  $sale_tax = $params['saleTax'];
  $sale_total = $params['saleTotal'];
  $sold_by = $params['soldBy'];

  $sale_datetime = date("Y-m-d H:m:s");


  //  this validation should be much more robust,
  //  but this should toss horribly malformed requests
  $options = array(
    'paymentType' => array (
      'is_integer'
    ),
    'resCustomer' => array (
      'is_integer'
    ),
    'resFolio' => array (
      'is_integer'
    ),
    'resId' => array (
      'is_integer',
    ),
    'saleSubtotal' => array (
      'is_float'
    ),
    'saleTax' => array (
      'is_float'
    ),
    'saleTotal' => array (
      'is_float'
    ),
    'soldBy' => array (
      'is_float'
    ),
    'saleItems' => array (
      'is_array'
    )
  );

  $v = new Validate( $params, $options);
  $v_result = $v->validate();

  $response['validate'] = $v_result;
  $response['errors'] = array();
  if($v_result['valid']) {
    //  carry on  . . .
    //  TODO all bits of the transaction need the same datetime . . . don't use NOW()

    $pdo = DataConnector::get_connection();
    $pdo->beginTransaction();

    //  1. iterate through the sale items and create
    $i = 0;
    $sale_items_posted = false;
    $items_posted_ok = true;
    foreach($sale_items as $sale_item) {
      $description = $sale_item['description'];
      $sale_type = $sale_item['saleType'];
      $sale_quantity = $sale_item['saleQuantity'];
      $sale_price = $sale_item['salePrice'];
      $sale_subtotal = $sale_item['saleSubtotal'];
      $sale_tax = $sale_item['saleTax'];
      $sale_total = $sale_item['saleTotal'];
      $tax_types = $sale_item['taxTypes'];
      $tax_spread = $sale_item['taxSpread'];

      try{
        $stmt = $pdo->prepare("INSERT INTO sale_items ( folio, sale_type, posted_by, description, sale_datetime, sale_quantity,
        sale_price, sale_subtotal, sale_tax, sale_total, tax_types, tax_spread)
        VALUES (:folio, :sale_type, :posted_by, :description, :sale_datetime, :sale_quantity, :sale_price, :sale_subtotal, :sale_tax, :sale_total, :tax_types, :tax_spread)");
        $stmt->bindParam(":folio", $res_folio);
        $stmt->bindParam(":sale_type", $sale_type);
        $stmt->bindParam(":posted_by", $sold_by);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":sale_datetime", $sale_datetime);
        $stmt->bindParam(":sale_quantity", $sale_quantity);
        $stmt->bindParam(":sale_price", $sale_price);
        $stmt->bindParam(":sale_subtotal", $sale_subtotal);
        $stmt->bindParam(":sale_tax", $sale_tax);
        $stmt->bindParam(":sale_total", $sale_total);
        $stmt->bindParam(":tax_types", json_encode( $tax_types) );
        $stmt->bindParam(":tax_spread", json_encode( $tax_spread) );
        $sale_items_posted = $stmt->execute();
      } catch (Exception $e) {
        array_push($response['errors'], $e);
        $pdo->rollBack();
        $items_posted_ok = false;
      }
      $i+=1;
    }

    //  3. post a payment
    $payment_posted = false;
    if( $items_posted_ok ) {
      try {
        $stmt = $pdo->prepare("INSERT INTO payments ( folio, payment_type, posted_by, datetime_posted, total) VALUES ( :folio, :payment_type, :posted_by, :datetime_posted, :total)");
        $stmt->bindParam(":folio", $res_folio);
        $stmt->bindParam(":payment_type", $payment_type);
        $stmt->bindParam(":posted_by", $sold_by );
        $stmt->bindParam(":datetime_posted", $sale_datetime);
        $stmt->bindParam(":total", $sale_total);
        $payment_posted = $stmt->execute();
      } catch ( Exception $e ) {
        $items_posted_ok = false;
        $pdo->rollBack();
      }
    }

    if( $items_posted_ok ) {
      $pdo->commit();
    }
    $response['sale_items_posted'] = $sale_items_posted;
    $response['payment_posted'] = $payment_posted;
    print json_encode($response);

  } else {
    $f3->error(400, 'params malformed or missing');
  }
});

