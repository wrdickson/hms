<?php

namespace wrdickson\hms;

$f3->route('GET /hello', function ( $f3 ) {
  $start = '2022-12-26';
  $end = '2022-12-27';
  $space_id = 15;
  $res_id = 23;
  print json_encode(Reservations::check_availability_by_dates_ignore_res( $start, $end, $res_id));
});

$f3->route('POST /create-reservation', function ( $f3 ) {
  $p = array( 
    'permission' => 1,
    'role' => 'wtf'
  );
  $a = F3Auth::authorize_token($f3, $p);
  //  get the user account
  $account_id = $a['decoded']->account->id;
  $p = json_decode($f3->get('BODY'), true);

  $response = array();
  $response['a'] = $a;
  $response['p'] = $p;

  $customer = $p['customer'];
  $checkin = $p['checkin'];
  $checkout = $p['checkout'];
  $space_id = $p['space_id'];
  $beds = $p['beds'];
  $people = $p['people'];

  
  $response['c'] = Reservations::create_reservation( $account_id, $customer, $checkin, $checkout, $space_id, $beds, $people);

  print json_encode($response);
});


