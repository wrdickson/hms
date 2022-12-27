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
    return $arr;
  }
}
