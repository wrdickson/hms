<?php

namespace wrdickson\hms;

$f3->route('GET /foo', function ( $f3) {
  print 'bar';
});

$f3->route('GET /hello', function ( $f3 ) {
  $start = '2022-12-26';
  $end = '2022-12-27';
  $space_id = 15;
  $res_id = 23;
  print json_encode(Reservations::check_availability_by_dates_ignore_res( $start, $end, $res_id));
});




