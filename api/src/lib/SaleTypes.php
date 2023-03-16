<?php

namespace wrdickson\hms;

Class SaleTypes {

  public static function get_all_sale_types () {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM sale_types ORDER BY is_active DESC, display_order");
    $stmt->execute();
    $sale_types = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['title'] = $obj->title;
      $i['tax_types'] = json_decode($obj->tax_types);
      $i['sale_type_group'] = $obj->sale_type_group;
      $i['is_fixed_price'] = $obj->is_fixed_price;
      $i['fixed_price'] = $obj->fixed_price;
      $i['is_active'] = $obj->is_active;
      $i['display_order'] = $obj->display_order;
      array_push( $sale_types, $i );
    }
    return $sale_types;
  }

  public static function create_sale_type (  $title, $tax_types, $sale_type_group, $is_fixed_price, $fixed_price, $is_actve, $display_order) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO sale_types (title, tax_types, sale_type_group, is_fixed_price, fixed_price, is_active, display_order) VALUES ( :t, :tt, :stg, :ifp, :fp, :ia, :do )");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':tt', json_encode($tax_types));
    $stmt->bindParam(':stg', $sale_type_group);
    $stmt->bindParam(':ifp', $is_fixed_price);
    $stmt->bindParam(':fp', $fixed_price);
    $stmt->bindParam(':ia', $is_actve);
    $stmt->bindParam(':do', $display_order);
    return $stmt->execute();
  }


  public static function update_sale_type ( $id, $title, $tax_types, $sale_type_group, $is_fixed_price, $fixed_price, $is_actve, $display_order) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE sale_types SET title = :t, tax_types = :tt, sale_type_group = :stg, is_fixed_price = :ifp, fixed_price = :fp, is_active = :ia, display_order = :do WHERE id = :id ");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':tt', json_encode($tax_types));
    $stmt->bindParam(':stg', $sale_type_group);
    $stmt->bindParam(':ifp', $is_fixed_price);
    $stmt->bindParam(':fp', $fixed_price);
    $stmt->bindParam(':ia', $is_actve);
    $stmt->bindParam(':do', $display_order);
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

}