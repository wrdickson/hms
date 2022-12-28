<?php

namespace wrdickson\hms;

$f3->route('POST /sale-type-groups/get-all/', function ( $f3 ) {
  $response['all_sale_type_groups'] = SaleTypeGroups::get_all_sale_type_groups();
  print json_encode($response);
});
