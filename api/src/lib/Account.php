<?php 

namespace wrdickson\hms;

use \PDO;

class Account {

    private $id;
    private $username;
    private $email;
    private $permission; 
    private $roles;  
    private $registered;
    private $last_login;
    private $last_activity;
    private $is_active;

    public function __construct( $id) {
        $pdo = DataConnector::get_connection();
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = :id");
        $stmt->bindParam(":id",$id,PDO::PARAM_INT);
        $stmt->execute();
        while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
          $this->id = $obj->id;
          $this->username = $obj->username;
          $this->email = $obj->email;
          $this->permission = $obj->permission;
          $this->roles = json_decode($obj->roles, true);
          $this->registered = $obj->registered;
          $this->last_login = $obj->last_login;
          $this->last_activity = $obj->last_activity;
          $this->is_active = (int)$obj->is_active;
        }
    }

    public static function create_account( $username, $password, $permission, $email ){
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      //  initially, we won't set this
      $roles = '[]';
      $is_active = 1;
      $pdo = DataConnector::get_connection();
      $stmt = $pdo->prepare('INSERT INTO accounts (username, email, permission, roles, registered, last_activity, last_login, is_active, password) VALUES (:u, :e, :p, :ro, NOW(), NOW(), NOW(),:ia, :pwd)');
      $stmt->bindParam(':u', $username);
      $stmt->bindParam(':e', $email);
      $stmt->bindParam(':p', $permission);
      $stmt->bindParam(':ro', $roles);
      $stmt->bindParam(':pwd', $password_hash);
      $stmt->bindParam(':ia', $is_active);
      return $stmt->execute();
    }

    public static function get_all_accounts () {
      $response = array();
      $pdo = DataConnector::get_connection();
      $accountsArr = array();
      $stmt = $pdo->prepare('SELECT * FROM accounts');
      $stmt->execute();
      while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['username'] = $obj->username;
        $iArr['email'] = $obj->email;
        $iArr['permission'] = $obj->permission;
        $iArr['roles'] = json_decode($obj->roles, true);
        $iArr['registered'] = $obj->registered;
        $iArr['last_login'] = $obj->last_login;
        $iArr['last_activity'] = $obj->last_activity;
        $iArr['is_active'] = $obj->is_active;
        array_push($accountsArr, $iArr);
      }
      return $accountsArr;
    }

    public function to_array(){
        $arr = array();
        $arr['id'] = $this->id;
        $arr['username'] = $this->username;
        $arr['email'] = $this->email;
        $arr['permission'] = $this->permission;
        $arr['roles'] = $this->roles;
        $arr['registered'] = $this->registered;
        $arr['last_login'] = $this->last_login;
        $arr['last_activity'] = $this->last_activity;
        $arr['is_active'] = $this->is_active;
        return $arr;
    }

    //  this method does NOT return account email or activity
    public function to_array_secure(){
      $arr = array();
      $arr['id'] = $this->id;
      $arr['username'] = $this->username;
      $arr['permission'] = $this->permission;
      $arr['roles'] = $this->roles;
      $arr['is_active'] = $this->is_active;
      return $arr;
  }

    public function set_email( $email ) {
      $xid = $this->get_id();
      $pdo = DataConnector::get_connection();
      $stmt = $pdo->prepare("UPDATE accounts SET email = :e WHERE id = :xid");
      $stmt->bindParam(":e", $email, PDO::PARAM_INT);
      $stmt->bindParam(":xid", $xid, PDO::PARAM_INT);
      $result = $stmt->execute();
      if ($result == true){
          $this->email = $email;
      }
      return $result;
  }

  public function set_is_active( $is_active ) {
    $xid = $this->get_id();
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE accounts SET is_active = :newP WHERE id = :xid");
    $stmt->bindParam(":newP", $is_active, PDO::PARAM_INT);
    $stmt->bindParam(":xid", $xid, PDO::PARAM_INT);
    $result = $stmt->execute();
    if ($result == true){
        $this->is_active = $is_active;
    }
    return $result;
}

  public function set_password( $password ) {
    $xid = $this->get_id();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE accounts SET password = :newP WHERE id = :xid");
    $stmt->bindParam(":newP", $password_hash, PDO::PARAM_STR);
    $stmt->bindParam(":xid", $xid, PDO::PARAM_INT);
    $result = $stmt->execute();
    if ($result == true){
        //don't update . . .  password is NOT in properties
    }
    return $result;
  }

    public function set_permission( $permission ) {
        $xid = $this->get_id();
        $pdo = DataConnector::get_connection();
        $stmt = $pdo->prepare("UPDATE accounts SET permission = :newPerm WHERE id = :xid");
        $stmt->bindParam(":newPerm", $permission, PDO::PARAM_INT);
        $stmt->bindParam(":xid", $xid, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result == true){
            $this->permission = $permission;
        }
        return $result;
    }

    public function set_username( $username ) {
      $xid = $this->get_id();
      $pdo = DataConnector::get_connection();
      $stmt = $pdo->prepare("UPDATE accounts SET username = :u WHERE id = :xid");
      $stmt->bindParam(":u", $username, PDO::PARAM_INT);
      $stmt->bindParam(":xid", $xid, PDO::PARAM_INT);
      $result = $stmt->execute();
      if ($result == true){
          $this->username = $username;
      }
      return $result;
  }
      
    public function update_activity() {
      $pdo = DataConnector::get_connection();
      $stmt = $pdo->prepare("UPDATE accounts SET last_activity = NOW() WHERE id = :i");
      $stmt->bindParam(":i", $this->id,PDO::PARAM_STR);
      $result = $stmt->execute();
      return $result;
    }

    public function update_login() {
      $pdo = DataConnector::get_connection();
      $stmt = $pdo->prepare("UPDATE accounts SET last_login = NOW() WHERE id = :i");
      $stmt->bindParam(":i", $this->id,PDO::PARAM_STR);
      $result = $stmt->execute();
      return $result;
  }

  
  public function get_email() {
      return $this->email;
  }

  public function get_id() {
      return $this->id;
  }

  public function get_is_active () {
    return $this->is_active;
  }

  public function get_registered() {
    return $this->registered;
  }

  public function get_roles() {
    return $this->roles;
  }

  public function get_permission() {
    return $this->permission;
  }

  public function get_username() {
    return $this->username;
  }

}