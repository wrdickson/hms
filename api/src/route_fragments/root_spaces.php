<?php

namespace wrdickson\hms;

//  GET ROOT SPACES
$f3->route('POST /root-spaces', function ( $f3 ) {
  $perms = [ 'permission' => 1, 'role' => 'get_root_spaces' ];
  //  the request should have 'Jwt' property in header with user's token
  //  this throws an error if the token doesn't work OR user doesn't have permission
  $f3auth = F3Auth::authorize_token( $f3, $perms );
  
  $account = $f3auth['decoded']->account;
  $params = json_decode($f3->get('BODY'));
  
  $root_spaces_pre_children = RootSpaces::get_root_spaces();
  $root_spaces_children_parents = array();
  foreach( $root_spaces_pre_children as $rspc ) {
    $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
    $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
    array_push($root_spaces_children_parents, $rspc);
  }
  
  $response['account'] = $account;
  $response['params'] = $params;
  $response['root_spaces_pre_children'] = $root_spaces_pre_children;
  $response['root_spaces_children_parents'] = $root_spaces_children_parents;
  print json_encode($response);
});

//  UPDATE ROOT SPACE
$f3->route('POST /root-spaces/update', function ( $f3 ) {
  print 'gotit';
});