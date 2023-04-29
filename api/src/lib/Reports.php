<?php

namespace wrdickson\hms;
use \PDO;
use \DATETIME;
use Brick\Money\Money;

Class Reports {

  public static function generate_period_sales_report( $start, $end ) {

    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT sale_items.id, sale_items.folio, sale_items.sale_datetime, sale_items.sale_type, sale_types.title AS sale_type_title, sale_items.sale_subtotal, sale_items.sale_tax, sale_items.sale_total, sale_items.tax_types, sale_items.tax_spread FROM sale_items INNER JOIN sale_types ON sale_items.sale_type = sale_types.id WHERE sale_items.sale_datetime >= :start AND sale_items.sale_datetime <= :end ORDER BY sale_items.sale_datetime ASC");

    $stmt->bindParam(":start", $start);
    $stmt->bindParam(":end", $end);
    $i = $stmt->execute();
    $all_period_sales = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['folio'] = $obj->folio;
      $arr['sale_datetime'] = $obj->sale_datetime;
      $arr['sale_type'] = $obj->sale_type;
      $arr['sale_subtotal'] = $obj->sale_subtotal;
      $arr['sale_tax'] = $obj->sale_tax;
      $arr['sale_total'] = $obj->sale_total;
      $arr['tax_types'] = json_decode($obj->tax_types);
      $arr['tax_spread'] = json_decode($obj->tax_spread);
      array_push($all_period_sales, $arr);
    }

    //  create an array of all tax_types and sale_types in all_period_sales array
    $all_included_tax_types = array();
    $all_included_sale_types = array();
    foreach( $all_period_sales as $sale ) {
      //  tax_types
      foreach( $sale['tax_types'] as $tax_type ) {
        if(!in_array($tax_type, $all_included_tax_types)){
          array_push($all_included_tax_types, $tax_type);
        }
      }
      //  sale_type
        if(!in_array($sale['sale_type'], $all_included_sale_types)){
          array_push($all_included_sale_types, $sale['sale_type']);
        }
    }
    sort($all_included_tax_types);
    sort($all_included_sale_types);
    
    $tax_totals_by_tax_type = array();
    foreach($all_included_tax_types as $tax_type){
      $tax_totals_by_tax_type[$tax_type] = 0;
    }
    //  tax spread
    foreach($all_included_tax_types as $tax_type){
      foreach($all_period_sales as $sale){
        foreach($sale['tax_spread'] as $spread_obj){
          if($spread_obj->i == $tax_type)
            $tax_totals_by_tax_type[$tax_type] = (strval($tax_totals_by_tax_type[$tax_type] * 100) + strval($spread_obj->t * 100))/100;
        }
      }
    }

    // sale_subtotal, sale_tax, sale_total
    $sale_subtotal_by_sale_type = array();
    $sale_tax_total_by_sale_type = array();
    $sale_total_by_sale_type = array();

    foreach($all_included_sale_types as $sale_type){
      $sale_subtotal_by_sale_type[$sale_type] = 0;
      $sale_tax_total_by_sale_type[$sale_type] = 0;
      $sale_total_by_sale_type[$sale_type] = 0;
    }

    foreach($all_period_sales as $sale){
      foreach($all_included_sale_types as $sale_type){
        if($sale['sale_type'] == $sale_type){
          $sale_subtotal_by_sale_type[$sale_type] = (strval($sale_subtotal_by_sale_type[$sale_type] * 100) + strval($sale['sale_subtotal'] * 100))/100;
          $sale_tax_total_by_sale_type[$sale_type] = (strval($sale_tax_total_by_sale_type[$sale_type] * 100) + strval($sale['sale_tax'] * 100))/100;
          $sale_total_by_sale_type[$sale_type] = (strval($sale_total_by_sale_type[$sale_type] * 100) + strval($sale['sale_total'] * 100))/100;
        }
      }
    }
   

    $response = array();

    $response['sale_subtotal_by_sale_type'] = $sale_subtotal_by_sale_type;
    $response['sale_tax_total_by_sale_type'] = $sale_tax_total_by_sale_type;
    $response['sale_total_by_sale_type'] = $sale_total_by_sale_type;

    $response['all_included_sale_types'] = $all_included_sale_types;
    $response['tax_totals_by_tax_type'] = $tax_totals_by_tax_type;
    $response['all_included_tax_types'] = $all_included_tax_types;
    $response['all_period_sales'] = $all_period_sales;
    $response['all_tax_types'] = TaxTypes::get_all_tax_types();
    $response['all_sale_types'] = SaleTypes::get_all_sale_types();
    $response['report_start'] = $start;
    $response['report_end'] = $end;
    return $response;
  }

  public static function generate_shift_report( $start, $end, $account_id ) {
    $response = array();
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT payments.id, payments.folio, payments.payment_type, payments.posted_by, payments.datetime_posted, payments.total, accounts.username, payment_types.payment_title, folios.customer, customers.last_name, customers.first_name FROM payments INNER JOIN folios ON payments.folio = folios.id INNER JOIN customers ON folios.customer = customers.id INNER JOIN accounts ON payments.posted_by = accounts.id INNER JOIN payment_types ON payments.payment_type = payment_types.id WHERE payments.posted_by = :pb AND ( payments.datetime_posted > :start AND payments.datetime_posted < :end) ORDER BY payments.datetime_posted ASC");

    $stmt->bindParam(":pb", $account_id);
    $stmt->bindParam(":start", $start);
    $stmt->bindParam(":end", $end);
    $i = $stmt->execute();
    $payment_details_arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['folio'] = $obj->folio;
      $arr['payment_type'] = $obj->payment_type;
      $arr['posted_by'] = $obj->posted_by;
      $arr['datetime_posted'] = $obj->datetime_posted;
      $arr['total'] = $obj->total;

      $arr['username'] = $obj->username;
      $arr['payment_title'] = $obj->payment_title;

      $arr['last_name'] = $obj->last_name;
      $arr['first_name'] = $obj->first_name;

      
      array_push($payment_details_arr, $arr);
    }
    $response['all_payments'] = $payment_details_arr;
    //  payment_details_arr is a raw array of all payments

    //  generate an array of all payment types in payment_details_arr
    $included_payment_types_arr = array();
    foreach($payment_details_arr as $payment) {
      if(!in_array($payment['payment_type'], $included_payment_types_arr)){
        array_push($included_payment_types_arr, $payment['payment_type']);
      }
    }

    $wrapper_arr = array();
    //  iterate through included_payments_arr and generate an array
    foreach($included_payment_types_arr as $included_payment_type){
      $payment_type_arr = array();
      $payment_type_arr['payments'] = array();
      $payment_type_arr['total'] = 0;
      foreach($payment_details_arr as $payment){
        if($payment['payment_type'] == $included_payment_type){
          $payment_type_arr['payment_type'] = $included_payment_type;
          $payment_type_arr['payment_type_title'] = $payment['payment_title'];
          //  don't do floating point math!!!!!
          $payment_type_arr['total'] += intval($payment['total'] * 100)/100;
          $payment_type_arr['total'] = round($payment_type_arr['total'],2);
          array_push($payment_type_arr['payments'], $payment);
        }
      }
      array_push($wrapper_arr, $payment_type_arr);
    }

    $response['report_by_payment_type'] = $wrapper_arr;
    $account = new Account($account_id);
    $response['account'] = $account->to_array();
    $response['report_start'] = $start;
    $response['report_end'] = $end;
    return $response;
  }


}


