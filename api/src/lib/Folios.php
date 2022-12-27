<?php

namespace wrdickson\hms;

use \PDO;

Class Folios {

  public static function create_folio( $customer_id ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO folios ( customer ) VALUES ( :custId )");
    $stmt->bindParam(":custId", $customer_id, PDO::PARAM_INT);
    
    $execute = $stmt->execute();
    $new_id = $pdo->lastInsertId();

    return $new_id;
  }

}
