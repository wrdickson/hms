<?php

namespace wrdickson\hms;

use \PDO;

Class Reservation{
  // $id int
  private $id;
  // $folio int (customer is retreived from db using joins on folios table and customers table)
  private $folio;
  //  $is_assigned bool
  private $is_assigned;
  //  $space_type_pref int
  private $space_type_pref;
  // $space_id int
  private $space_id;
  // $space_code json array if space_id INTs
  private $space_code;
  // $checkin string, formatted date '2022-03-01'
  private $checkin;
  // $checkout string, formatted date '2024-01-07'
  private $checkout;
  // $people int
  private $people;
  // $beds int
  private $beds;
    // history json
  private $history;
  /**
   * Status:
   * 0 - Checked in/ in house
   * 1 - Checked out/ not in house
   */
  private $status;
// notes json
  private $notes;
  /**
   * the folowing properties are generated at construct, not in reservations table
   */
  private $customer;
  private $customer_first_name;
  private $customer_last_name;

  /**
   * CONSTRUCTOR
   */
  public function __construct($id){
    $pdo = DataConnector::get_connection();
    $sql = "SELECT reservations.id, reservations.folio, reservations.is_assigned, reservations.space_type_pref, 
    reservations.space_id, reservations.space_code, reservations.checkin, reservations.checkout, 
    reservations.people, reservations.beds, reservations.history, reservations.status, reservations.notes, 
    folios.customer, customers.last_name, customers.first_name FROM reservations 
    INNER JOIN folios ON reservations.folio = folios.id 
    INNER JOIN customers ON folios.customer = customers.id 
    WHERE reservations.id = :res_id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":res_id", $id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->folio = $obj->folio;
      $this->is_assigned = $obj->is_assigned;
      $this->space_type_pref = $obj->space_type_pref;
      $this->space_id = $obj->space_id;
      $this->space_code = json_decode($obj->space_code, true);
      $this->checkin = $obj->checkin;
      $this->checkout = $obj->checkout;
      $this->people = $obj->people;
      $this->beds = $obj->beds;
      $this->history = json_decode($obj->history, true);
      $this->status = $obj->status;
      $this->notes = json_decode($obj->notes, true);
      $this->customer = $obj->customer;
      $this->customer_first_name = $obj->first_name;
      $this->customer_last_name = $obj->last_name;
    }
  }

  /**
   * SETTERS
   */
  public function set_folio ( $folio ) {
    $this->folio = $folio;
    return $this->update_to_db();
  }
  public function set_is_assigned ( $is_assigned ) {
    $this->is_assigned = $is_assigned;
    return $this->update_to_db();
  }
  public function set_space_type_pref ( $space_type_pref ) {
    $this->space_type_pref = $space_type_pref;
    return $this->update_to_db();
  }
  public function set_space_id ( $space_id ) {
    //  when we set space id, we also have to generate and set
    //  a new space_code
    //  generate the space code
    $childrenArr = RootSpaces::get_root_space_children( $space_id );
    if( count( $childrenArr ) > 0 ) {
      array_push($childrenArr, $space_id);
      $this->space_code = $childrenArr;
    } else {
      $this->space_code = array( $space_id );
    }
    $this->space_id = $space_id;
    return $this->update_to_db();
  }
  public function set_checkin ( $checkin ) {
    $this->checkin = $checkin;
    return $this->update_to_db();
  }
  public function set_checkout ( $checkout ) {
    $this->checkout = $checkout;
    return $this->update_to_db();
  }
  public function set_people ( $people ) {
    $this->people = $people;
    return $this->update_to_db();
  }
  public function set_beds ( $beds ) {
    $this->beds = $beds;
    return $this->update_to_db();
  }

  // note is a assoc array 
   public function add_note( $note_text, $user_id, $user_name ){
    $noteArr = array();
    $noteArr['date'] = date('Y-m-j H:i:s');
    $noteArr['account_id'] = $user_id;
    $noteArr['account_name'] = $user_name;
    $noteArr['text'] = $note_text;
    array_push($this->notes, $noteArr);
    $updateSuccess = $this->update_to_db();
    return $updateSuccess;
  }

  public function add_history( $history_text, $user_id, $user_name ){
    $historyArr = array();
    $historyArr['date'] = date('Y-m-j H:i:s');
    $historyArr['account_id'] = $user_id;
    $historyArr['account_name'] = $user_name;
    $historyArr['text'] = $history_text;
    array_push($this->history, $historyArr);
    $updateSuccess = $this->update_to_db();
    return $updateSuccess;
  }

  public function checkin(){
    $this->status = 1;
    return $this->update_to_db();
  }

  public function checkout(){
    $this->status = 0;
    return $this->update_to_db();
  }

  /**
   * to_array()
   */  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['folio'] = $this->folio;
    $arr['is_assigned'] = $this->is_assigned;
    $arr['space_type_pref'] = $this->space_type_pref;
    $arr['space_id'] = $this->space_id;
    $arr['space_code'] = $this->space_code;
    $arr['checkin'] = $this->checkin;
    $arr['checkout'] = $this->checkout;
    $arr['people'] = $this->people;
    $arr['beds'] = $this->beds;
    $arr['history'] = $this->history;
    $arr['status'] = $this->status;
    $arr['notes'] = $this->notes;
    $arr['customer'] = $this->customer;
    $arr['customer_first_name'] = $this->customer_first_name;
    $arr['customer_last_name'] = $this->customer_last_name;
    return $arr;
  }

  /**
   * update_to_db() is called after each time we modify a property
   */
  public function update_to_db () {
    $history_json = json_encode($this->history);
    $notes_json = json_encode($this->notes);
    $space_code_json = json_encode($this->space_code);
    $pdo2 = DataConnector::get_connection();
    $stmt = $pdo2->prepare("UPDATE reservations SET is_assigned = :ias, space_type_pref = :stf, space_id = :spid, space_code = :sc, checkin = :ci, checkout = :co, people = :pe, beds = :be, history = :hi, status = :stat, notes = :nt WHERE id = :rid");
    $stmt->bindParam(":ias", $this->is_assigned);
    $stmt->bindParam(":stf", $this->space_type_pref);
    $stmt->bindParam(":spid", $this->space_id);
    $stmt->bindParam(":sc", $space_code_json);
    $stmt->bindParam(":ci", $this->checkin);
    $stmt->bindParam(":co", $this->checkout);
    $stmt->bindParam(":pe", $this->people);
    $stmt->bindParam(":be", $this->beds);
    $stmt->bindParam(":hi", $history_json);
    $stmt->bindParam(":stat", $this->status);
    $stmt->bindParam(":nt", $notes_json);
    $stmt->bindParam(":rid", $this->id);
    $execute = $stmt->execute();
    $error = $stmt->errorInfo(); 
    return $execute;
  }


  //  TODO change this for the new db configurations . . . 
  public static function updateReservation1( $resId, $beds, $checkin, $checkout, $customer, $folio, $people, $space_code, $space_id, $status ){
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE reservations SET space_id = :si, space_code = :sc, checkin = :ci, checkout = :co, people = :pe, beds = :be, folio = :fo, status=:st, customer = :cu WHERE id = :id");
    $stmt->bindParam(":si", $space_id);
    $stmt->bindParam(":fo", $folio);
    $stmt->bindParam(":sc", $space_code);
    $stmt->bindParam(":ci", $checkin);
    $stmt->bindParam(":co", $checkout);
    $stmt->bindParam(":pe", $people);
    $stmt->bindParam(":be", $beds);

    $stmt->bindParam(":st", $status);
    $stmt->bindParam(":cu", $customer);
    $stmt->bindParam(":id", $resId);
    $execute = $stmt->execute();
    $error = $stmt->errorInfo(); 
    return $execute;
  }

  /**
   * GETTERS
   */
  public function get_id () {
    return $this->id;
  }
  public function get_is_assigned () {
    return $this->is_assigned;
  }
  public function get_space_type_pref () {
    return $this->space_type_pref;
  }
  public function get_space_id () {
    return $this->space_id;
  }
  public function get_space_code () {
    return $this->space_code;
  }
  public function get_checkin () {
    return $this->checkin;
  }
  public function get_checkout () {
    return $this->checkout;
  }
  public function get_people () {
    return $this->people;
  }
  public function get_beds () {
    return $this->beds;
  }
  public function get_history () {
    return $this->history;
  }

  public function get_status () {
    return $this->status;
  }
  public function get_notes () {
    return $this->notes;
  }
  //  the following three properties are generated at instantiation
  //  from joins to folios and customers based on reservation.folio
  public function get_customer () {
    return $this->customer;
  }
  public function get_customer_first_name () {
    return $this->customer_first_name;
  }
  public function get_customer_last_name () {
    return $this->customer_last_name;
  }
}
