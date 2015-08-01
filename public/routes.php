<?php
$app->notFound(function() use ($app) {
    global $base_url, $twig;
    $params = array(
        'base_url' => $base_url,
        'title' => 'Error 404',
        'message' => "We couldn't find the page you asked for, sorry about that",
    );
    $app->render('error.html.twig',$params);
});

$app->get('/', $authenticate($app), function() use ($app){
    $app->render('test.html.twig');
});

/*$app->get('/login', function() use ($app){
    $app->render('login.html.twig');
    //$app->redirect('/Security/login.html');
);*/

$app->get("/login", function () use ($app) {
   $flash = $app->view()->getData('flash');
   $error = '';
   if (isset($flash['error'])) {
      $error = $flash['error'];
   }
   $urlRedirect = '/';
   if ($app->request()->get('r') && $app->request()->get('r') != '/logout' && $app->request()->get('r') != '/login') {
      $_SESSION['urlRedirect'] = $app->request()->get('r');
   }
   if (isset($_SESSION['urlRedirect'])) {
      $urlRedirect = $_SESSION['urlRedirect'];
   }
   $email_value = $email_error = $password_error = '';
   if (isset($flash['Username'])) {
      $email_value = $flash['Username'];
   }
   if (isset($flash['errors']['Username'])) {
      $email_error = $flash['errors']['Username'];
   }
   if (isset($flash['errors']['password'])) {
      $password_error = $flash['errors']['password'];
   }
   $app->render('login.html.twig', array('error' => $error, 'Username' => $email_value, 'Username_error' => $email_error, 'password_error' => $password_error, 'urlRedirect' => $urlRedirect));
});

$app->post("/login", function () use ($app) {
    $username = $app->request()->post('name');
    $password = $app->request()->post('pass');
    $errors = array();
    if ($username != "brian@nesbot.com") {
        $errors['Username'] = "Username not found.";
    } else if ($password != "aaaa") {
        $app->flash('Username', $username);
        $errors['password'] = "Password does not match.";
    }
    if (count($errors) > 0) {
        $app->flash('errors', $errors);
        $app->redirect('/login');
    }
    $_SESSION['user'] = $username;
    $_SESSION['access'] = 2;
    if (isset($_SESSION['urlRedirect'])) {
       $tmp = $_SESSION['urlRedirect'];
       unset($_SESSION['urlRedirect']);
       $app->redirect($tmp);
    }
    $app->redirect('/');
});

$app->run();