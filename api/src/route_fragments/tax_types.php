<?php

namespace wrdickson\hms;

$f3->route('POST /tax-types/get-all/', function ( $f3 ) {
  $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  print json_encode($response);
});

