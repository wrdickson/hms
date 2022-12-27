<?php

namespace wrdickson\hms;

use \PDO;

class SpaceTypes {

  public static function get_space_types () {

    $pdo = DataConnector::get_connection();
    //todo validate user
  
    $stmt = $pdo->prepare("SELECT * FROM space_types");
    $execute= $stmt->execute();
    $arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['title'] = $obj->title;
        $iArr['is_active'] = $obj->is_active;
        $arr[$obj->id] = $iArr;
    };
    $response['execute'] = $execute;
    $response['space_types'] = $arr;
    return $response;
  }

}
