<?php
    
    /*include "../functions.php";
    sec_session_start();*/
    //sec_session_start();
$data = "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom";
$entries = file_get_contents($data);
$entries = new SimpleXmlElement($entries);
if(count($entries)):
    echo "<script src='../../js/jquery/js/jquery-2.0.3.min.js'></script>";
    echo "<script src='../../js/jquery/js/jquery-ui-1.10.0.custom.min.js'></script>";
    echo "<style>.emergency_logo{
        background-color:white;
    display: inline-block;
    float: left;
    width: 120px;
    /*height: 100px;*/
}
.alert_info{
    display: inline-block;
    /*float: left;*/
    padding: 0 0 0 0;
    margin: 0 0 0 0;
    width: 85%;
    height:100%;
    min-height: 126px;
    /*rgin-left:110px;*/
}</style>";
    
    //echo "<pre>";print_r($entries);die;
    //alternate way other than registring NameSpace
    //$asin = $asins->xpath("//*[local-name() = 'ASIN']");

    $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
    $result = $entries->xpath("//prefix:entry");
    //echo count($asin);
    //echo "<pre>";print_r($result);die;
    $i_Alert_EM_C=0;
    if(sizeof($result)>0){
        echo "<div class='ui-state-error'><!--<h2>Alberta Emergency Alert</h2>-->";
        foreach ($result as $entry):
            //echo "<pre>";print_r($entry);die;
            if(strpos($entry->summary,'Lethbridge') !== false){
                if($i_Alert_EM_C>0){
                    echo "<hr/>";
                }
                $i_Alert_EM_C++;
                echo "<span class='emergency_logo'><img style='margin: 10px 0px 10px 10px;' src='/TPS/images/AEMA.png'/></span>";
                //$dc = $entry->children('urn:oasis:names:tc:emergency:cap:1.1');
                echo "<span class='alert_info'><p><strong>".$entry->title."</strong><br/>";
                //echo $dc->name."<br/>";
                echo $entry->summary."</p>";
                echo "</span>";   
            }
        endforeach;
        echo "</div>";
    }
endif;
?>