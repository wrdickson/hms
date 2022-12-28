<?php

namespace wrdickson\hms;

use \Exception;

/**
 *  Create reservation
 */
$f3->route('POST /reservations/', function ( $f3 ) {
  $perms = ['permission' => 2, 'role' => 'create_reservation' ];
  $f3auth = F3Auth::authorize_token( $f3, $perms);

  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'), true);

  $response['create'] = Reservations::create_reservation($account->id, $params['customer']['id'], $params['checkin'], $params['checkout'], $params['space_id'], $params['beds'], $params['people'] );

  $response['cid'] = $params['customer']['id'];
  $response['account'] = $account;
  $response['params'] = $params;
  print json_encode($response);
  
});

/**
 *  Check availability by dates
 */
$f3->route('POST /reservations/availability', function ( $f3 ) {
  $perms = [ 'permission' => 0, 'role' => 'get_availability' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));

  $response['account'] = $account;
  $response['params'] = $params;
  $response['availability'] = Reservations::check_availability_by_dates( $params->startDate, $params->endDate );
  print json_encode($response);
});

/**
 * Check conflicts
 */
$f3->route('POST /reservations/conflicts', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'check_conflicts' ];
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $params = json_decode($f3->get('BODY'));

  $start = $params->startDate;
  $end = $params->endDate;
  $space_id = $params->spaceId;

  $response['f3auth'] = $f3auth;
  $response['checkConflicts'] = Reservations::check_conflicts( $start, $end, $space_id );
  print json_encode( $response );
});

/**
 *  Get reservations by date range
 */
$f3->route('POST /reservations/range', function( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));

  //  TODO validate dates??

  $response['account'] = $account;
  $response['params'] = $params;
  $response['reservations'] = Reservations::get_reservations_date_range($params->startDate, $params->endDate);
  print json_encode($response);
});

/**
 *  Get reservations by date range IGNORING A RESERVATION
 */
$f3->route('POST /reservations/range-ignore-res', function( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));
  
  //  TODO validate dates??

  $response['account'] = $account;
  $response['params'] = $params;
  $response['availableSpaceIds'] = Reservations::check_availability_by_dates_ignore_res($params->start_date, $params->end_date, $params->res_id);
  print json_encode($response);
});

/**
 *  MODIFY RESERVATION 1
 */
$f3->route('POST /reservations/update/', function ( $f3 ) {

  $perms = [ 'permission' => 3, 'role' => 'modify_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));
  $resObj = $params->res_obj;

  $response['auth'] = $f3auth;
  $response['params'] = $params;

  //  TODO validate params
  
  //  create local param copies
  $checkin = $resObj->checkin;
  $checkout = $resObj->checkout;
  $customer_id = $resObj->customer;
  $people = $resObj->people;
  $beds = $resObj->beds;
  $space_id = $resObj->space_id;
  $res_id = $resObj->res_id;

  $iRes = new Reservation($res_id);
  $response['rid'] = $res_id;
  $iResArray = $iRes->to_array();
  $response['origResArray'] = $iResArray;

  $response['error'] = array();
  $response['success'] = true;

  //  go through the params and see if the properties have changed and
  //  do the updates. we are only handling changes of checkin, checkout, customer, 
  //  beds, people & space_id with this function
  //  beds
  //  TODO this really should be done as a transaction that can be
  //  unwound if there is an error

  //  we don't need to verify date change IF they are also trying to change space_id . . .
  if( $checkin != $iRes->get_checkin() && $space_id == $iRes->get_space_id() ) {
    //  make sure this reservation is available
    $is_available = Reservations::check_conflicts_ignore_res( $checkin, $iRes->get_checkout(), $space_id, $iRes->get_id() );
    $response['ci change available'] = $is_available;
    if( $is_available ) {
      $response['set_checkin'] = $iRes->set_checkin( $checkin );
    } else {
      array_push( $response['error'], 'checkin change violates availability' );
      $response['success'] = false;
    }
  }

  //  we don't need to verify date change IF they are also trying to change space_id . . .
  if( $checkout != $iRes->get_checkout() && $space_id == $iRes->get_space_id() ) {
    //  make sure this reservation is available
    $is_available = Reservations::check_conflicts_ignore_res( $iRes->get_checkin(), $checkout, $space_id, $iRes->get_id() );
    $response['co change available'] = $is_available;
    if( $is_available ) {
      $response['set_checkout'] = $iRes->set_checkout( $checkout );
    } else {
      array_push( $response['error'], 'checkout change violates availability' );
      $response['success'] = false;
    }
  }
  if( $customer_id != $iRes->get_customer() ) {
    $response['set_customer'] = $iRes->set_customer( $customer_id );
  }
  if( $beds != $iRes->get_beds() ){
    $response['set_beds'] = $iRes->set_beds( $beds );
  }
  if( $people != $iRes->get_people() ) {
    $response['set_people'] = $iRes->set_people( $people );
  }
  if( $space_id != $iRes->get_space_id() ) {
    //  if we change the space_id, we also have to change space_code
    //  BUT the setter handles this 
    // make sure this reservation is available
    $is_available = Reservations::check_conflicts_ignore_res( $checkin, $checkout, $space_id, $iRes->get_id() );
    $response['space_id change available'] = $is_available;
    if( $is_available ) {
      $response['set_space_id'] = $iRes->set_space_id( $space_id );
    } else {
      array_push( $response['error'], 'space_id change not available' );
      $response['success'] = false;
    }
  }
  // no matter what happened, return the res as it is now
  $jRes = new Reservation($res_id);
  $response['current_res'] = $jRes->to_array();
  print json_encode($response);
});


/**
 *  Reservation checkin
 */
$f3->route('POST /reservations/checkin', function( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'manipulate_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

    //  TODO validate params

  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));

  $res_id = $params->res_id;
  $iRes = new Reservation($res_id);

  $response['account'] = $account;
  $response['res_id'] = $res_id;
  $response['reservation_before'] = $iRes->to_array();
  $response['checkin'] = $iRes->checkin();
  $response['add_history'] = $iRes->add_history("Checked in", $account->id, $account->username);
  $response['reservation_after_checkin'] = $iRes->to_array();

  print json_encode($response);
});

/**
 *  Reservation checkout
 */
$f3->route('POST /reservations/checkout', function( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'manipulate_reservations' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );

    //  TODO validate params

  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));

  $res_id = $params->res_id;
  $iRes = new Reservation($res_id);

  $response['account'] = $account;
  $response['res_id'] = $res_id;
  $response['reservation_before'] = $iRes->to_array();
  $response['checkout'] = $iRes->checkout();
  $response['add_history'] = $iRes->add_history("Checked out", $account->id, $account->username);
  $response['reservation_after_checkout'] = $iRes->to_array();
  

  print json_encode($response);
});


