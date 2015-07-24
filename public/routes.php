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

$app->get('/', function() use ($app){
    if(session_id()==''||!isset($_SESSION)){
        $app->redirect('./login/');
    }
    else{
        $app->render('test.html.twig');
    }
});

$app->get('/login', function() use ($app){
    //$app->render('login.html.twig');
    $app->redirect('Security/login.html');
});

//Routes for customer(s) resource
$app->get('/customers', function () {
    global $base_url, $twig;

    $data = ModelsCustomers::all();

    $params = array('data' => $data,
        'base_url' => $base_url,
        'title' => 'All Customers Listing'
    );
    echo $twig->render('profile.html.twig', $params);
});

$app->get('/customers/add', function () {
    global $base_url, $twig;

    $data = ModelsCustomers::all();

    $params = array('data' => $data,
        'base_url' => $base_url,
        'title' => 'Add New Customer Record'
    );
    echo $twig->render('profile.html.twig', $params);
});

$app->get('/customers/:id', function ($customer_id) {
    global $base_url, $twig;

    $data = ModelsCustomers::find($customer_id);

    $params = array('data' => $data,
        'base_url' => $base_url,
        'title' => 'Customer Record Editor'
    );
    echo $twig->render('customers_editor.html', $params);
});

$app->post('/customers', function() use ($app) {
    global $base_url;
    $data = $app->request->post();

    if (isset($data['put'])) {
        $customer_id = $data['customer_id'];
        $customer = ModelsCustomers::find($customer_id);
    } else if (isset($data['delete'])) {
        $customer_id = $data['customer_id'];

        ModelsCustomers::find($customer_id)->delete();

        $app->redirect($base_url . '/customers');

        return;
    } else {
        $customer = new ModelsCustomers();
    }

    $customer->userInput($data);
    $customer->save();

    $app->redirect($base_url . '/customers');
});

$app->run();