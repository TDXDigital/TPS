<?php
    /*if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }*/
include implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'setup.common.php']);
if(file_exists($xml_path)){
    http_response_code(403);
    $refusal = "<h1>403 Forbidden</h1><p>Your request cannot proceed as the"
            . " this server has already been configured.</p>";
    die($refusal);
}
?>
<?php
    $message = filter_input(INPUT_GET, 'm' , FILTER_SANITIZE_STRING);
    if(isset($message)){
        echo "<div class=\"panel panel-fail\">
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
            $lic_xml = simplexml_load_file(
                    implode(DIRECTORY_SEPARATOR, [$current_directory, "lics.xml"])
                    );
            assert(!is_null($lic_xml), "XML licence files not found");
            $n = 0;
            foreach( $lic_xml->license as $license_file){
                if($n>0){
                    echo "<h3>".$license_file->Segment."</h3>";
                }
                $file = $license_file->file;
                $lic = file_get_contents(implode(DIRECTORY_SEPARATOR, [$current_directory, $file]));
                echo nl2br($lic);
                $n++;
                echo "<br><hr>";
            }
            
            ?>
        </p>
        <div class="input-group">
            <span class="input-group-addon">
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
                ?>/>
            </span>
            <label class="form-control" for="eula"> I have read, and agree to the license(s) terms</label>
      </div><!-- /input-group -->
        
        <br><br>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
        </form>
    </div>
</div>
</form>
    
