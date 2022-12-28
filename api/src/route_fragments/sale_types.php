<?php

namespace wrdickson\hms;

$f3->route('POST /sale-types/get-all/', function ( $f3 ) {
  $response['all_sale_types'] = SaleTypes::get_all_sale_types();
  print json_encode($response);
});
