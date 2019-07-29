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
	if ($id == NULL)
	    return;
	return $this->mysqli->query("SELECT * FROM clients WHERE ClientNumber=$id;")->fetch_array(MYSQLI_ASSOC);
    }

    /*
    * @author Derek Melchin
    * @abstract Gathers information about all the active radio_show_promos
    * @return Associative array containing info about each radio_show_promo
    *              [<AdId> => ['name' => <showName>, 'Sun' => [['12:00', '14:30'], ...], ...], ...]
    */
    public function getPromos() {
	$stmt = $this->mysqli->query("SELECT * FROM radio_show_promos WHERE AdId IN (SELECT AdId FROM adverts WHERE Active = 1) ORDER BY showName ASC, showDay ASC, showStart ASC;");
	$promos = [];
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC)) {
	    $duration = [ $row['showStart'], $row['showEnd'] ];
	    // If we already have the AdId as a key in $promos
	    if (in_array($row['AdId'], array_keys($promos)))
		// If we already have this row's day
		if (in_array($row['showDay'], array_keys($promos[$row['AdId']])))
		    // Append this row's duration times
		    array_push($promos[$row['AdId']][$row['showDay']], $duration);
		else
		    // Start this day
		    $promos[$row['AdId']][$row['showDay']] = [$duration];
	    else
		// Create a promo element for this AdId
		$promos[$row['AdId']] = ["name" => $row['showName'], $row['showDay'] => [$duration]];

	}
	return $promos;
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
    * @abstract Gathers the PSAs from the database
    * @return Array of adverts rows in the database that are PSA
    */
    public function getPSAs() {
	$stmt = $this->mysqli->query("SELECT * FROM adverts WHERE psa = 1 AND active = 1;");
	$PSAs = [];
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
	    array_push($PSAs, $row);
	return $PSAs;
    }


    /*
    * @author Derek Melchin
    * @abstract Updates the information on a client's record
    * @param $clientID     int  Client ID
    * @param $clientName   str  Client's name
    * @param $company      str  Client's employer
    * @param $contactEmail str  Client contact email
    * @param $creditLimit  dbl  Credit limit of client
    * @param $paymentTerms int  Payment terms for client
    * @param $address      str  Address of client
    * @param $phoneNumber  str  Phone number of client
    * @param $status       int  Status of client. enum('OVL','ACT','EXP','COL','INT','CLO','SUS') 
    */
    public function updateClient($clientID, $clientName, $company, $contactEmail, $phoneNumber=NULL, $creditLimit=5000, 
				 $paymentTerms=1, $address=NULL, $status=7) {
        if($stmt = $this->mysqli->prepare("UPDATE clients SET Name=?, companyName=?, email=?, "
					. "CreditLimit=?, PaymentTerms=?, Address=?, PhoneNumber=?, Status=? "
					. "WHERE ClientNumber=?")) {
            $stmt->bind_param("sssdisssi", $clientName, $company, $contactEmail, $creditLimit, 
			      $paymentTerms, $address, $phoneNumber, $status, $clientID);
            if($stmt->execute())
                $this->log->info(sprintf("Updated Client %d", $clientID ));
            else
                $this->log->error($this->mysqli->errno);
            $stmt->close();
        }
        else{
            $this->log->error("Failed to update client" . $this->mysqli->error);
        }
    }

    /*
    * @author Derek Melchin
    * @abstract Create a new client
    * @param $name         string Name of the company who is advertising
    * @param $company      string Name of the company $name works at
    * @param $email        string Email of the company contact
    * @param $creditLimit  double Credit limit for the client
    * @param $paymentTerms int    Payment terms for this client
    * @param $address      string Address of the client
    * @param $phoneNumber  string Phone number of the client
    * @param $status       int    Status of the client
    * @return int ClientNumber of the newly created client
    */
    public function createClient($name, $company, $email, $creditLimit=5000, $phoneNumber=NULL, $paymentTerms=1, $address=NULL, $status=7) {
	$id = -1;
        if($stmt = $this->mysqli->prepare("INSERT INTO clients (Name, companyName, email, "
					. "CreditLimit, PaymentTerms, Address, PhoneNumber, Status) "
					. "VALUES (?, ?, ?, ?, ?, ?, ?, ?);")) {
            $stmt->bind_param("sssdissi", $name, $company, $email, $creditLimit, 
			      $paymentTerms, $address, $phoneNumber, $status);
            if($stmt->execute()) {
		$id = $this->mysqli->insert_id;
                $this->log->info(sprintf("Created client %d", $id ));
            } else {
                $this->log->error($this->mysqli->errno);
	    }
            $stmt->close();
        }
        else{
            $this->log->error("Failed to create client" . $this->mysqli->error);
        }
	return $id;
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
    * @param $psa               int  1 if the advert is a PSA, 0 otherwise.
    * @return The unique id of the newly-created ad
    */
    public function createNewAd($adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID,
				$maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				$backingArtist, $backingAlbum, $showName, $showDayTimes, $psa)
    {
    	$id = -1;
        if($stmt = $this->mysqli->prepare("insert into adverts ("
                . "Category, Length, EndDate, StartDate, AdName,"
                . "Language, Active, Friend, ClientID, maxPlayCount, "
		. "maxDailyPlayCount, assignedShow, assignedHour, "
		. "backing_song, backing_artist, backing_album, psa) values "
                . "( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
            $stmt->bind_param("sissssiiiiiissssi", $cat, $length, $endDate, $startDate, $adName, $lang, $active, 
				$friend, $clientID, $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour,
				$backingTrack, $backingArtist, $backingAlbum, $psa);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->log->info(sprintf("New Ad created %d", $id ));
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();

	    $this->updateRadioShowPromos($id, $showName, $showDayTimes);
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

    /*
    * @author Derek Melchin
    * @abstract Updates the radio_show_promos tables with the radio show information
    * @param $adID         int  ID of the ad
    * @param $showName     str  Name of the show being promoted 
    * @param $showDayTimes arr  The broadcasting schedule for the show being promoted
    *                           [ <day> => [[<startTime>, <endTime>], ...], ...]. 
    */
    public function updateRadioShowPromos($adID, $showName, $showDayTimes) {
	if ($adID < 0)
	    return;

	// Remove radio_show_promos in database for this $adID
	if ($stmt = $this->mysqli->prepare("DELETE FROM radio_show_promos WHERE AdId=?")) {
	    $stmt->bind_param("i", $adID);
	    if ($stmt->execute())
	        $this->log->info(sprintf("Deleted radio_show_promos where AdId %d", $adID));
	    else 
	        $this->log->error($this->mysqli->errno);
	}

	// If it's an ad promoting a radio show
        if ($showName != NULL && count($showDayTimes) > 0) {

	    // Insert the name and date information of the show into the radio_show_promos table
	    if ($stmt = $this->mysqli->prepare("INSERT INTO radio_show_promos (AdId, showName, showDay, showStart, showEnd) VALUES (?, ?, ?, ?, ?)")) {
	        foreach ($showDayTimes as $day => $showTimes) {
		    foreach ($showTimes as $showTime) {
		        $stmt->bind_param("issss", $adID, $showName, $day, $showTime[0], $showTime[1]);
			if (!$stmt->execute())
                	    $this->log->error($this->mysqli->errno);
		    }
	        }
	    }
	    $stmt->close();
	}
    }

    public function updateAd($adId, $adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID,
			     $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack, 
			     $backingArtist, $backingAlbum, $showName, $showDayTimes, $psa)
    {
    	 if($stmt = $this->mysqli->prepare("UPDATE adverts SET "
                . "Category=?, Length=?, EndDate=?, StartDate =?, AdName=?, "
                . "Language=?, Active=?, Friend=?, ClientID=?, maxPlayCount=?, maxDailyPlayCount=?, assignedShow=?, "
		. "assignedHour=?, backing_song=?, backing_artist=?, backing_album=?, psa=? "
                . "WHERE AdId=?")){
            $stmt->bind_param("sissssiiiiiissssii", $cat, $length, $endDate, $startDate, $adName, $lang, $active, 
				$friend, $clientID, $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, 
				$backingTrack, $backingArtist, $backingAlbum, $psa, $adId);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->log->info(sprintf("Updated Ad %d", $adId ));
            }
            else{
                $this->log->error($this->mysqli->errno);
                $adId = -1;
            }
            $stmt->close();

	    $this->updateRadioShowPromos($adId, $showName, $showDayTimes);
        }
        else{
            $this->log->error("Failed to update Ad"
                    . $this->mysqli->error);
            $adId = -1;
        }
        return $adId;
    }
}
