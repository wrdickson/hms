<?php

namespace wrdickson\hms;
/**
 * GET ALL
 */
$f3->route('POST /sale-type-groups/get-all/', function ( $f3 ) {
  $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  print json_encode($response);
});

/**
 * CREATE
 */
$f3->route('POST /sale-type-group-create', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_sale_type_groups' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY')); 
  $response = array();
  $response['params'] = $params;
  $response['account'] = $account;
  $response['create'] = false;

  //  validate
  $options = array(
    'is_active' => array(
      'is_0_or_1'
    ),
    'title' => array(
      'is_length, 4, 24' 
    )
  );
  $test_values = array(
    'is_active' => $params->is_active,
    'title' => $params->title
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    $response['create'] = SaleTypeGroups::create_sale_type_group( $params->title, $params->is_active, $params->display_order);
    $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  };


  print json_encode($response);
});

/**
 * UPDATE
 */
$f3->route('POST /sale-type-group-update', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_sale_type_groups' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY')); 
  $response = array();

  //  validate
  $options = array(
    'id' => array(
      'is_integer'
    ),
    'is_active' => array(
      'is_0_or_1'
    ),
    'title' => array(
      'is_length, 2, 24' 
    )
  );
  $test_values = array(
    'id' => $params->id,
    'is_active' => $params->is_active,
    'title' => $params->title
  );

  $v = new Validate($test_values, $options);

  $response['validate'] = $v->validate();
  if( $response['validate']['valid'] == true ) {
    $response['update'] = SaleTypeGroups::update_sale_type_group($params->id, $params->title, $params->display_order, $params->is_active);
    $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  } else {
    $response['update'] = false;
  }

  $response['params'] = $params;
  $response['account'] = $account;

  print json_encode($response);
});

/**
 * DELETE
 */
$f3->route('POST /sale-type-group-delete/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_sale_type_groups' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY')); 
  $response = array();


  //  validate
  $options = array(
    'sale_type_group_id' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'sale_type_group_id' => $params->sale_type_group_id
  );
  $v = new Validate($test_values, $options);

  $response['validate'] = $v->validate();
  if( $response['validate']['valid'] == true ) {
    $response['delete'] = SaleTypeGroups::delete_sale_type_group( $params->sale_type_group_id );
    $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  } else {
    $response['delete'] = false;
    $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  }


  $response['params'] = $params;
  $response['account'] = $account;
  print json_encode($response);
});
