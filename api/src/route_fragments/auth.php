<?php

namespace wrdickson\hms;

/**
 * LOGIN
 * 
 * ftn requires SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, and DB_PASS as DEFINED vars.
 * @param $f3 OBJECT should have a raw json body with properties 'username' and 'password'
 */
$f3->route('POST /login', function( $f3 ) {
  $iAuth = new Auth( SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS);
  $params = json_decode($f3->get('BODY'));
  print json_encode( $iAuth->check_login( $params->username, $params->password ) );
});


/**
 *  AUTHORIZE TOKEN
 * 
 * $perms is not a param, but it:
 * is ALWAYS necessary and
 * is ALWAYS an array with a 'permission' INT property of the account's perm
 * and a 'role' STRING property that might match a string in account's 'roles' JSON array
 * 
 */
$f3->route('POST /authorize-token', function( $f3 ) {
  $perms = ['permission'=> 1, 'role'=>'void'];
  $r = F3Auth::authorize_token( $f3, $perms );
  print json_encode($r);
});


