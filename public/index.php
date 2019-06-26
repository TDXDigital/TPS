<?php
//if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
// Set variables

/**
 * The standardResult provides a quick method to format SLIM responses
 * @example <br/>
 * standardRequest::error($app, "Descriptive Error")
 * @version 1.0
 * @since 2016-02-15
 * @package TPS
 * @license https://opensource.org/licenses/MIT MIT
 * @author James Oliver <support@ckxu.com>
 */
class standardResult{

    /**
     * return an expected format for the standardRequest
     * @param type $app
     * @param type $data
     * @param type $code
     * @param type $key
     * @param type $isJSON
     * @return type
     */
    static private function encode($app, $data, $code, $key, $isJSON){
        $app->response->setStatus($code);
        if($isJSON){
            $app->response->headers->set('Content-Type', 'application/json');
            if(!is_null($key)){
                return json_encode(array($key=>$data));
            }
            return json_encode($data);
        }
    }

    /**
     * print error response 500 (if not prevented by 404)
     *
     * @author James Oliver <support@ckxu.com>
     * @version 1.0
     * @since 2016-02-15
     * @license https://opensource.org/licenses/MIT MIT
     *
     * @param object $app Slim Application
     * @param string $data message for user / response
     * @param string $key key of message, uses message as default
     * @param int $code httpResponseCode value
     * @param bool $isJSON Boolean to disable setting content type
     */
    static public function error($app, $data=NULL, $key="message", $code=500,
            $isJSON=True){
        $str = static::encode($app, $data, $code, $key, $isJSON);
        if(!$isJSON){
            $app->error(new \Exception($str), $code);
        }
    }

    /**
     * print error 400
     *
     * @author James Oliver <support@ckxu.com>
     * @version 1.0
     * @since 2016-02-15
     * @license https://opensource.org/licenses/MIT MIT
     *
     * @param object $app Slim Application
     * @param string $data message for user / response
     * @param string $key key of message, uses message as default
     * @param int $code httpResponseCode value
     * @param bool $isJSON Boolean to disable setting content type
     */
    static public function badRequest($app, $data=NULL, $key="message",
            $code=400, $isJSON=True){
        $str = static::encode($app, $data, $code, $key, $isJSON);
        if(!$isJSON){
            $app->error(new \Exception($str), $code);
        }
        else{
            return $str;
        }
    }

    /**
     * response 202
     *
     * @author James Oliver <support@ckxu.com>
     * @version 1.0
     * @since 2016-02-15
     * @license https://opensource.org/licenses/MIT MIT
     *
     * @param object $app Slim Application
     * @param string $data message for user / response
     * @param string $key key of message, uses message as default
     * @param int $code httpResponseCode value
     * @param bool $isJSON Boolean to disable setting content type
     */
    static public function accepted($app, $data=NULL, $key="message", $code=202,
            $isJSON=True){
        print static::encode($app, $data, $code, $key, $isJSON);
    }

    /**
     * response 201
     *
     * @author James Oliver <support@ckxu.com>
     * @version 1.0
     * @since 2016-02-15
     * @license https://opensource.org/licenses/MIT MIT
     *
     * @param object $app Slim Application
     * @param string $data message for user / response
     * @param string $key key of message, uses message as default
     * @param int $code httpResponseCode value
     * @param bool $isJSON Boolean to disable setting content type
     */
    static public function created($app, $data=NULL, $key="message", $code=201,
            $isJSON=True){
        print static::encode($app, $data, $code, $key, $isJSON);
    }

    /**
     * response 200
     *
     * @author James Oliver <support@ckxu.com>
     * @version 1.0
     * @since 2016-02-15
     * @license https://opensource.org/licenses/MIT MIT
     *
     * @param object $app Slim Application
     * @param string $data message for user / response
     * @param string $key key of message, uses message as default
     * @param int $code httpResponseCode value
     * @param bool $isJSON Boolean to disable setting content type
     */
    static public function ok($app, $data=NULL, $key="message", $code=200,
            $isJSON=True){
        print static::encode($app, $data, $code, $key, $isJSON);
    }
}

$debug = False;
$basepath = dirname(__DIR__).DIRECTORY_SEPARATOR;
$autoload_path = $basepath."vendor".DIRECTORY_SEPARATOR."autoload.php";
$twig_path = $basepath."lib".DIRECTORY_SEPARATOR."Twig".DIRECTORY_SEPARATOR
        ."Autoloader.php";
