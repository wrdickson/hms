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

}