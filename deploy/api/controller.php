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

function  getSearchCalatogue(Request $request, Response $response, $args)
{
  $flux = '[{"titre":"linux","ref":"001","prix":"20"},{"titre":"java","ref":"002","prix":"21"},{"titre":"windows","ref":"003","prix":"22"},{"titre":"angular","ref":"004","prix":"23"},{"titre":"unix","ref":"005","prix":"25"},{"titre":"javascript","ref":"006","prix":"19"},{"titre":"html","ref":"007","prix":"15"},{"titre":"css","ref":"008","prix":"10"}]';

  $response->getBody()->write($flux);

  return addHeaders($response);
}

// API Nécessitant un Jwt valide
function getCatalogue(Request $request, Response $response, $args)
{
  $flux = '[
  {
    "ref": "x001",
    "designation": "angular",
    "prix": 10.5,
    "qte": 1
  },
  {
    "ref": "x002",
    "designation": "ubuntu",
    "prix": 30.5,
    "qte": 0
  },
  {
    "ref": "x003",
    "designation": "docker",
    "prix": 40.5,
    "qte": 2
  },
  {
    "ref": "x004",
    "designation": "java",
    "prix": 50.5,
    "qte": 1
  },
  {
    "ref": "x005",
    "designation": "php",
    "prix": 60.5,
    "qte": 5
  },
  {
    "ref": "x006",
    "designation": "mysql",
    "prix": 70.5,
    "qte": 1
  },
  {
    "ref": "x007",
    "designation": "mongodb",
    "prix": 80.5,
    "qte": 5
  },
  {
    "ref": "x008",
    "designation": "nodejs",
    "prix": 90.5,
    "qte": 5
  },
  {
    "ref": "x009",
    "designation": "expressjs",
    "prix": 100.5,
    "qte": 7
  },
  {
    "ref": "x010",
    "designation": "reactjs",
    "prix": 110.5,
    "qte": 8
  }
]';

  $response->getBody()->write($flux);

  return addHeaders($response);
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
  $flux = '{"nom":"Stones","prenom":"emma"}';
  $database = 'login=emma&password=toto';

  parse_str($request->getBody()->getContents(), $requestData);

  parse_str($database, $databaseData);

  if ($requestData['login'] == $databaseData['login'] && $requestData['password'] == $databaseData['password']) {
    $response = createJwT($response);
    $response->getBody()->write($flux);
  } else {
    $response = $response->withStatus(401);
  }

  return addHeaders($response);
}
