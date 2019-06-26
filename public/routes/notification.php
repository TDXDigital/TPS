<?php

/* 
 * The MIT License
 *
 * Copyright 2016 J.oliver.
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
include_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."notifications.php";
$notifications = new \TPS\notification(\TPS\util::get($_SESSION, 'CALLSIGN'));

$app->get('/message', function () use ($app){
    $app->redirect('/message/');
});

$app->group("/message", array($authenticate($app,[1,2])),
        function() use ($app,$authenticate, $notifications){
    $app->get("/", $authenticate($app, [1,2]), function () use ($app, $notifications){
        $broadcasts = $notifications->listUserNotifications('*', $_SESSION['access']);
        $messages = $notifications->listUserNotifications($_SESSION['username'], $_SESSION['access']);
        $value = array_merge($broadcasts, $messages);
        standardResult::ok($app, $value, null);
    });
    $app->put("/acknowledge", $authenticate($app, [1, 2]), function () use ($app, $notifications) {
	$id = $app->request->put("id");
	$notifications = new \TPS\notification(\TPS\util::get($_SESSION, 'CALLSIGN'));
	$notifications->acknowledge($id);
    });
});
