<?php
    
    /*include "../functions.php";
    sec_session_start();*/
    //sec_session_start();
    /*
    <div class="ui-state-error">
    <h2>Alberta Emergency Alert</h2>
    <hr/>
    <span class="emergency_logo">
        <img src="/TPS/images/AEMA.png"/>
    </span>
    <span class="alert_info">
            <p>
                <strong>Information Alert - Water Supply - Mar 12, 2014 at 03:09PM</strong>
            </p>
            <p>This is an Alberta Emergency Alert. City of Lethbridge has issued a Water Supply Alert. This alert is in effect for: Parts of Lethbridge County. As of 4 p.m. today City of Lethbridge water will no longer be drinkable and there is a Boil Water Order in place.  This includes Lethbridge, Picture Butte, Coaldale, Coalhurst and County of Lethbridge.
 
We are at risk of not having enough water available for fire suppression,so please cooperate and do not hoard water.
 
Please tell your neighbours, friends and family. If you are in the affected area: Boil water - bring  water to a rapid rolling boil for one minute prior to use. Do not consume. Follow the directions of local authorities. Limit use for emergencies only. Take all necessary precautions. For details visit www.emergencyalert.alberta.ca or stay tuned to local media.
            </p>
    </span>
    <br/>
    <hr/>
    <span class="emergency_logo"><img src="/TPS/images/AEMA.png"/></span>
    <span class="alert_info">
        <div>
            <p>
               <strong>Information Alert - Civil Emergency - Mar 12, 2014 at 11:25AM
               </strong>
            </p>
        </div>
        <div>
            <p>This is an Alberta Emergency Alert. City of Lethbridge has issued a Civil Emergency Alert. This alert is in effect for: Parts of Lethbridge County. The City of Lethbridge is in a local state of emergency regarding critically low levels of potable water. Conditions of the river have worsened late this morning impacting our ability to produce potable water. This has created an emergent situation and we are requiring that all users stop all water consumption until further notice. If you are in the affected area: Limit water consumption to drinking and cooking only. Please avoid hoarding water as this will deplete current levels. For details visit www.emergencyalert.alberta.ca or stay tuned to local media
            </p>
        </div>
    </span>
</div>*/
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
    echo "<div class='ui-state-error'><!--<h2>Alberta Emergency Alert</h2>-->";
    //echo "<pre>";print_r($entries);die;
    //alternate way other than registring NameSpace
    //$asin = $asins->xpath("//*[local-name() = 'ASIN']");

    $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
    $result = $entries->xpath("//prefix:entry");
    //echo count($asin);
    //echo "<pre>";print_r($result);die;
    $i_Alert_EM_C=0;
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
endif;
?>