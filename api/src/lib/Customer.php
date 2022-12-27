<?php

namespace wrdickson\hms;

use \PDO;

Class Customer {
  private $id;
  private $last_name;
  private $first_name;
  private $address_1;
  private $address_2;
  private $city;
  private $region;
  private $country;
  private $postal_code;
  private $email;
  private $phone;
  
  public function __construct($id){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(":id",$id,PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->last_name = $obj->last_name;
      $this->first_name = $obj->first_name;
      $this->address_1 = $obj->address_1;
      $this->address_2 = $obj->address_2;
      $this->city = $obj->city;
      $this->region = $obj->region;
      $this->country = $obj->country;
      $this->postal_code = $obj->postal_code;
      $this->phone = $obj->phone;
      $this->email = $obj->email;
    }
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['last_name'] = $this->last_name;
    $arr['first_name'] = $this->first_name;
    $arr['address_1'] = $this->address_1;
    $arr['address_2'] = $this->address_2;
    $arr['city'] = $this->city;
    $arr['region'] = $this->region;
    $arr['country'] = $this->country;
    $arr['postal_code'] = $this->postal_code;
    $arr['phone'] = $this->phone;
    $arr['email'] = $this->email;
    return $arr;
  }
  
  public static function getCustomers(){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM customers ORDER BY last_name ASC");
    $stmt->execute();
    $cArr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr =array();
      $iArr['id'] = $obj->id;
      $iArr['last_name'] = $obj->last_name;
      $iArr['first_name'] = $obj->first_name;
      $iArr['address_1'] = $obj->address_1;
      $iArr['address_2'] = $obj->address_2;
      $iArr['city'] = $obj->city;
      $iArr['region'] = $obj->region;
      $iarr['country'] = $obj->country;
      $iArr['postal_code'] = $obj->postal_code;
      $iArr['phone'] = $obj->phone;
      $iArr['email'] = $obj->email;
      array_push($cArr, $iArr);
    }
    return $cArr;
  }
  
  public function update(){
    //TODO validate
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE customers SET last_name = :last_name, first_name = :first_name, address_1 = :address_1, address_2 = :address_2, city = :city, region = :region, country = :country, postal_code = :postal_code, phone = :phone, email = :email WHERE id=:id");
    $stmt->bindParam(":last_name", $this->last_name, PDO::PARAM_STR);
    $stmt->bindParam(":first_name", $this->first_name, PDO::PARAM_STR);
    $stmt->bindParam(":address_1", $this->address_1, PDO::PARAM_STR);
    $stmt->bindParam(":address_2", $this->address_2, PDO::PARAM_STR);
    $stmt->bindParam(":city", $this->city, PDO::PARAM_STR);
    $stmt->bindParam(":region", $this->region, PDO::PARAM_STR);
    $stmt->bindParam(":country", $this->country, PDO::PARAM_STR);
    $stmt->bindParam(":postal_code", $this->postal_code, PDO::PARAM_STR);
    $stmt->bindParam(":phone", $this->phone, PDO::PARAM_STR);
    $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
    $stmt->bindParam(":id", $this->id, PDO::PARAM_STR);
    $success = $stmt->execute();
    return $success;
  }
  
  //getters
  public function get_id(){
    return $this->id;
  }
  public function get_last_name(){
    return $this->last_name;
  }
  public function get_first_name(){
    return $this->first_name;
  }
  public function get_address_1(){
    return $this->address_1;
  }
  public function get_address_2(){
    return $this->address_2;
  }
  public function get_city(){
    return $this->city;
  }
  public function get_region(){
    return $this->region;
  }
  public function get_country(){
    return $this->country;
  }
  public function get_postal_code(){
    return $this->postal_code;
  }
  public function get_email(){
    return $this->email;
  }
  public function get_phone(){
    return $this->phone;
  }
}
