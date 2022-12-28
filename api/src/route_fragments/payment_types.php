<?php

namespace wrdickson\hms;

$f3->route('POST /payment-types/get-active', function ( $f3 ) {
  $response['active_payment_types'] = PaymentTypes::get_active_payment_types();
  $response['hello'] = 'hello';
  print json_encode($response);
});

