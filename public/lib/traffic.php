<?php
namespace TPS;

require_once 'ssp.class.php';
require_once 'station.php';

class traffic extends station{

    public function __construct($callsign=null){
        parent::__construct($callsign);
    }
    


    public function createNewAd($advertiser, $cat, $length, $lang, $startDate, $endDate, $active, $friend)
    {
    	$id = -1;
        if($stmt = $this->mysqli->prepare("insert into adverts ("
                . "Category, Length, EndDate, StartDate, AdName,"
                . "Language, Active, Friend) values "
                . "( ?, ?, ?, ?, ?, ?, ?, ?)")){
            $stmt->bind_param("sissssii", $cat, $length, $endDate, $startDate, $advertiser, $lang, $active, $friend);
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
