<?php

namespace wrdickson\hms;
/**
 * GET
 */
$f3->route('GET /space-types', function ($f3) {
  print json_encode(SpaceTypes::get_space_types());
});

/**
 * CREATE
 */
$f3->route('POST /space-type-create', function ( $f3 ) {
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
    ),
    'display_order' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'is_active' => $params->is_active,
    'title' => $params->title,
    'display_order' =>$params->display_order
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    $response['create'] = SpaceTypes::create_space_type( $params->title, $params->is_active, $params->display_order);
  };

  print json_encode($response);
});

/**
 * UPDATE
 */
$f3->route('POST /space-type-update', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_space_types' ];
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
    ),
    'display_order' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'is_active' => $params->is_active,
    'title' => $params->title,
    'display_order' =>$params->display_order
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    
    $response['update'] = SpaceTypes::update_space_type($params->id, $params->title, $params->display_order, $params->is_active);
    $response['all_space_types'] = SpaceTypes::get_space_types();
  };

  print json_encode($response);
});

/**
 * DELETE
 */
$f3->route('POST /space-type-delete', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_space_types' ];
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
    'space_type_id' => array(
      'is_integer'
    )
  );
  $test_values = array(
    'space_type_id' => $params->space_type_id
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;


  if ( $validate_result['valid'] == true ) {
    $response['delete'] = SpaceTypes::delete_space_type( $params->space_type_id );
    $response['all_space_types'] = SpaceTypes::get_space_types();
  };

  print json_encode($response);
});