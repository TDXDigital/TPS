<?php
    
set_include_path("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR);
include 'public/lib/emergencyAlert.php';

$station = filter_input(INPUT_GET, "station", FILTER_SANITIZE_STRING);
$location = filter_input(INPUT_GET, "location", FILTER_SANITIZE_STRING);

$alerts = new \TPS\emergencyAlert($station);
print $alerts->run();

exit();

function checkFeed($provider, $data, $location, $logo){
    //if(!file_exists($data)){
    //    error_log("Could not locate $data for $provider");
    //    return FALSE;
    //}
    $entries = file_get_contents($data);
    $entries = new SimpleXmlElement($entries);
    if(count($entries)):
        /*echo "<script src='../../js/jquery/js/jquery-2.1.1.min.js'></script>";
        echo "<script src='../../js/jquery/js/jquery-ui-1.11.0.custom.min.js'></script>";*/
        echo "<style>.emergency_logo{
            background-color:white;
        display: inline-block;
        float: left;
        width: 120px;
        /*height: 100px;*/
        max-height: 100px;
        max-width: 100px;
    }
    .emergency_logo img{
        max-height: 100px;
        max-width: 100px;
    }
    .alert_info{
        display: inline-block;
        /*float: left;*/
        padding: 0 0 0 0;
        margin: 0 0 0 0;
        width: 85%;
        height:100%;
        min-height: 100px;
        /*rgin-left:110px;*/
    }
    .alert_info a{
        color: grey;
    }
    </style>";

        //echo "<pre>";print_r($entries);die;
        //alternate way other than registring NameSpace
        //$asin = $asins->xpath("//*[local-name() = 'ASIN']");

        $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
        $result = $entries->xpath("//prefix:entry");
        //echo count($asin);
        //echo "<pre>";print_r($result);die;
        $i_Alert_EM_C=0;
        $Alerts = "";
        if(sizeof($result)>0){
            /*$Alerts .= "<div class='";
            //ui-state-error
            // check for alert level
            if()
            $Alerts .= "'><!--<h2>Alberta Emergency Alert</h2>-->";*/
            foreach ($result as $entry):
                //echo "<pre>";print_r($entry);die;
                if(strpos($entry->summary,$location) !== false){
                    preg_match_all("/(?<=Expires:\s)(.+)(?=\n)/", 
                            $entry->summary, $matches);
                    if(sizeof($matches)>0){
                        try {
                            if(sizeof($matches[0])>0){
                                $expires = new \DateTime($matches);
                                $now = new\DateTime();
                                if($now > $expires);
                                continue;
                            }
                        } catch (Exception $exc) {
                            continue;
                        }

                    }
                    if(strpos($entry->title,'Test') !== FALSE){
                        $Alerts .= "<div class=\"ui-state-highlight\" style=\"background-color:green\">";
                    }
                    elseif(strpos($entry->title,'Information')!==FALSE 
                            || strpos($entry->title,"advisory")!==FALSE
                            || strpos($entry->title,"special weather")!==FALSE
                            || strpos($entry->title,"special weather")!==FALSE
                            || strpos($entry->title,"bulletin météorologique spécial")!==FALSE){
                        $Alerts .= "<div class=\"ui-state-highlight\">";
                    }
                    else{
                        $Alerts .= "<div class=\"ui-state-error\">";
                    }
                    if($i_Alert_EM_C>0){
                        $Alerts .= "<hr/>";
                    }
                    $i_Alert_EM_C++;
                    $Alerts .= "<span class='emergency_logo'><img src='$logo'/></span>";
                    //$dc = $entry->children('urn:oasis:names:tc:emergency:cap:1.1');
                    $Alerts .= "<span class='alert_info'><p><h3><a href=\"".$entry->link->attributes()->href."\" target=\"_blank\">".$entry->title."</a></h3>";
                    //echo $dc->name."<br/>";
                    $Alerts .= $entry->summary."</p>";
                    $Alerts .= "</span>";   
                    $Alerts .= "</div>";
                }
            endforeach;
            if($i_Alert_EM_C>0){
                echo $Alerts;
            }
        }
    endif; 
    return TRUE;
}

$location = filter_input(INPUT_GET, "location", FILTER_SANITIZE_STRING)?:'Description';
foreach ($providers as $name => $data) {
    if(!checkFeed($name, $data['feed'], $location, $data['logo'])){
        http_response_code(404);
    }
}
