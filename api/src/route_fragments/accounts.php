<?php

namespace wrdickson\hms;

use \PDO;
use \Exception;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\SignatureInvalidException;
use \Firebase\JWT\BeforeValidException;
use \Firebase\JWT\ExpiredException;

/**
 * GET ALL
 */

 $f3->route('POST /accounts/get-all/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_accounts' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $response = array();
  $response['account'] = $account;

  $response['all_accounts'] = Account::get_all_accounts();


  print json_encode($response);
 });

/**
 * CREATE
 */
$f3->route('POST /accounts/create', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_accounts' ];
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  

  $account = $f3auth['decoded']->account;
  $response = array();
  $response['account'] = $account;

  $params = json_decode($f3->get('BODY'));
  $response['params'] = $params;

  //  validate
  $options = array(
    'username' => array(
      'is_length, 4, 24'
    ),
    'email' => array(
      'is_email'
    ),
    'permission' => array(
      'is_integer'
    ),
    'is_active' => array(
      'is_0_or_1' 
    )
  );
  $test_values = array(
    'username' => $params->username,
    'email' => $params->email,
    'permission' => $params->permission,
    'is_active' => $params->is_active
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $response['create'] = Account::create_account($params->username, $params->password, $params->permission, $params->email, $params->is_active);
    $response['all_accounts'] = Account::get_all_accounts();
  };

  print json_encode($response);
});

/**
 * DISPLAY UPDATE REQUEST FROM LINK
 */
$f3->route('GET /accounts/reset-link-request/@token', function ( $f3 ) {
  try {
    $token = $f3->get('PARAMS.token');
    //$site_name = $f3->get('PARAMS.site-name');
    $uri_base = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $e = explode('/', $uri_base);
    array_pop($e);
    array_pop($e);
    $f = implode('/', $e);
    $f .= '/reset-password/';
    //print $f . '<br/>';

    $decoded = JWT::decode( $token, new Key( JWT_KEY, 'HS256') );
    //print "<PRE>"; 
    //print_r($decoded);
    //echo "</PRE>";
    //echo "<h1>" . $site_name . " password reset:</h1>";
    echo "<form method='post' action='$f'>";
    echo "<input type='hidden' id='token' name='token' value=$token />";
    echo "<label for='pwd1'>Password</label><br/>";
    echo "<input type='password' id='pwd1' name='pwd1'/><br/>";
    echo "<label for='pwd2'>Again:</pwd2><br/>";
    echo "<input type='password' id='pwd2' name='pwd2'/><br/>";
    echo "<input type='submit' value='Go'>";
    echo "</form>";
    
  } catch (  Exception $e ) {
    print "There was an error: " . $e->getMessage();
  }
});

/**
 * REQUEST UPDATE LINK
 */
$f3->route('POST /accounts/reset-link-request', function ( $f3 ) {
  $params = json_decode($f3->get('BODY'), true);
  $response['params'] = $params;
  $test_username = $params['username'];
  $site_name = $params['siteName'];
  $response = array();
  if($params['username']) {

    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE username = :username");
    $stmt->bindParam( ":username", $test_username );
    $stmt->execute();
    $account_email = null;
    $is_active = false;
    $id = null;

    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $account_email = $obj->email;
      $is_active = $obj->is_active;
      $id = $obj->id;
    }
    if( $id && $is_active ) {
      $account = new Account($id);
      $response['account'] = $account->to_array();
      $response['email_sent'] = $account->send_reset_link($site_name);
      $response['message'] = 'account found';
    } elseif ( $id ){
      $response['message'] = 'Inactive account';
    } else {
      $response['message'] = 'Account not found';
    }
  } 
  print json_encode($response);
});

/**
 * RESET PASSWORD FROM EMAIL LINK
 */
$f3->route('POST /accounts/reset-password/', function ($f3) {
  $token = $_POST['token'];
  $pwd1 = $_POST['pwd1'];
  $pwd2 = $_POST['pwd2'];
  if( $token && $pwd1 && $pwd2) {
    if( $pwd1 == $pwd2 ) {
      // validate 
      $options = array(
        'pwd1' => array(
          'is_alphanum_dash_star_underscore',
          'is_length, 4, 24'
        )
      );
      $test_values = array(
        'pwd1' => $pwd1
      );
      $v = new Validate($test_values, $options);
      $validate_result = $v->validate();
      if( $validate_result['valid'] ) {
        //  good to go
        //  authorize token
        try {
          $decoded = JWT::decode( $token, new Key( JWT_KEY, 'HS256') );
          //echo '<PRE>' . print_r($decoded) . '</PRE>';
          //echo '<hr>' . 'id: ' . $decoded->account->id . '<hr/>';
          $iAccount = new Account($decoded->account->id);
          $pwd_set = $iAccount->set_password( $pwd1);
          if( $pwd_set ) {
            echo '<h4>Password reset.<h4>';
            echo '<p>Return to the app and login.</p>';
          } else {
            echo 'Error.  Password NOT reset.';
          }
        } catch ( Exception $e ){
          echo $e->getMessage();
        }
      } else {
        //  print the errors
        foreach ( $validate_result['errors'] as $error ) {
          echo 'Error: <br/>';
          echo $error . '<br/>';
        }
      }
    } else {
      echo "Passwords do not match.";
    }
  } else {
    echo "There was an error.";
  }
});

/**
 * UPDATE ACCOUNT (NOT PASSWORD)
 */
$f3->route('POST /accounts/update/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_accounts' ];
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  

  $account = $f3auth['decoded']->account;
  $response = array();
  $response['account'] = $account;

  $params = json_decode($f3->get('BODY'));
  $response['params'] = $params;

  //  validate
  $options = array(
    'username' => array(
      'is_length, 4, 24'
    ),
    'email' => array(
      'is_email'
    ),
    'permission' => array(
      'is_integer'
    ),
    'is_active' => array(
      'is_0_or_1' 
    )
  );
  $test_values = array(
    'username' => $params->username,
    'email' => $params->email,
    'permission' => $params->permission,
    'is_active' => $params->is_active
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $iAccount = new Account($params->id);
    $response['iAccount'] = $iAccount->to_array();
    $response['update'] = $iAccount->update_basic($params->username, $params->email, $params->permission, $params->is_active);
    $response['all_accounts'] = Account::get_all_accounts();
  };

  print json_encode($response);
});

/**
 * UPDATE ACCOUNT PASSWORD
 */
$f3->route('POST /accounts/update-password/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_accounts' ];
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  

  $account = $f3auth['decoded']->account;
  $response = array();
  $response['account'] = $account;

  $params = json_decode($f3->get('BODY'));
  $response['params'] = $params;

  //  validate
  $options = array(
    'id' => array(
      'is_integer'
    ),
    'pwd' => array(
      'is_alphanum_dash_star_underscore',
      'is_length, 4, 24'
    )
  );
  $test_values = array(
    'id' => $params->id,
    'pwd' => $params->pwd
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $iAccount = new Account($params->id);
    $response['iAccount'] = $iAccount->to_array();
    $response['update'] = $iAccount->set_password($params->pwd);
    $response['all_accounts'] = Account::get_all_accounts();
  };

  print json_encode($response);
});
