<?php

namespace TPS;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."../../TestsBase.php";
include dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../public/lib/libs.php';

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-01-12 at 03:26:49.
 */
class TPSTest extends \TestsBase {

    /**
     * @var TPS
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $GLOBALS['pdo'] = $this->getConnection();
        $this->object = new \TPS\TPS(FALSE,FALSE,NULL,
                dirname(__FILE__).'/_files/DBSETTINGS.xml');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        unset($GLOBALS['pdo']);
    }

    /**
     * @covers TPS\TPS::getStations
     * @todo   Implement testGetStations().
     */
    public function testGetStations() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }
    
    /**
     * @covers TPS\TPS::sanitizePagination
     */
    public function testSanitizePagination(){
        // test standard values
        $page = 5;
        $max = 50;
        $this->object->sanitizePagination($page,$max);
        $this->assertEquals(array($page,$max),array(199,50),
                "standard test failed");
        // test values out of bounds (9999), page > 0 is valid
        $page = 500;
        $max = 9999;
        $this->object->sanitizePagination($page,$max);
        $this->assertEquals(array($page,$max),array(498999,1000),
                "bounds test failed");
        // verify values that are not expected are ignored
        $page = "string1";
        $max = array("string",10);
        $this->object->sanitizePagination($page,$max);
        $this->assertEquals(array($page,$max),array(0,1000),
                "non int test failed");
        // test negatives
        $page = -99999;
        $max = -99999;
        $this->object->sanitizePagination($page,$max);
        $this->assertEquals(array($page,$max),array(0,1000), 
                "negatives test failed");
        // test zero values
        $page = 0;
        $max = 0;
        $this->object->sanitizePagination($page,$max);
        $this->assertEquals(array($page,$max),array(0,1000), 
                "zero value test failed");
    }
}
