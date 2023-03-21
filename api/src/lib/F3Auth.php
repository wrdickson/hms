<?php

namespace wrdickson\hms;

Class F3Auth {
  /**
   * authorize a token provided as 'Jwt' in header and return the user or throw an error
   * 
   * @param $f3 object the Fat-Free-Framework base instance
   * @param $perm_required array an array with 'permission' and 'role' as members
   * 
   * @return array 'auth' array with an 'account' array containing user info
   * @return ERROR will throw an request error: 401, 402, 403, 500 if token fails
   */
  public static function authorize_token ( $f3, $perm_required ) {
    //  throws a 500 error if 'Jwt' is not in headers
    $token = $f3['HEADERS']['Jwt'];
    //
    $iAuth = new Auth( SERVER_NAME, JWT_KEY, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $auth = $iAuth->authenticate( $perm_required, $token );
    //  throw an error if authenticate() fails OR if response code > 399
    if( $auth && $auth['status'] == 200 ) {
      //  authenticate passed . . . 
      return $auth;
    } else {
      if( $auth['status'] ) {
        $f3->error( $auth['status'] );
      } else {
        $f3->error('500');
      }
    }
  }

}
