<?php
    header("Content-Type: application/rss+xml; charset=ISO-8859-1");

    include "../functions.php";
    include "../db_connect.php";

    if($mysqli->connect_errno){
        die("Connection Failed ".$mysqli->connect_error);
    }

    $rssfeed = '<?xml version="1.0" encoding="ISO-8859-1"?>
    ';
    $rssfeed .= '<rss version="2.0">
    ';
    $rssfeed .= '<channel>
    ';
    $rssfeed .= '<title>My RSS feed</title>
    ';
    $rssfeed .= '<link>http://picard.local.ckxu.com</link>
    ';
    $rssfeed .= '<description>This is an example RSS feed</description>
    ';
    $rssfeed .= '<language>en-us</language>
    ';
    $rssfeed .= '<copyright>Copyright (C) 2009 mywebsite.com</copyright>
    ';
    $rssfeed .= '<item>
    ';
    $rssfeed .= '<title>' . "EXAMPLE RSS" . '</title>
    ';
    $rssfeed .= '<description>' . "YOUR DOING IT WRONG" . '</description>
    ';
    $rssfeed .= '<link>' . "http://picard.local.ckxu.com" . '</link>
    ';
    $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime("now")) . '</pubDate>
    ';
    $rssfeed .= '</item>
    ';
 
    $rssfeed .= '</channel>
';
    $rssfeed .= '</rss>';
 
    echo $rssfeed;
?>