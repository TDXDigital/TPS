<?php

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'AutoLoader.php');
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'TestsBase.php');
// Register the directory to your include files
\AutoLoader::registerDirectory(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'public/lib');
