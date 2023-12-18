<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

function optionsCatalogue(Request $request, Response $response, $args)
{

  // Evite que le front demande une confirmation à chaque modification
  $response = $response->withHeader("Access-Control-Max-Age", 600);

  return addHeaders($response);
}

function hello(Request $request, Response $response, $args)
{
  $array = [];
  $array["nom"] = $args['name'];
  $response->getBody()->write(json_encode($array));
  return $response;
}

function getSearchCatalogue(Request $request, Response $response, $args)
{
  $flux = file_get_contents("../assets/mock/produits.json");
  $flux = json_decode($flux, true);

  $filteredFlux = array_filter($flux, function ($item) use ($args) {
    return strpos(strtolower($item['designation']), strtolower($args['filtre'])) !== false;
  });

  $filteredFlux = array_values($filteredFlux);

  $jsonData = json_encode($filteredFlux);
  $response = $response->withHeader('Content-Type', 'application/json');
  $response->getBody()->write($jsonData);

  return addHeaders($response);
}

// API Nécessitant un Jwt valide
function getCatalogue(Request $request, Response $response, $args)
{
  $path = "../assets/mock/produits.json";

  if (file_exists($path)) {
    $jsonContent = file_get_contents($path);

    $data = json_decode(
      $jsonContent,
      true
    );

    if ($data !== null) {
      $jsonData = json_encode($data);

      $response = $response->withHeader('Content-Type', 'application/json');

      $response->getBody()->write($jsonData);

      return $response;
    }
  }
  return $response->withStatus(500)->getBody()->write("Erreur lors de la récupération du catalogue.");
}

function optionsUtilisateur(Request $request, Response $response, $args)
{

  // Evite que le front demande une confirmation à chaque modification
  $response = $response->withHeader("Access-Control-Max-Age", 600);

  return addHeaders($response);
}

// API Nécessitant un Jwt valide
function getUtilisateur(Request $request, Response $response, $args)
{

  $payload = getJWTToken($request);
  $login  = $payload->userid;

  $flux = '{"nom":"martin","prenom":"jean"}';

  $response->getBody()->write($flux);

  return addHeaders($response);
}

function postLogin(Request $request, Response $response, $args)
{

  $body = $request->getParsedBody();

  if (isset($body['login']) && isset($body['password'])) {
    $username = $body['login'];
    $password = $body['password'];

    if ($username === 'emma' && $password === 'toto') {
      $token = createJWT($response);

      $userData = [
        'nom' => 'Watson',
        'prenom' => 'Emma',
      ];

      $flux = json_encode($userData);

      $response = createJwt($response, $token);

      $response->getBody()->write($flux);

      return addHeaders($response);
    }
  }

  $response->getBody()->write(json_encode(['error' => 'Identifiants incorrects']));
  return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
}
