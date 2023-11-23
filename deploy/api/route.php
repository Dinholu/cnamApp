<?php

	$app->get('/api/hello/{name}', 'hello');

	$app->options('/api/catalogue', 'optionsCatalogue' );

	// API Nécessitant un Jwt valide
	$app->get('/api/catalogue/{filtre}', 'getSearchCalatogue' );

	// API Nécessitant un Jwt valide
	$app->get('/api/catalogue', 'getCatalogue');

	$app->options('/api/utilisateur', 'optionsUtilisateur');

	// API Nécessitant un Jwt valide
	$app->get('/api/utilisateur', 'getUtilisateur');

	// APi d'authentification générant un JWT
	$app->post('/api/utilisateur/login', 'postLogin');
	
	

