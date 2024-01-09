<?php

$app->get('/api/hello/{name}', 'hello');


// API Nécessitant un Jwt valide
$app->get('/api/catalogue/filtrer', 'getSearchCatalogue');

// API Nécessitant un Jwt valide
$app->get('/api/catalogue', 'getCatalogue');

$app->options('/api/utilisateur', 'optionsUtilisateur');

// API Nécessitant un Jwt valide
$app->get('/api/utilisateur', 'getUtilisateur');

$app->post('/api/utilisateur/signup', 'postSignup');

// APi d'authentification générant un JWT
$app->post('/api/utilisateur/login', 'postLogin');

$app->post('/api/catalogue/pay', 'postPay');

$app->get('/api/catalogue/categories', 'getCategories');

$app->get('/api/catalogue/commandes', 'getCommandes');

$app->options('/api/utilisateur/commandes', function ($request, $response) {
    $response = $response->withHeader('Access-Control-Max-Age', '*');
    return $response;
});

$app->options('/api/catalogue/categories', function ($request, $response) {
    $response = $response->withHeader('Access-Control-Max-Age', '*');
    return $response;
});

$app->any('/{routes:.*}', function ($request, $response) {
    $indexContent = file_get_contents('../index.html');
    $response->getBody()->write($indexContent);
    return $response->withHeader('Content-Type', 'text/html');
});
