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
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO root_spaces (title, child_of, display_order, show_children, space_type, people, beds, is_active) VALUES (:t, :co, :do, :sc, :st, :p, :b, :ia)");
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
    return $id;
  }

  public static function delete_root_space ( $root_space_id ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("DELETE FROM root_spaces WHERE id = :rsi");
    $stmt->bindParam(":rsi", $root_space_id);
    $execute = $stmt->execute();
    $pdo = null;
    return $execute;
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
    *  run getChildren() on everything
    *  if it works:
    *    save everything off
    *  if it doesnt:
    *    return an error
    */

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
    return $execute;
  }

  public function generateSpaceCodes () {

  }

}
