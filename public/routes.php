<?php
$app->notFound(function() use ($app) {
    global $base_url, $twig;
    $params = array(
        'base_url' => $base_url,
        'title' => 'Error 404',
        'message' => "$base_url Url not found",
    );
    $app->render('error.html.twig',$params);
});

$app->get('/', $authenticate($app), function() use ($app){
    $app->render('test.html.twig');
});

$app->get('/login', function() use ($app){
    $app->render('login.html.twig');
    //$app->redirect('/Security/login.html');
});

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
   if (isset($flash['email'])) {
      $email_value = $flash['email'];
   }
   if (isset($flash['errors']['email'])) {
      $email_error = $flash['errors']['email'];
   }
   if (isset($flash['errors']['password'])) {
      $password_error = $flash['errors']['password'];
   }
   $app->render('login.php', array('error' => $error, 'email_value' => $email_value, 'email_error' => $email_error, 'password_error' => $password_error, 'urlRedirect' => $urlRedirect));
});

$app->post("/login", function () use ($app) {
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $errors = array();
    if ($email != "brian@nesbot.com") {
        $errors['email'] = "Email is not found.";
    } else if ($password != "aaaa") {
        $app->flash('email', $email);
        $errors['password'] = "Password does not match.";
    }
    if (count($errors) > 0) {
        $app->flash('errors', $errors);
        $app->redirect('/login');
    }
    $_SESSION['user'] = $email;
    if (isset($_SESSION['urlRedirect'])) {
       $tmp = $_SESSION['urlRedirect'];
       unset($_SESSION['urlRedirect']);
       $app->redirect($tmp);
    }
    $app->redirect('/');
});

$app->run();