<?php

namespace wrdickson\hms;
/**
 * Create tax type
 */
$f3->route('POST /tax-types-create/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_tax_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));
  //  validate params
  $options = array (
    'tax_title' => array (
      'is_length, 3, 24'
    ),
    'tax_rate' => array (
      'is_float'
    ),
    'display_order' => array (
      'is_integer', 'is_greater_than_0'
    ),
    'is_current' => array (
      'is_integer', 'is_0_or_1'
    )
  );
  $test_values = array(
    'tax_title'=> $params->tax_title,
    'tax_rate' => $params->tax_rate,
    'display_order' => $params->display_order,
    'is_current' => $params->is_current
  );
  $Validator = new Validate( $test_values, $options );
  $valid = $Validator->validate();
  if($valid['valid'] == true) {
    $response['create'] = TaxTypes::create_tax_type( $params->tax_title, $params->tax_rate, $params->is_current, $params->display_order );
    $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  }
  $response['valid'] = $valid['valid'];
  $response['valid_obj'] = $valid;
  $response['account'] = $account;
  $response['params'] = $params;
  print json_encode($response);
});

/**
 * delete tax type
 */
$f3->route('POST /tax-types-delete/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_tax_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));  
  //  validate params
  $options = array(
    'id' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'id' => $params->id
  );
  $v = new Validate($test_values, $options);
  $response['validate'] = $v->validate();
  if( $response['validate']['valid'] == true ) {
    $response['delete'] = TaxTypes::delete_tax_type( $params->id );
    $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  } else {

  }
  
  $response['account'] = $account;
  $response['params'] = $params;

  print json_encode($response);
});

/**
 * get tax types
 */
$f3->route('POST /tax-types/get-all/', function ( $f3 ) {
  $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  print json_encode($response);
});

/**
 * update tax type
 */
$f3->route('POST /tax-type-update/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_tax_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  //  TODO validate params
  $params = json_decode($f3->get('BODY'));

  $response['w'] = $params->uObj->display_order;

  $response['update'] = TaxTypes::update_tax_type( $params->uObj->id, $params->uObj->tax_title, $params->uObj->tax_rate, $params->uObj->is_current, $params->uObj->display_order );
  $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  $response['account'] = $account;
  $response['params'] = $params;

  print json_encode($response);
});

