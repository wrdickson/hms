<?php

namespace wrdickson\hms;

Class PaymentTypes {

  public static function create_payment_type ( $payment_title, $is_active, $display_order ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO payment_types (payment_title, is_active, display_order) VALUES (:t, :ia, :do )");
    $stmt->bindParam(':t', $payment_title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    return $stmt->execute();
  }

  public static function get_active_payment_types(){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM payment_types WHERE is_active = 1 ORDER BY display_order");
    $stmt->execute();
    $active_payment_types = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['payment_title'] = $obj->payment_title;
      $i['is_active'] = $obj->is_active;
      $i['display_order'] = $obj->display_order;
      array_push( $active_payment_types, $i );
    }
    return $active_payment_types;
  }

  public static function get_all_payment_types(){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM payment_types ORDER BY display_order");
    $stmt->execute();
    $active_payment_types = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['payment_title'] = $obj->payment_title;
      $i['is_active'] = $obj->is_active;
      $i['display_order'] = $obj->display_order;
      array_push( $active_payment_types, $i );
    }
    return $active_payment_types;
  }

  public static function delete_payment_type ( $id ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("DELETE FROM payment_types WHERE id = :id ");
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

  public static function update_payment_type( $id, $payment_title, $is_active, $display_order ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE payment_types SET payment_title = :t, is_active = :ia, display_order = :do WHERE id = :id ");
    $stmt->bindParam(':t', $payment_title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

}
