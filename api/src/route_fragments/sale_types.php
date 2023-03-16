<?php

namespace wrdickson\hms;

$f3->route('POST /sale-types/get-all/', function ( $f3 ) {
  $response['all_sale_types'] = SaleTypes::get_all_sale_types();
  print json_encode($response);
});

/** 
 * CREATE
 */
$f3->route('POST /sale-types/create/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_sale_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY')); 
  $response = array();
  $response['params'] = $params;
  $response['account'] = $account;

  //  validate
  //  TODO validate tax_types,
  //  TODO ensure salae_type_group exists, same for tax_types array
  $options = array(
    'is_active' => array(
      'is_0_or_1'
    ),
    'is_fixed_price' => array(
      'is_float'
    ),
    'sale_type_group' => array(
      'is_integer'
    ),
    'title' => array(
      'is_length, 4, 24' 
    ),
    'display_order' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'is_active' => $params->is_active,
    'is_fixed_price' => $params->is_fixed_price,
    'sale_type_group' => $params->sale_type_group,
    'title' => $params->title,
    'display_order' =>$params->display_order
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    $response['create'] = SaleTypes::create_sale_type( $params->title, $params->tax_types, $params->sale_type_group, $params->is_fixed_price, $params->fixed_price, $params->is_active, $params->display_order);
    $response['all_sale_types'] = SaleTypes::get_all_sale_types();
  };

  print json_encode( $response );
});

/**
 * UPDATE
 */
$f3->route('POST /sale-types/update/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_sale_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY')); 
  $response = array();
  $response['params'] = $params;
  $response['account'] = $account;

  //  validate
  //  TODO validate tax_types,
  //  TODO ensure salae_type_group exists, same for tax_types array
  $options = array(
    'is_active' => array(
      'is_0_or_1'
    ),
    'is_fixed_price' => array(
      'is_float'
    ),
    'sale_type_group' => array(
      'is_integer'
    ),
    'title' => array(
      'is_length, 4, 24' 
    ),
    'display_order' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'is_active' => $params->is_active,
    'is_fixed_price' => $params->is_fixed_price,
    'sale_type_group' => $params->sale_type_group,
    'title' => $params->title,
    'display_order' =>$params->display_order
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    $response['update'] = SaleTypes::update_sale_type($params->id, $params->title, $params->tax_types, $params->sale_type_group, $params->is_fixed_price, $params->fixed_price, $params->is_active, $params->display_order);
    $response['all_sale_types'] = SaleTypes::get_all_sale_types();
  };


  print json_encode($response);
});
