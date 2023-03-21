<?php

namespace wrdickson\hms;

$f3->route('POST /payment-types/get-active', function ( $f3 ) {
  $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  $response['hello'] = 'hello';
  print json_encode($response);
});

/**
 * GET ALL PAYMENT TYPES
 */
$f3->route('POST /payment-types/get-all', function ( $f3 ) {
  $response['all_payment_types'] = PaymentTypes::get_all_payment_types();
  $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  print json_encode($response);
});

/**
 * CREATE
 */
$f3->route('POST /payment-types/create/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_payment_types' ];
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $account = $f3auth['decoded']->account;
  $response = array();
  $response['account'] = $account;

  $params = json_decode($f3->get('BODY'));
  $response['params'] = $params;

  //  validate
  $options = array(
    'payment_title' => array(
      'is_length, 4, 24',
    ),
    'display_order' => array(
      'is_integer'
    ),
    'is_active' => array(
      'is_0_or_1' 
    )
  );
  $test_values = array(
    'payment_title' => $params->payment_title,
    'display_order' => $params->display_order,
    'is_active' => $params->is_active
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $response['create'] = PaymentTypes::create_payment_type($params->payment_title, $params->is_active, $params->display_order);
    $response['all_payment_types'] = PaymentTypes::get_all_payment_types();
    $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  };

  print json_encode($response);
});

/**
 * UPDATE
 */
$f3->route('POST /payment-types/update/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_payment_types' ];
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
    'payment_title' => array(
      'is_length, 4, 24'
    ),
    'display_order' => array(
      'is_integer'
    ),
    'is_active' => array(
      'is_0_or_1' 
    )
  );
  $test_values = array(
    'id' => $params->id,
    'payment_title' => $params->payment_title,
    'display_order' => $params->display_order,
    'is_active' => $params->is_active
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $response['update'] = PaymentTypes::update_payment_type($params->id, $params->payment_title, $params->is_active, $params->display_order);
    $response['all_payment_types'] = PaymentTypes::get_all_payment_types();
    $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  };

  print json_encode($response);
});

/**
 * DELETE
 */
$f3->route('POST /payment-types/delete/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_payment_types' ];
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
    )
  );
  $test_values = array(
    'id' => $params->id
  );
  $v = new Validate($test_values, $options);
  $validate_result = $v->validate();
  $response['validateResult'] = $validate_result;

  if ( $validate_result['valid'] == true ) {
    $response['delete'] = PaymentTypes::delete_payment_type($params->id);
    $response['all_payment_types'] = PaymentTypes::get_all_payment_types();
    $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  };

  print json_encode($response);
});
