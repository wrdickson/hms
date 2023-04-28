<?php

namespace wrdickson\hms;
use \PDO;
use \Exception;
/**
 * Period Sales Report
 */
$f3->route('POST /reports/period-sales-report', function ( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  $response = array();
  $params = json_decode($f3->get('BODY'), true );
  $response['params'] = $params;
  $start = $params['start'];
  $end = $params['end'];
  try {
    $response['sales_report'] = Reports::generate_period_sales_report( $start, $end );
  } catch (Exception $e ) {
    throw new Exception( $e->getMessage() );
    $response['error'] = $e->getMessage();
  }
  print json_encode($response);
});

/**
 * Shift Report
 */
$f3->route('POST /reports/shift-report', function ( $f3 ) {
  $perms = [ 'permission' => 3, 'role' => 'post_payment' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  //$response['auth'] = $f3auth;

  // get account id form auth
  $account_id = $f3auth['decoded']->account->id;
  //$response['account_id'] = $account_id;
  
  $params = json_decode($f3->get('BODY'), true );
  //$response['params'] = $params;
  $params = json_decode( $f3->get('BODY'), true );
  $start = $params['start'];
  $end = $params['end'];
  try {
    //$response['start'] = $start;
    //$response['end'] = $end;
    $response['shift_report'] = Reports::generate_shift_report( $start, $end, $account_id );
    $response['all_payment_types'] = PaymentTypes::get_all_payment_types();
  } catch (Exception $e ) {
    $response['error'] = $e;
  }
  print json_encode($response);
});
