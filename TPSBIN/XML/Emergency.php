<?php
$data = "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom";
$entries = file_get_contents($data);
$entries = new SimpleXmlElement($entries);
if(count($entries)):
    echo "<div style='background-color:#f20; color:#fff'>
    <h2>Alberta Emergency Alert</h2><hr>";
    //echo "<pre>";print_r($entries);die;
    //alternate way other than registring NameSpace
    //$asin = $asins->xpath("//*[local-name() = 'ASIN']");

    $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
    $result = $entries->xpath("//prefix:entry");
    //echo count($asin);
    //echo "<pre>";print_r($result);die;
    foreach ($result as $entry):
        //echo "<pre>";print_r($entry);die;
        if(strpos($entry->summary,'Lethbridge') !== false){
            //echo "<div style='display: inline-block;'><img src='/TPS/images/AEMA.png'></div>";
            $dc = $entry->children('urn:oasis:names:tc:emergency:cap:1.1');
            echo "<div style='display: inline-block;'><strong>".$entry->title."</strong></div>";
            //echo $dc->name."<br/>";
            echo "<div style='display: inline-block;'>".$entry->summary."</div>";
            echo "<hr>";   
        }
    endforeach;
    echo "</div>";
endif;
?>