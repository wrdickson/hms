<?php

namespace wrdickson\hms;

use \PDO;

Class RootSpace {

  private $id;
  private $space_type;
  private $title;
  private $child_of;
  private $show_children;
  private $people;
  private $beds;
  private $display_order;
  private $is_active;

  public function __construct($root_space_id){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM root_spaces WHERE id = :id");
    $stmt->bindParam(":id", $root_space_id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->space_type = $obj->space_type;
      $this->title = $obj->title;
      $this->child_of = $obj->child_of;
      $this->show_children = $obj->show_children;
      $this->people = $obj->people;
      $this->beds = $obj->beds;
      $this->display_order = $obj->display_order;
      $this->is_active = $obj->is_active;
    }
    $pdo = null;
  }

  public function update_subspaces () {
    //  calculate children . . . 
    //$children = RootSpaces::get_root_space_children($this->id);
    //return $children;
  }

  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['space_type'] = $this->space_type;
    $arr['title'] = $this->title;
    $arr['child_of'] = $this->child_of;
    $arr['show_children'] = $this->show_children;
    $arr['people'] = $this->people;
    $arr['beds'] = $this->beds;
    $arr['display_order'] = $this->display_order;
    $arr['is_active'] = $this->is_active;
    return $arr;
  }

  /**
   * GETTERS
   */
  public function get_space_type () {
    return $this->space_type;
  }

}
