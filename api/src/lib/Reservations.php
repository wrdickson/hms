<?php

namespace wrdickson\hms;

use \PDO;
use \Exception;

Class Reservations {

public static function check_availability_by_dates( $start, $end ) {
  $response = array();
  $pdo = DataConnector::get_connection();
  //first, get all reservations that conflict with those dates
  $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkin < :end AND checkout > :start");
  $stmt->bindParam(":start", $start, PDO::PARAM_STR);
  $stmt->bindParam(":end", $end, PDO::PARAM_STR);
  $stmt->execute();
  //second, get all space_id's that are booked for those dates ($rArr)
  $rArr = array();
  while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
    $tArr = json_decode($obj->space_code, true);
    foreach( $tArr as $iterate){
      if(! in_array($iterate, $rArr)) {
        array_push( $rArr, $iterate );
      }
    }
  }
  $response['rArr'] = $rArr;
  //third, get an array of all space_id's
  $allSpaceIds = RootSpaces::get_all_space_ids();
  $response['allspaceids'] = $allSpaceIds;
  //fourth, get only those from all space_ids that are
  //NOT in the array of booked id's
  $availableSpaceIds = array_diff($allSpaceIds, $rArr);

  //  So far, we are failing to catch the situation where
  //  a child item reserved should block the parent
  //  get all root spaces with children
  
  //  iterate through available space_id's 
  //  1. generate children for each one
  //  2. if one of the children is in a reservation space code, remove it 
  foreach( $availableSpaceIds as $index => $spaceId ) {
    //  run the recursive function to get the space's children
    $children = RootSpaces::get_root_space_children($spaceId);
    //  iterate through the children
    foreach($children as $childSpaceId){
      //  compare to the array we made above to include all space codes in res
      if ( in_array( $childSpaceId, $rArr) ){
        //  unset
        unset($availableSpaceIds[$index]);
      }
    }
  }
  $response['availableSpaceIds'] = $availableSpaceIds;
  return $response;
}

public static function check_availability_by_dates_ignore_res( $start, $end, $res_id ) {
  $response = array();
  $pdo = DataConnector::get_connection();
  //first, get all reservations that conflict with those dates
  $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkin < :end AND checkout > :start AND id != :id");
  $stmt->bindParam(":start", $start, PDO::PARAM_STR);
  $stmt->bindParam(":end", $end, PDO::PARAM_STR);
  $stmt->bindParam(":id", $res_id, PDO::PARAM_INT);
  $stmt->execute();
  //second, get all space_id's that are booked for those dates ($rArr)
  $rArr = array();
  while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
    $tArr = json_decode($obj->space_code, true);
    foreach( $tArr as $iterate){
      if(! in_array($iterate, $rArr)) {
        array_push( $rArr, $iterate );
      }
    }
  }
  $response['rArr'] = $rArr;
  //third, get an array of all space_id's
  $allSpaceIds = RootSpaces::get_all_space_ids();
  $response['allspaceids'] = $allSpaceIds;
  //fourth, get only those from all space_ids that are
  //NOT in the array of booked id's
  $availableSpaceIds = array_diff($allSpaceIds, $rArr);

  //  So far, we are failing to catch the situation where
  //  a child item reserved should block the parent
  //  get all root spaces with children
  
  //  iterate through available space_id's 
  //  1. generate children for each one
  //  2. if one of the children is in a reservation space code, remove it 
  foreach( $availableSpaceIds as $index => $spaceId ) {
    //  run the recursive function to get the space's children
    $children = RootSpaces::get_root_space_children($spaceId);
    //  iterate through the children
    foreach($children as $childSpaceId){
      //  compare to the array we made above to include all space codes in res
      if ( in_array( $childSpaceId, $rArr) ){
        //  unset
        unset($availableSpaceIds[$index]);
      }
    }
  }
  return $availableSpaceIds;
}


