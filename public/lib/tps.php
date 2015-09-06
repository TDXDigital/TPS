<?php
namespace TPS;
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

class TPS{
    protected $mysqli;
    
    /**
     * @access private
     * @param int $pagination current page index
     * @param int $maxResult number of items to in response
     */
    protected function sanitizePagination(&$pagination,&$maxResult){
        if( !is_int($maxResult) || $maxResult > 1000):
            $maxResult = 1000;
        endif;
        if( !is_int($pagination)):
            $pagination = 1;
        endif;
        $floor = abs(($pagination*$maxResult))-($maxResult+1);
        $ceil = abs(($pagination*$maxResult));
        // Simply for security. should never happen
        if ($floor < 0):
            $floor=0;
        endif;
        if($ceil < 0):
            $ceil = abs($ceil);
        endif;
        $pagination = $floor;
        $maxResult = $ceil;
    }

    public function __construct() {
        global $mysqli;
        $this->mysqli = $mysqli;
    }
    
    public function getStations(){
        
    }
    /*
     * should be used to create top level params such as DB connection
     */
}
