<?php

$app->get('/api/hello/{name}', 'hello');


// API Nécessitant un Jwt valide
$app->get('/api/catalogue/filtrer', 'getSearchCatalogue');

// API Nécessitant un Jwt valide
$app->get('/api/catalogue', 'getCatalogue');

$app->options('/api/utilisateur', 'optionsUtilisateur');

// API Nécessitant un Jwt valide
$app->get('/api/utilisateur', 'getUtilisateur');

$app->post('/api/utilisateur/signup', 'getSignup');

// APi d'authentification générant un JWT
$app->post('/api/utilisateur/login', 'postLogin');

$app->get('/api/catalogue/categories', 'getCategories');

// Route OPTIONS pour la gestion CORS préalable pour la route /api/catalogue/categories
$app->options('/api/catalogue/categories', function ($request, $response) {
    // Ajoutez ici les en-têtes CORS nécessaires pour la route /api/catalogue/categories
    $response = $response->withHeader('Access-Control-Max-Age', '*');
    return $response;
});