public static function check_conflicts( $start, $end, $space_id ) {
    $pdo = DataConnector::get_connection();
    //works, note the comparators are "<" and ">", not "<=" and ">=" because
    //we do allow overlap in sense that one person can checkout on the same
    //day someone checks in
    //  https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE JSON_CONTAINS( space_code, :spaceId ) > 0 AND ( :start < checkout AND :end > checkin )");
    $stmt->bindParam(":start", $start, PDO::PARAM_STR);
    $stmt->bindParam(":end", $end, PDO::PARAM_STR);
    //  this must be a string for JSON_CONTAINS
    $stmt->bindParam(":spaceId", $space_id, PDO::PARAM_STR);
    $success = $stmt->execute();
    $pdoError = $pdo->errorInfo();
    $response['success'] = $success;
    $rArr = array();
    // TODO ? handle the case where the space_id doesn't exist
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        array_push($rArr, $iArr);
    };
    $response['hits'] = $rArr;
    //return $rArr;
    if(sizeOf($response['hits']) > 0){
        return false;
    } else {
        return true;
    };
  }

  public static function check_conflicts_ignore_res( $start, $end, $space_id, $res_id ) {
    $pdo = DataConnector::get_connection();
    //works, note the comparators are "<" and ">", not "<=" and ">=" because
    //we do allow overlap in sense that one person can checkout on the same
    //day someone checks in
    //  https://stackoverflow.com/questions/325933/determine-whether-two-date-ranges-overlap
    $stmt = $pdo->prepare("SELECT * FROM `reservations` WHERE JSON_CONTAINS( space_code, :spaceId ) > 0 AND ( :start < `checkout` AND :end > `checkin`  ) AND id != :id");
    $stmt->bindParam(":start", $start, PDO::PARAM_STR);
    $stmt->bindParam(":end", $end, PDO::PARAM_STR);
    //  spaceid must be a string for JSON_CONTAINS
    $stmt->bindParam(":spaceId", $space_id, PDO::PARAM_STR);
    $stmt->bindParam(":id", $res_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    $pdoError = $pdo->errorInfo();
    $response['success'] = $success;
    $rArr = array();
    // TODO ? handle the case where the space_id doesn't exist
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        array_push($rArr, $iArr);
    };
    $response['hits'] = $rArr;
    //return $rArr;
    if(sizeOf($response['hits']) > 0){
        return false;
    } else {
        return true;
    };
  }

  /**
   *  Create Reservation
   */
  public static function create_reservation( $account_id, $customer, $checkin, $checkout, $space_id, $beds, $people ){ 
    $response = array();
    $new_res_id = 0;
    //  TODO make damn sure there is not a comflict

    //  start the transaction
    $pdo = DataConnector::get_connection();
    $pdo->beginTransaction();

    //  get the user(account)
    try{
      $i_account = new Account($account_id);
      $account_username = $i_account->get_username();
    } catch ( Exception $e ) {
      $pdo->rollBack();
    }
    //  generate the space code
    try {
      $childrenArr = RootSpaces::get_root_space_children( $space_id );
      if( count( $childrenArr ) > 0 ) {
        array_push($childrenArr, (int)$space_id);
        $space_code = $childrenArr;
      } else {
        $space_code = array( (int)$space_id );
      }
      $response['space_code'] = $space_code;
      $space_code_json = json_encode($space_code);
    } catch (Exception $e) {
      $pdo->rollBack();
    }

    //  create the folio
    try {
      $new_folio_id = Folios::create_folio( $customer );
      $response['new_folio_id'] = $new_folio_id;
      //  returns the id of the new folio, which is all we need
      $i_folio = new Folio($new_folio_id);
      $response['new_folio_id'] = $i_folio;
    } catch (Exception $e ) {
      $pdo->rollBack();
    }

    //  get space type pref
    try {
      $i_root_space = new RootSpace($space_id);
      $space_type_pref = $i_root_space->get_space_type();
      $response['space_type_pref'] = $space_type_pref;
    } catch ( Exception $e ) {
      $pdo->rollBack();
    }

    try {
      //  add to db
      $stmt = $pdo->prepare("INSERT INTO reservations (folio, is_assigned, space_type_pref, space_id, space_code, checkin, checkout, people, beds, history, `status`, notes) VALUES (:fol, '1', :stf, :si, :sc, :ci, :co, :ppl, :bds, '[]', '0', '[]')");
      $stmt->bindParam(":fol", $new_folio_id);
      $stmt->bindParam(":stf", $space_type_pref);
      $stmt->bindParam(":si", $space_id);
      $stmt->bindParam(":sc", $space_code_json);
      $stmt->bindParam(":ci", $checkin);
      $stmt->bindParam(":co", $checkout);
      $stmt->bindParam(":ppl", $people);
      $stmt->bindParam(":bds", $beds);
      $execute = $stmt->execute();
      $new_res_id = $pdo->lastInsertId();
      $response['execute_res_sql'] = $execute;
      $response['new_res_id'] = $new_res_id;
      // COMMIT
      $pdo->commit();
    } catch ( Exception $e ) {
      $response['add_res_error'] = $e;
      $pdo->rollBack();
    }

    //  add history
    //  we can not do this until the transaction has committed
    //  since reservation->add_history requires an instantiated reservation
    try {
      $h_res = new Reservation( $new_res_id );
      $response['nri'] = $new_res_id;
      $response['h_res'] = $h_res->to_array();
      $response['add_history'] = $h_res->add_history( 'Reservation created', $account_id, $account_username );
    } catch (Exception $e) {
      $response['add_history'] = false;
    }
    
    //  instantiate the new reservation
    $final_res = new Reservation( $new_res_id );
    $response['new_res'] = $final_res->to_array();
    //  return
    return $response;
  }

  public static function get_reservations_date_range( $startDate, $endDate ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE checkout >= :start AND checkin <= :end");
    $stmt->bindParam(':start', $startDate);
    $stmt->bindParam(':end', $endDate);
    $stmt->execute();
    $arr= array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $cust = new Customer($obj->customer);
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['space_id'] = $obj->space_id;
        $iArr['space_code'] = $obj->space_code;
        $iArr['checkin'] = $obj->checkin;
        $iArr['checkout'] = $obj->checkout;
        $iArr['customer'] = $obj->customer;
        $iArr['customer_obj'] = $cust->to_array();
        $iArr['people'] = $obj->people;
        $iArr['beds'] = $obj->beds;
        $iArr['folio'] = $obj->folio;
        $iArr['status'] = $obj->status;
        $iArr['history'] = json_decode($obj->history, true);
        $iArr['notes'] = json_decode($obj->notes, true);
        array_push($arr, $iArr);
    };
    return $arr;
  }

}
