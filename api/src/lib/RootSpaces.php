<?php

//  there is a gotcha in get_root_space_children()
//  and get_root_space_parents() where we have
//  to explicitly refer the functions by namespace
//  when executing these recursive functions
//  easy to fix, just do it when changing namespace
namespace wrdickson\hms;

use \PDO;
use \Exception;

Class RootSpaces {

  public static function create_root_space ( $beds, $childOf, $displayOrder, $people, $showChildren, $spaceType, $title, $isActive ) {

    /* 
    *  what we need to do:
    *  load up the all spaces array
    *  modify the space in question with the new data
    *  run getChildren() on everything ie recalculate space_code on every effected reservation
    *  if it works:
    *    save everything off
    *  if it doesnt:
    *    return an error
    */
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO root_spaces (title, child_of, display_order, show_children, space_type, people, beds, is_active, is_unassigned) VALUES (:t, :co, :do, :sc, :st, :p, :b, :ia, 0)");
    $stmt->bindParam(":t", $title);
    $stmt->bindParam(":co", $childOf);
    $stmt->bindParam(":do", $displayOrder);
    $stmt->bindParam(":sc", $showChildren);
    $stmt->bindParam(":st", $spaceType);
    $stmt->bindParam(":p", $people);
    $stmt->bindParam(":b", $beds);

    $stmt->bindParam(":ia", $isActive);
    try{
      $execute = $stmt->execute();
      $id = $pdo->lastInsertId();
    } catch (Exception $ex) {
      return $ex;
    }
    $pdo = null;

    //  TODO make this part of the return, huh?
    //  TODO this is rough and uses processing power and sucks
    //  FIX IT!
    Reservations::update_space_codes();

    return $id;
  }

  public static function delete_root_space ( $root_space_id ) {
    $reesponse = array();
    /* 
    *  what we need to do:  AS A TRANSACTION, please
    *  1. unassign all reservations with this space_code
    *  2. recalculate space codes on ALL reservations
    *  3. actually delete the space
    */
    $pdo = DataConnector::get_connection();

    // 1
    //  first, get the unassigned root space id
    $stmt = $pdo->prepare("SELECT id FROM root_spaces WHERE is_unassigned = 1");
    $response['execute get unassigned rs'] = $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $unassigned_space_id = $result['id'];
    $response['$unassigned_space_id'] = $unassigned_space_id;

    //  second, iterate through all reservatons that have this as space_code
    //  and bump to unassigned after getting space_type_pref

    //  get array of all root spaces
    $root_spaces_arr = RootSpaces::get_root_spaces();
    $response['$root_spaces_arr'] = $root_spaces_arr;

    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE space_id = :si");
    $stmt->bindParam(':si', $root_space_id);
    $stmt->execute();
    $effected_res_arr = array();
    while( $result = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $res = array();
      $res['id'] = $result['id'];
      $res['space_id'] = $result['space_id'];
      array_push($effected_res_arr, $res);
    };
    $response['$effected_res_arr'] = $effected_res_arr;


    //  iterate through effected reservations and reassign
    foreach($effected_res_arr as $effected_res){
      foreach($root_spaces_arr as $root_space){
        if($root_space['id'] == $effected_res['space_id']){
          $iRes = new Reservation($effected_res['id']);
          $iRes->set_is_assigned(0);
          $iRes->set_space_id(0);
          $iRes->set_space_type_pref($root_space['spaceType']);
        }
      }
    }

    $stmt = $pdo->prepare("DELETE FROM root_spaces WHERE id = :rsi");
    $stmt->bindParam(":rsi", $root_space_id);
    $execute_delete = $stmt->execute();
    $response['execute_delete'] = $execute_delete;

    //  TODO make this part of the return, huh?
    //  TODO this is rough and uses processing power and sucks
    //  FIX IT!
    Reservations::update_space_codes();
    $pdo = null;
    return $response;
  }

  public static function get_all_space_ids(){
    $pdo= DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT id FROM root_spaces");
    $stmt->execute();
    $cArr = array();
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)){
      array_push($cArr, $obj->id);
    }
    $pdo = null;
    return $cArr;
  }

  public static function get_root_spaces () {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM root_spaces ORDER BY display_order ASC");
    $execute = $stmt->execute();
    $returnArr = array();
    while ($iObj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $iArr = array();
      $iArr['id'] = $iObj->id;
      $iArr['title'] = $iObj->title;
      $iArr['childOf'] = $iObj->child_of;
      $iArr['displayOrder'] = $iObj->display_order;
      $iArr['showChildren'] = $iObj->show_children;
      $iArr['spaceType'] = $iObj->space_type;
      $iArr['people'] = $iObj->people;
      $iArr['beds'] = $iObj->beds;
      $iArr['isActive'] = $iObj->is_active;
      $iArr['isUnassigned'] = $iObj->is_unassigned;
      array_push($returnArr, $iArr);
    }
    $pdo = null;
    return (array) $returnArr;
  }

  public static function get_root_space_children( $rootSpaceId ){

    //  recursive get_children()
    if( !function_exists('wrdickson\hms\get_children') ){
      function get_children($spaceId, $rootSpaces) {
        $children = [];
        foreach($rootSpaces as $space){
          if ($space['childOf'] == $spaceId) {
            //  recursive:
            $c = get_children($space['id'], $rootSpaces);
            array_push($children, $space['id']);
            $children = array_merge($children, $c);
          }
        }
        return $children;
      }
    }
    //  get the root spaces . . .
    $rootSpaces = RootSpaces::get_root_spaces();
    $children = get_children($rootSpaceId, $rootSpaces);
    return $children;
  }

  public static function get_root_space_parents( $rootSpaceId ) {

    //  recursive getParents()
    if( !function_exists('wrdickson\hms\getParents') ){
      function getParents($rootSpaceId, $rootSpaces) {
        $parents = [];
        foreach($rootSpaces as $rootSpace) {
          if($rootSpace['id'] == $rootSpaceId && $rootSpace['childOf'] > 0 ){
            array_push($parents, $rootSpace['childOf']);
            if($rootSpace['childOf'] > 0 ){
              $p = getParents($rootSpace['childOf'], $rootSpaces);
              $parents = array_merge($parents, $p);
            }
          }
        }
        return $parents;
      }
    }
    //  get the root spaces . . .
    $rootSpaces = RootSpaces::get_root_spaces();
    $parents = getParents($rootSpaceId, $rootSpaces);
    return $parents;
  }

  public static function update_root_space ($id, $title, $childOf, $displayOrder, $showChildren, $spaceType, $people, $beds, $isActive) {
    /* 
    *  what we need to do:
    *  load up the all spaces array
    *  modify the space in question with the new data
    *  run getChildren() on everything ie recalculate space_code on every effected reservation
    *  if it works:
    *    save everything off
    *  if it doesnt:
    *    return an error
    */

    //  instead, we just hammer the update through . . . 
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE root_spaces SET title = :t, display_order = :d, child_of = :c, show_children = :s, space_type = :st, people= :p, beds = :b, is_active = :ia WHERE id = :i");
    $stmt->bindParam(":i", $id);
    $stmt->bindParam(":t", $title);
    $stmt->bindParam(":d", $displayOrder);
    $stmt->bindParam(":c", $childOf);
    $stmt->bindParam(":s", $showChildren);
    $stmt->bindParam(":st", $spaceType);
    $stmt->bindParam(":p", $people);
    $stmt->bindParam(":b", $beds);
    $stmt->bindParam(":ia", $isActive);
    $execute = $stmt->execute();

    //  TODO make this part of the return, huh?
    //  TODO this is rough and uses processing power and sucks
    //  FIX IT!
    Reservations::update_space_codes();
    return $execute;
  }



}
