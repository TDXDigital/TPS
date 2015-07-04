<?php
namespace Controller;
Error_reporting(0);
Class Main extends \TPS\Controller
{
    public function index()
    {
        $this->render("test", array("title" => $this->data->message, "name" => "Home"));
    }
    public function login()
    {
        $this->render("login", array("title" => $this->data->siteName,
            "append"=>$this->data->append,"name" => "Login"));
    }
    public function test($title)
    {
        $this->render("test", array("title" => $title, "name" => "Test"));
    }

    public function notFound($callable=NULL)
    {
        $url = $_SERVER['REQUEST_URI'];
        $this->render('error', array(
            "statusCode"=>404,
            "title"=>"PC LOAD LETTER (404)",
            "message"=>"Error 404: Page Not Found",
            "details"=>array("page '$url' could not be found","you managed to request a page that cannot print"
            . "please follow the navigation to the homepage and load letter paper"),
            "navigation"=>array(
                array(
                    "href"=>"/",
                    "caption"=>"Home"
                    )
            )
        ), 404);
    }
    
    public function error($argument = null) {
        if($argument instanceof Exception){
            $this->render('error',array(
                "title"=>"Error 500",
                "message"=>$argument.getMessage(),
                "ExceptionCode"=>$argument.getCode(),
                "TraceString"=>$argument.getTraceAsString()),
                    500);
        }
        else{
        }
        
        #parent::error($argument);
    }
}
