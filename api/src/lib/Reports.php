<?php

namespace wrdickson\hms;
use \PDO;

Class Reports {

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
          $payment_type_arr['total'] = (intval($payment_type_arr['total'] * 100 ) + intval($payment['total'] * 100))/100;
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


