<?php

namespace wrdickson\hms;

$f3->route('POST /tax-types/get-all/', function ( $f3 ) {
  $response['all_tax_types'] = TaxTypes::get_all_tax_types();
  print json_encode($response);
});

$f3->route('POST /tax-type-update/', function ( $f3 ) {
  $perms = [ 'permission' => 7, 'role' => 'edit_tax_types' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  //  TODO validate params
  $params = json_decode($f3->get('BODY'));

  $response['w'] = $params->uObj->display_order;

  $response['update'] = TaxTypes::update_tax_type( $params->uObj->id, $params->uObj->tax_title, $params->uObj->tax_rate, $params->uObj->is_current, $params->uObj->display_order );

  $response['account'] = $account;
  $response['params'] = $params;

  print json_encode($response);
});

