<?php 

namespace wrdickson\hms;

use \PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use \PDOException;
use \DateTimeImmutable;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\ExpiredException;

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

    public static function create_account( $username, $password, $permission, $email, $is_active ){
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      //  initially, we won't set this
      $roles = '[]';
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


    public function generate_pwd_reset_token( ) {
      $issuedAt = date_create();
      $expireAt = date_create();
      date_add( $expireAt, date_interval_create_from_date_string("15 minutes") );
      $payload = [
        'iat' => $issuedAt->getTimestamp(),  // Issued at: time when the token was generated,
        //  SERVER_NAME is a constant set in config.php
        'iss' => SERVER_NAME,
        'exp' => $expireAt->getTimeStamp(),
        'exp_f' => date_format($expireAt, 'Y-m-d H:i:s'),  // Formatted expire
        'nbf'  => $issuedAt->getTimestamp(),  // Not before
        'nbf_f' => date_format($issuedAt, 'Y-m-d H:i:s'),  // Formatted expire
        'account' => $this->to_array_secure()
      ];
      $token = JWT::encode($payload, JWT_KEY, 'HS256');
      return $token;
    }

    public static function get_all_accounts () {
      $response = array();
      $pdo = DataConnector::get_connection();
      $accountsArr = array();
      $stmt = $pdo->prepare('SELECT * FROM accounts ORDER BY username ASC');
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

    public function send_reset_link ( $site_name ) {
      $uri_base = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      $response['uri_base'] = $uri_base;
      $token = $this->generate_pwd_reset_token();
      $link_uri = $uri_base . $token;
      $mail = new PHPMailer(true);
      try {
        //  Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;          // Enable verbose debug output
        $mail->SMTPAuth   = false;                      // Enable SMTP authentication
        //Recipients
        $mail->setFrom('cahunis@trekbill.com', 'Mailer');
        $mail->addAddress( $this->email );       // Name is optional
        $mail->addReplyTo('webmaster@trekbill.com');
        
        // Content
        $mail->isHTML(true);                           // Set email format to HTML
        $mail->Subject = 'Password reset from ' . $site_name;
        $mail->Body = '<h2>A request has been sent from ' . $site_name . ' to reset your password.</h2>';
        $mail->Body .= '<p>Follow the link below to reset your password:</p>';
        $mail->Body .= '<p>Link expires in 15 minutes.</p>';
        //$mail->Body.= '<p>' . $uri_base . '</p>';
        //$mail->Body.= '<p>' . $token . '</p>';
        //$mail->Body.= '<p>' . $link_uri . '</p>';
        $mail->Body.= '<a href="' . $link_uri . '">Change password</a>';


        //  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        return true;
      } catch (Exception $e) {
          return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
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

    public function update_basic( $username, $email, $permission, $is_active ) {
      $success = true;
      if( $this->username != $username)  {
        if(!$this->set_username($username)){
          $success = false;
        }
      }
      if( $this->email != $email)  {
        if(!$this->set_email($email)){
          $success = false;
        }
      }
      if( $this->permission != $permission)  {
        if(!$this->set_permission($permission)){
          $success = false;
        }
      }
      if( $this->is_active != $is_active)  {
        if(!$this->set_is_active($is_active)){
          $success = false;
        }
      }
      return $success;
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