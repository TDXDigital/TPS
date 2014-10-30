<?php
    /*if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }*/
?>
<?php
    $message = filter_input(INPUT_GET, 'm' , FILTER_SANITIZE_STRING);
    if(isset($message)){
        echo "<div class=\"panel panel-success\">
    <div class=\"panel-heading\">Message Information</div>
        <div class=\"panel-body\">
            <span>$message</span>
        </div>
    </div>";
    }
?>
<form action='setup.vars.php' method="POST" name="lic">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'db';
        }
    ?>'/>
    <input type='hidden' name='q' value='db'/>
<div class="panel panel-primary">
    <div class="panel-heading">Please read the following licenses required for this software.</div>
    <div class="panel-body">
        <p>
            <?php
            // get licences
            $lic_xml = simplexml_load_file("lics.xml");
            $n = 0;
            foreach( $lic_xml->license as $license_file){
                if($n>0){
                    echo "<h3>".$license_file->Segment."</h3>";
                }
                $file = $license_file->file;
                $lic = file_get_contents($file);
                echo nl2br($lic);
                $n++;
                echo "<br><hr>";
            }
            
            ?>
        </p>
        <input id="eula" type="checkbox" required name='eula' <?php
        if(isset($_SESSION['EULA'])){
            switch ($_SESSION['EULA']) {
                case 'on':
                    print " checked disabled ";
                    break;

                default:
                    break;
            }
        }
        ?>/><label for="eula"> I have read, and agree to the license(s) terms</label><br><br>
        <input type="submit" value="Next"/>
        </form>
    </div>
</div>
</form>
    
