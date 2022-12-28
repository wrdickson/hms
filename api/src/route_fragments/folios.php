<?php

namespace wrdickson\hms;
use \Exception;

$f3->route('POST /folios/get-folio-1', function ( $f3 ) {
  $params = json_decode( $f3->get('BODY'), true );
  $folio_id = $params['folio_id'];
  try {
    $iFolio = new Folio($folio_id);
    $response['folio_to_array'] = $iFolio->to_array();
  } catch (Exception $e ) {

  }
  print json_encode($response);
});