$slim_path = $basepath."lib".DIRECTORY_SEPARATOR."Slim".DIRECTORY_SEPARATOR
        ."Slim.php";

// $twig_path = $basepath."vendor".DIRECTORY_SEPARATOR."twig".DIRECTORY_SEPARATOR."twig".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.
//         "Twig".DIRECTORY_SEPARATOR."Autoloader.php";
// $slim_path = $basepath."vendor".DIRECTORY_SEPARATOR."slim".DIRECTORY_SEPARATOR."slim".DIRECTORY_SEPARATOR."Slim".DIRECTORY_SEPARATOR
//         ."Slim.php";

$views_path = $basepath."Views";
$temp_path = false;
$sessionExpiry = "30minutes";
$sessionName = "TPSSlimSession";
$sessionSecret =
        "Q7^nY{Zd'UO]Z`=L8X&`fV)Fn(LwH(vFwAm-y[z,YJD*vJj'WVYNC!+R3\cnF3I";

if(!file_exists("TPSBIN".DIRECTORY_SEPARATOR."XML".
        DIRECTORY_SEPARATOR."DBSETTINGS.xml")){
    header('Location: /Setup/');
    exit();
}

require_once 'header.php';

if($debug){
    $GLOBALS['logLevel'] = "debug";
}

/**
 * performs authentication (Not Authorization) for TPS
 * $app is the current application to be used
 * $access is a value greater than zero (public) corresponding to the security
 * clearance level required, an array can be provided if multiple levels are
 * to have access
 * $json sets the output to a JSON format instead of the standard HTML output
 */
$authenticate = function ($app,$access=0,$json=FALSE) {
    return function () use ($app,$access,$json) {
        $log = new \TPS\logger(NULL,NULL,NULL,NULL,$_SERVER['REMOTE_ADDR']);
        if (!isset($_SESSION['access'])) {
            $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, Login required","redirect",
                    $_SERVER['REMOTE_ADDR']);
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
        elseif($access){
            if(!is_array($access)){
                $log->debug("user accessed ".$app->request()->getPathInfo(),
                        'pass',$_SERVER['REMOTE_ADDR']);
                $access = array($access);
            }
            if(!in_array($_SESSION['access'],$access)){
                $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, Not Authorized",401,
                    $_SERVER['REMOTE_ADDR']);
                $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
                //$app->flash('error', 'Insufficient Permissions');
                //$app->redirect('/401');
                $app->response->setStatus(401);
                global $base_url, $twig;
                $params = array(
                    'base_url' => $app->request->getResourceUri(),
                    'title' => 'Error 401',
                    'message' => "Not Authorized",
                );
                if($json){
                    print "<h1>".$params['title']."</h1>".$params['message'];
                }
                else{
                    $app->render("error.html.twig",$params);
                }
                $app->stop(401);
            }
        }
    };
};

$requiresHttps = function () use ($app,$debug) {
    $log = new \TPS\logger(NULL,NULL,NULL,NULL,$app->request()->getIp());
    if ($app->environment['slim.url_scheme'] !== 'https' and !$debug) {
        $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, HTTPS Required","redirect",
                    $_SERVER['REMOTE_ADDR']);
        $app->redirect('/requiressl');    // or render response and $app->stop();
     }
};

$app->hook('slim.before.dispatch', function() use ($app) {
   $user = null;
   #$callsign = filter_input(INPUT_SESSION, "CALLSIGN")?:NULL;
   try{
       $callsign = $_SESSION['CALLSIGN'];
   } catch (Exception $ex) {
       $callsign = NULL;
   }
   if (isset($_SESSION['fname'])) {
      $user = $_SESSION['fname'];
   }
   $uid = null;
   if (isset($_SESSION['account'])) {
      $uid = $_SESSION['account'];
   }
   $access = null;
   if (isset($_SESSION['access'])) {
      $access = $_SESSION['access'];
      if ($access > 1 && !is_null($callsign)) {
	  $notifications = new \TPS\notification($callsign);
          $notifications->checkConvert();
      }
   }

   $app->view()->setData('userName',$user);
   $app->view()->setData('userId',$uid);
   $app->view()->setData('permissions',$access);
   $app->view()->setData('callsign', $callsign);
});
/*
$app->hook('slim.after', function() use ($app){
    $uid = null;
    if (isset($_SESSION['account'])) {
       $uid = $_SESSION['account'];
    }
    $log = new \TPS\logger($uid);
    $log->debug("Render complete ".$app->request->url);
});*/
require_once 'routes.php';

