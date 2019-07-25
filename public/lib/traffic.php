<?php
namespace TPS;

require_once 'ssp.class.php';
require_once 'station.php';

class traffic extends station{

    public function __construct($callsign=null){
        parent::__construct($callsign);
    }
    
    /*
    * @author Derek Melchin
    * @abstract Fetch all of the names of our clients
    * @return Associative array [ClientNumber => Name, ...]
    */
    public function getClientsNames() {
	$stmt = $this->mysqli->query("SELECT ClientNumber, Name FROM clients");
	$clients = [];
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
	    $clients[$row['ClientNumber']] = $row['Name'];
	return $clients;
    }

    /*
    * @author Derek Melchin
    * @abstract Gathers information about a given client
    * @return Associative array The database row for the given client
    */
    public function getClientByID($id) {
	return $this->mysqli->query("SELECT * FROM clients WHERE ClientNumber=$id;")->fetch_array(MYSQLI_ASSOC);
    }

    /*
    * @author Derek Melchin
    * @abstract Create a new client
    * @param $name         string Name of the company who is advertising
    * @param $contactName  string Name of the company contact
    * @param $email        string Email of the company contact
    * @param $creditLimit  double Credit limit for the client
    * @param $paymentTerms int    Payment terms for this client
    * @param $address      string Address of the client
    * @param $phoneNumber  string Phone number of the client
    * @param $status       string Status of the client
    * @return int ClientNumber of the newly created client
    */
    public function createClient($name, $contactName, $email, $creditLimit=NULL, $paymentTerms=NULL, $address=NULL, $phoneNumber=NULL, $status=NULL) {
	$columns = "Name, ContactName, email";
	if ($creditLimit != NULL)
	    $columns .= ", CreditLimit";
	if ($paymentTerms != NULL)
	    $columns .= ", PaymentTerms";
	if ($address != NULL)
	    $columns .= ", Address";
	if ($phoneNumber != NULL)
	    $columns .= ", PhoneNumber";
	if ($status != NULL)
	    $columns .= ", Status";

	$values = "'$name', '$contactName', '$email'";
	if ($creditLimit != NULL)
	    $values .= ", $creditLimit";
	if ($paymentTerms != NULL)
	    $values .= ", $paymentTerms";
	if ($address != NULL)
	    $values .= ", '$address'";
	if ($phoneNumber != NULL)
	    $values .= ", '$phoneNumber'";
	if ($status != NULL)
	    $values .= ", $status";

	$this->mysqli->query("INSERT INTO clients ($columns) VALUES ($values);");
	return $this->mysqli->insert_id;
    }

    public function createNewAd($adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID)
    {
    	$id = -1;
        if($stmt = $this->mysqli->prepare("insert into adverts ("
                . "Category, Length, EndDate, StartDate, AdName,"
                . "Language, Active, Friend, ClientID) values "
                . "( ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $stmt->bind_param("sissssiii", $cat, $length, $endDate, $startDate, $adName, $lang, $active, $friend, $clientID);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->log->info(sprintf("New Ad created %d", $id ));
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();
        }
        else{
            $this->log->error("Failed to create new Ad"
                    . $this->mysqli->error);
        }
        return $id;
    }

     public function displayTable($filter)
    {
        
        $where = '';
        $table = 'adverts';
         
        // Table's primary key
        $primaryKey = 'AdId';         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'AdId',   'dt' => 'AdId' ),
            array( 'db' => 'Category', 'dt' => 'Category' ),
            array( 'db' => 'Length', 'dt' => 'Length' ),
            array( 'db' => 'EndDate', 'dt' => 'EndDate' ),
            array( 'db' => 'StartDate', 'dt' => 'StartDate' ),
            array( 'db' => 'Playcount', 'dt' => 'Playcount' ),
            array( 'db' => 'AdName', 'dt' => 'AdName' ),
            array( 'db' => 'Active', 'dt' => 'Active' ),
            array( 'db' => 'Friend', 'dt' => 'Friend' ),

        );

        $prog_data = \SSP::complex($_GET, $this->db, $table, $primaryKey, $columns, null, $where);

        // foreach($prog_data['data'] as &$program) {
        //     $program['host'] = $this->getDjByProgramName($program['programname']);
        // }
        return json_encode($prog_data);
    }

    public function get($id)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM adverts where AdId = ?");
        $param = array($id);

        $stmt->bind_param("i", ...$param);
        $stmt->execute();
        $result = $stmt->get_result();

        $ad = [];
        while($row = $result->fetch_assoc()){
            $ad = $row;
        }
        $stmt->free_result();
        $stmt->close();

        return $ad;

    }

    public function updateAd($adId, $advertiser, $cat, $length, $lang, $startDate, $endDate, $active, $friend)
    {
    	 if($stmt = $this->mysqli->prepare("UPDATE adverts SET "
                . "Category=?, Length=?, EndDate=?, StartDate =?, AdName=?, "
                . "Language=?, Active=?, Friend=? "
                . "WHERE AdId=?")){
            $stmt->bind_param("sissssiii", $cat, $length, $endDate, $startDate, $advertiser, $lang, $active, $friend,$adId);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->log->info(sprintf("Updated Ad %d", $adId ));
            }
            else{
                $this->log->error($this->mysqli->errno);
                $adId = -1;
            }
            $stmt->close();
        }
        else{
            $this->log->error("Failed to update Ad"
                    . $this->mysqli->error);
            $adId = -1;
        }
        return $adId;
    }
}
