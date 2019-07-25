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
    * @abstract Gathers information regarding a radio show promo
    * @param $id int ID of the advert
    * @return Associative array The database row for the given client
    */
    public function getPromoInfo($id) {
	$stmt = $this->mysqli->query("SELECT * FROM radio_show_promos WHERE AdId=$id ORDER BY showDay ASC, showStart ASC;");
	$promoShowInfo = [];
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
	    if (count($promoShowInfo) == 0)
	        $promoShowInfo['name'] = $row['showName'];

	    $duration = [ $row['showStart'], $row['showEnd'] ];
	    if (in_array($row['showDay'], array_keys($promoShowInfo)))
		array_push($promoShowInfo[$row['showDay']], $duration);
	    else
		$promoShowInfo[$row['showDay']] = [$duration];
	}
	return $promoShowInfo;
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

    /*
    * @abstract Create a new add
    * @param $adName            str  Name of the ad
    * @param $cat               str  Ad category
    * @param $length            int  Ad length
    * @param $lang              str  Langauge of the ad
    * @param $startDate         str  Ad start date
    * @param $endDate           str  Ad end date
    * @param $active            int  Ad active status (0/1)
    * @param $friend            int  Ad friend status (0/1)
    * @param $clientID          int  Unique ID of the client this ad is for
    * @param $maxPlayCount      int  Maximum number of times the ad can be run
    * @param $maxDailyPlayCount int  Maximum number of times the ad can run in a single day
    * @param $assignedShow      int  The id of the show that this ad should be played on
    * @param $assignedHour      str  The hour when the show should play the ad
    * @param $backingTrack      str  The title of the backing song in the ad
    * @param $backingArtist     str  The artist of the backing song in the ad
    * @param $backingAlbum      str  The album name of the backing song in the ad
    * @param $showName          str  The name of the show being promoted
    * @param $showDayTimes      arr  [ <day#Week> =>[['start' => '9:30', 'end' => '11:30'], ...], ...]. Sunday = 0 day#Week.
    * @return The unique id of the newly-created ad
    */
    public function createNewAd($adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID,
				$maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				$backingArtist, $backingAlbum, $showName, $showDayTimes)
    {
    	$id = -1;
        if($stmt = $this->mysqli->prepare("insert into adverts ("
                . "Category, Length, EndDate, StartDate, AdName,"
                . "Language, Active, Friend, ClientID, maxPlayCount, "
		. "maxDailyPlayCount, assignedShow, assignedHour, "
		. "backing_song, backing_artist, backing_album) values "
                . "( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $stmt->bind_param("sissssiiiiiissss", $cat, $length, $endDate, $startDate, $adName, $lang, $active, 
				$friend, $clientID, $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour,
				$backingTrack, $backingArtist, $backingAlbum);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->log->info(sprintf("New Ad created %d", $id ));
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();

	    // If it's an ad promoting a radio show
	    if ($id > -1 && $showName != NULL && count($showDayTimes) > 0) {
		// Insert the name and date information of the show into the radio_show_promos table
		if ($stmt = $this->mysqli->prepare("INSERT INTO radio_show_promos (AdId, showName, showDay, showStart, showEnd) VALUES (?, ?, ?, ?, ?)")) {
		    foreach ($showDayTimes as $dayNum => $showTimes) {
			foreach ($showTimes as $showTime) {
			    $stmt->bind_param("isiss", $id, $showName, $dayNum, $showTime['start'], $showTime['end']);
			    if (!$stmt->execute())
                		$this->log->error($this->mysqli->errno);
			}
		    }
		}
		$stmt->close();
	    }

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
