<?php

namespace wrdickson\hms;

Class TaxTypes {

  public static function create_tax_type( $tax_title, $tax_rate, $is_current, $display_order ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO tax_types ( tax_title, tax_rate, is_current, display_order ) VALUES ( :tt, :tr, :ic, :do )");
    $stmt->bindParam(":tt", $tax_title);
    $stmt->bindParam(":tr", $tax_rate);
    $stmt->bindParam(":ic", $is_current);
    $stmt->bindParam(":do", $display_order);
    return $stmt->execute(); 
  }

  public static function delete_tax_type( $id ) {
    $pdo = DataConnector::get_connection();
    $stmt= $pdo->prepare("DELETE FROM tax_types WHERE id = :id");
    $stmt->bindParam(':id', $id );
    return $stmt->execute();
  }

  public static function get_all_tax_types(){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM tax_types ORDER BY is_current DESC, display_order, tax_title");
    $stmt->execute();
    $tax_types = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['tax_title'] = $obj->tax_title;
      $i['tax_rate'] = $obj->tax_rate;
      $i['is_current'] = $obj->is_current;
      $i['display_order'] = $obj->display_order;
      array_push( $tax_types, $i );
    }
    return $tax_types;
  }

  public static function update_tax_type ( $tax_type_id, $tax_title, $tax_rate, $is_current, $display_order ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE tax_types SET tax_title = :tt, tax_rate = :tr, is_current = :ic, display_order = :do WHERE id = :ttid ");
    $stmt->bindParam(':tt', $tax_title);
    $stmt->bindParam(':tr', $tax_rate);
    $stmt->bindParam(':ic', $is_current);
    $stmt->bindParam(':do', $display_order);
    $stmt->bindParam(':ttid', $tax_type_id); 
    return $stmt->execute();
  }

}
