<?php

namespace wrdickson\hms;

use \PDO;
use \Exception;

Class Folio{
  //$id, $customer, and $reservaton are from the folio table
  private $id;
  private $customer;
  private $payments;
  private $sale_items;
  private $sale_detail;

  public function __construct( $id ) {

    $pdo = DataConnector::get_connection();

    //first get the basics: id, customer
    $stmt = $pdo->prepare("SELECT * FROM folios WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->customer = $obj->customer;
      
    }
   
      //  1. sale items:
    $stmt = $pdo->prepare("SELECT * FROM sale_items WHERE folio = :i");
    $stmt->bindParam(':i', $id);
    $i = $stmt->execute();
    $items_arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['folio'] = $obj->folio;
      $arr['sale_type'] = $obj->sale_type;
      $arr['posted_by'] = $obj->posted_by;
      $arr['description'] = $obj->description;
      $arr['sale_datetime'] = $obj->sale_datetime;
      $arr['sale_quantity'] = $obj->sale_quantity;
      $arr['sale_price'] = $obj->sale_price;
      $arr['sale_subtotal'] = $obj->sale_subtotal;
      $arr['sale_tax'] = $obj->sale_tax;
      
      $arr['sale_total'] = $obj->sale_total;
      $arr['tax_spread'] = json_decode( $obj->tax_spread, true );
      array_push( $items_arr, $arr );
    }
    $this->sale_items = $items_arr;

    // 2. payments
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE folio = :i");
    $stmt->bindParam(':i', $id);
    $i = $stmt->execute();
    $payments_arr = array();
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['folio'] = $obj->folio;
      $arr['payment_type'] = $obj->payment_type;
      $arr['posted_by'] = $obj->posted_by;
      $arr['datetime_posted'] = $obj->datetime_posted;
      $arr['total'] = $obj->total;
      array_push($payments_arr, $arr);
    }
    $this->payments = $payments_arr;

    // 3. sale_detail
    $stmt = $pdo->prepare("SELECT payments.id AS payment_id, payments.total AS payment_total, payments.datetime_posted AS posted_date, accounts.username as posted_by, sale_types.title, sale_items.description, sale_items.sale_quantity, sale_items.sale_price, sale_items.sale_subtotal, sale_items.sale_tax, sale_items.sale_total, payment_types.payment_title AS payment_type FROM payments LEFT JOIN sale_items ON payments.id = sale_items.payment_id INNER JOIN sale_types ON sale_items.sale_type = sale_types.id INNER JOIN accounts on payments.posted_by = accounts.id INNER JOIN payment_types on payments.payment_type = payment_types.id WHERE payments.folio = :i ORDER BY payments.id ASC");

    $stmt->bindParam(":i", $id);
    $i = $stmt->execute();
    $payment_details_arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->payment_id;
      $arr['payment_total'] = $obj->payment_total;
      $arr['posted_date'] = $obj->posted_date;
      $arr['posted_by'] = $obj->posted_by;
      $arr['title'] = $obj->title;
      $arr['description'] = $obj->description;
      $arr['sale_quantity'] = $obj->sale_quantity;
      $arr['sale_price'] = $obj->sale_price;
      $arr['sale_subtotal'] = $obj->sale_subtotal;
      $arr['sale_tax'] = $obj->sale_tax;
      $arr['sale_total'] = $obj->sale_total;
      $arr['payment_type'] = $obj->payment_type;
      array_push($payment_details_arr, $arr);
    }
    $this->sale_detail = $payment_details_arr;
    //$this->sale_detail = $this->id;
  }

  public function get_id(){
    return $this->id;
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['customer'] = $this->customer;
    $arr['payments'] = $this->payments;
    $arr['sale_items'] = $this->sale_items;
    $arr['sale_detail'] = $this->sale_detail;
    return $arr;
  }
}
