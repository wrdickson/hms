<?php

namespace wrdickson\hms;

use \PDO;
use \Exception;

Class Customers {

  public static function create_customer( $last_name, $first_name, $address_1, $address_2, $city, $region, $country, $postal_code, $phone, $email ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO customers (last_name, first_name, address_1, address_2, city, region, country, postal_code, phone, email) VALUES (:ln, :fn, :a1, :a2, :ci, :re, :co, :pc, :ph, :em)");
    $stmt->bindParam(":ln", $last_name, PDO::PARAM_STR);
    $stmt->bindParam(":fn", $first_name, PDO::PARAM_STR);
    $stmt->bindParam(":a1", $address_1, PDO::PARAM_STR);
    $stmt->bindParam(":a2", $address_2, PDO::PARAM_STR);
    $stmt->bindParam(":ci", $city, PDO::PARAM_STR);
    $stmt->bindParam(":re", $region, PDO::PARAM_STR);
    $stmt->bindParam(":co", $country, PDO::PARAM_STR);
    $stmt->bindParam(":pc", $postal_code, PDO::PARAM_STR);
    $stmt->bindParam(":ph", $phone, PDO::PARAM_STR);
    $stmt->bindParam(":em", $email, PDO::PARAM_STR);
    $i = $stmt->execute();
    $insertId = $pdo->lastInsertId();
    return $insertId;
  }

  public static function search_customers( $last_name, $first_name, $offset, $limit ){
    $last = $last_name . "%";
    $first = $first_name ."%";
    $pdo = DataConnector::get_connection();
    //are last_name and first_name both > 1?
    if( strlen($last) > 1 && strlen($first) > 1 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last AND first_name LIKE :first ORDER BY last_name, first_name ASC LIMIT :offset, :limit" );
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last AND first_name LIKE :first");
      $stmt_count->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt_count->bindParam(":first",$first,PDO::PARAM_STR);

    //is last_name >1 while first_name = 0?
    } elseif ( strlen($last) > 1 && strlen($first) == 0 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last ORDER BY last_name, first_name ASC LIMIT :offset, :limit");
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last");
      $stmt_count->bindParam(":last", $last);
    //is first_name > 1 and last_name = 0?
    } elseif ( strlen($first) > 1 && strlen($last) == 0 ){
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE first_name LIKE :first ORDER BY last_name, first_name ASC LIMIT :offset, :limit");
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset);
      $stmt->bindParam(":limit", $limit);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE first_name LIKE :first");
      $stmt_count->bindParam(":first", $first);
    //first and last are both 0 (ie empty)
    } else {
      $stmt = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last AND first_name LIKE :first ORDER BY last_name, first_name ASC LIMIT :offset,:limit");
      $stmt->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt->bindParam(":first",$first,PDO::PARAM_STR);
      $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
      $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
      //  count
      $stmt_count = $pdo->prepare("SELECT * FROM customers WHERE last_name LIKE :last AND first_name LIKE :first");
      $stmt_count->bindParam(":last",$last,PDO::PARAM_STR);
      $stmt_count->bindParam(":first",$first,PDO::PARAM_STR);
    }
    $stmt->execute();
    $stmt_count->execute();
    $cArr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iCust = new Customer($obj->id);
      array_push($cArr, $iCust->to_array());
    }
    $result['customers'] = $cArr;
    $result['count'] = $stmt_count->rowCount();
    return $result;
  }

}
