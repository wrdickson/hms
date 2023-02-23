<?php

namespace wrdickson\hms;

Class SaleTypeGroups {

  public static function create_sale_type_group ( $title, $is_active, $display_order ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO sale_type_groups ( title, is_active, display_order ) VALUES ( :t, :ia, :do )");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    return $stmt->execute();
  }

  public static function get_all_sale_type_groups () {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM sale_type_groups ORDER BY display_order");
    $stmt->execute();
    $items = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['title'] = $obj->title;
      $i['display_order'] = $obj->display_order;
      $i['is_active'] = $obj->is_active;
      array_push( $items, $i );
    }
    return $items;
  }

  public static function update_sale_type_group ( $id, $title, $display_order, $is_active ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE sale_type_groups SET title = :t, is_active = :ia, display_order = :do WHERE id = :id ");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

  public static function delete_sale_type_group ( $id ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("DELETE FROM sale_type_groups WHERE id = :id ");
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }
}
