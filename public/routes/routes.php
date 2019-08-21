<?php

/* 
 * The MIT License
 *
 * Copyright 2015 James Oliver <support@ckxu.com>.
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
//function autoload_class_multiple_directory($class_name) 
//{
//
//    # List all the class directories in the array.
//    $array_paths = glob("/*", GLOB_ONLYDIR);
//
//    foreach($array_paths as $path)
//    {
//        $file = sprintf('%s/_include_.php', $path, $class_name);
//        if(is_file($file)) 
//        {
//            include_once $file;
//        } 
//
//    }
//}
#spl_autoload_register();#'autoload_class_multiple_directory');

#load page groups
require_once 'environment.php';
require_once 'notification.php';
require_once 'library.php';
require_once 'reviews.php';
require_once 'station.php';
require_once 'program.php';
require_once 'episode.php';
require_once 'label.php';
require_once 'playlist.php';
require_once 'host.php';
require_once 'traffic.php';
require_once 'user.php';
#load api last;
require_once 'api.php';

