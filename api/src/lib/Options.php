<?php

namespace wrdickson\hms;

use \PDO;

Class Options {

  private $all_options = null;


  public function __construct () {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM options");
    $stmt->execute();
    $items = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['option_name'] = $obj->option_name;
      if( $this->is_json($obj->option_value) == true){
        $i['is_json'] = 1;
        $i['option_value'] = json_decode($obj->option_value);
      } else {
        $i['is_json'] = 0;
        $i['option_value'] = $obj->option_value;
      }
      $i['autoload'] = $obj->autoload;
      array_push( $items, $i );
    }
    $this->all_options = $items;
  }

  public function get_all_options () {
    $arr = array();
    foreach($this->all_options as $option ) {
      array_push($arr, $option);
    };
    return $arr;
  }

  public function get_autoload_options () {
    $arr = array();
    foreach($this->all_options as $option ) {
      if( $option['autoload'] == 1 ) {
        $arr[ $option['option_name' ] ] = array(
          'id' => $option['id'],
          'option_value' => $option['option_value'],
          'autoload' => $option['autoload']
        );
      }
    };
    return $arr;
  }

  private function is_json( $string ) {
    return is_string($string) && is_array(json_decode($string, true) ) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
  }


}
