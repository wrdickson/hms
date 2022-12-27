<?php

namespace wrdickson\hms;

Class Payments {

  public static function create_payment( $folio, $payment_type, $posted_by, $datetime_posted, $total ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO payments ( folio, payment_type, posted_by, datetime_posted, total) Values ( :fol, :pyt, :pby, :dtp, :ttl)");
    $stmt->bindParam(':fol', $folio);
    $stmt->bindParam(':pyt', $payment_type);
    $stmt->bindParam(':pby', $posted_by);
    $stmt->bindParam(':dtp', $datetime_posted);
    $stmt->bindParam(':ttl', $total);
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }

}
