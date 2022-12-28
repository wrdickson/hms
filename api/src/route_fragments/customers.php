<?php

namespace wrdickson\hms;

/** 
 * CREATE CUSTOMER
 * This is hamburger bullshit, we are only creating based on 
 * firstname ,lastname, phone, and email
 * TODO fix this shit
 */
$f3->route('POST /customers/', function ( $f3 ) {
  $perms = [ 'permission' => 2, 'role' => 'create_customer' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $params = json_decode($f3->get('BODY'));
  $email = $params->email;
  $firstName = $params->firstName;
  $lastName = $params->lastName;
  $email = $params->email;
  $phone = $params->phone;

  //  TODO validate inputs

  $response = array();
  $response['params'] = $params;
  $response['createCustomer'] = Customers::create_customer( $lastName, $firstName, '', '', '', '', '', '', 
  $phone, $email);
  if( $response['createCustomer'] && $response['createCustomer'] > 0 ) {
    $new_customer = new Customer($response['createCustomer']);
    $response['newCustomer'] = $new_customer->to_array();
  }
  
  print json_encode($response);


});

//  Search Customers
$f3->route('POST /customers/search', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'search_customers' ];
  //  the request should have 'jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $params = json_decode($f3->get('BODY'));
  $last_name = $params->lastName;
  $first_name = $params->firstName;
  $offset = $params->offset;
  $limit = $params->limit;

  print json_encode(Customers::search_customers( $last_name, $first_name, $offset, $limit ));
});