<?php

/*
 * The MIT License
 *
 * Copyright 2015 James Oliver.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace TPS;

require_once 'station.php';
/**
 * Genre handles communications with the database
 * for retrieving 
 *
 * @author support@ckxu.com
 */
class user extends station{
    public function __construct($callsign) {
        parent::__construct($callsign);
    }
    
    public function assignPermission($id, $permissions){
    	$plCreate = isset($permissions['plCreate']) ? 1:0;
        $plView = isset($permissions['plView']) ? 1:0;
        $plEdit = isset($permissions['plEdit']) ? 1:0;

        $trCreate = isset($permissions['trCreate']) ? 1:0;
        $trView = isset($permissions['trView']) ? 1:0;
        $trEdit = isset($permissions['trEdit']) ? 1:0;
        
        $memCreate = isset($permissions['memCreate']) ? 1:0;
        $memView = isset($permissions['memView']) ? 1:0;
        $memEdit = isset($permissions['memEdit']) ? 1:0;

        $progCreate = isset($permissions['progCreate']) ? 1:0;
        $progView = isset($permissions['progView']) ? 1:0;
        $progEdit = isset($permissions['progEdit']) ? 1:0;

        $genCreate = isset($permissions['genCreate']) ? 1:0;
        $genView = isset($permissions['genView']) ? 1:0;
        $genEdit = isset($permissions['genEdit']) ? 1:0;

        $libCreate = isset($permissions['libCreate']) ? 1:0;
        $libView = isset($permissions['libView']) ? 1:0;
        $libEdit = isset($permissions['libEdit']) ? 1:0;
        $zero = 0;


        try {
            $stmt = $this->db->prepare("INSERT INTO permissions VALUES "
                    ."(:idpermissions, :access, :Station_Settings_View, :Station_Settings_Edit, :Callsign,"
                    . " :Playsheet_Create, :Playsheet_View, :Playsheet_Edit, "
                    . " :Advert_View, :Advert_Edit, :Advert_Create, :Audit_View, "
                    . " :Member_Create, :Member_View, :Member_Edit, "
                    . " :Program_Create, :Program_View, :Program_Edit, "
                    . " :Genre_View, :Genre_Create, :Genre_Edit, "
                    . " :Library_View, :Library_Edit, :Library_Create)");

            $stmt->bindParam(":idpermissions", $id);
            $stmt->bindParam(":access", $zero);
            $stmt->bindParam(":Station_Settings_View", $zero);
            $stmt->bindParam(":Station_Settings_Edit", $zero);
            $stmt->bindParam(":Callsign", $this->callsign, \PDO::PARAM_STR);

            $stmt->bindParam(":Playsheet_Create", $plCreate);
            $stmt->bindParam(":Playsheet_View", $plView);
            $stmt->bindParam(":Playsheet_Edit", $plEdit);
            
            $stmt->bindParam(":Advert_View", $trView);
            $stmt->bindParam(":Advert_Edit", $trEdit);
            $stmt->bindParam(":Advert_Create", $trCreate);
            $stmt->bindParam(":Audit_View", $libView);
            
            $stmt->bindParam(":Member_Create", $memCreate);
            $stmt->bindParam(":Member_View", $memView);
            $stmt->bindParam(":Member_Edit", $memEdit);
            
            $stmt->bindParam(":Program_Create", $progCreate);
            $stmt->bindParam(":Program_View", $progView);
            $stmt->bindParam(":Program_Edit", $progEdit);
            
            $stmt->bindParam(":Genre_Create", $genCreate);
            $stmt->bindParam(":Genre_View", $genView);
            $stmt->bindParam(":Genre_Edit", $genEdit);
            
            $stmt->bindParam(":Library_Create", $libCreate);
            $stmt->bindParam(":Library_View", $libView);
            $stmt->bindParam(":Library_Edit", $libEdit);
            
            
            $stmt->execute();
            return TRUE;

        } catch (PDOException $exc) {
            $this->db->rollback(); 
            error_log(sprintf("PDO Exception, %s: %s"
                    ,$exc->getMessage(), $exc->getTraceAsString()));
            return FALSE;
        }

    }

    public function getUserInfo($id)
    {
        if ($id == NULL)
            return;
        return $this->mysqli->query("SELECT * FROM members WHERE id=$id;")->fetch_array(MYSQLI_ASSOC);
    }

    public function getPermissions($id)
    {
        if ($id == NULL)
            return;
        return $this->mysqli->query("SELECT * FROM permissions WHERE idpermissions=$id;")->fetch_array(MYSQLI_ASSOC);
    }
}
