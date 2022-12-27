<?php

namespace wrdickson\hms;

Class SaleTypeGroups {
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
        array_push( $items, $i );
      }

    return $items;
  }
}
