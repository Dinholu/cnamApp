<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\HttpBasicAuthentication;
use \Firebase\JWT\JWT;
require __DIR__ . '/../vendor/autoload.php';
 
const JWT_SECRET = "makey1234567";

$app = AppFactory::create();

function  addHeaders (Response $response) : Response {
    $response = $response
    ->withHeader("Content-Type", "application/json")
    ->withHeader('Access-Control-Allow-Origin', ('http://localost:4200'))
    ->withHeader('Access-Control-Allow-Headers', 'Content-Type,  Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
    ->withHeader('Access-Control-Expose-Headers', 'Authorization');

    return $response;
}

$app->get('/api/hello/{name}', function (Request $request, Response $response, $args) {
    $array = [];
    $array ["nom"] = $args ['name'];
    $response->getBody()->write(json_encode ($array));
    return $response;
});

function createJwT (Response $response) : Response {

    $issuedAt = time();
    $expirationTime = $issuedAt + 60;
    $payload = array(
    'userid' => 'toto',
    'email' => 'titi@gmail.com',
    'pseudo' => 'titiPseudo',
    'iat' => $issuedAt,
    'exp' => $expirationTime
    );

    $token_jwt = JWT::encode($payload,JWT_SECRET, "HS256");
    $response = $response->withHeader("Authorization", "Bearer {$token_jwt}");
    return $response;
}

$app->options('/api/user', function (Request $request, Response $response, $args) {
    
    // Evite que le front demande une confirmation à chaque modification
    $response = $response->withHeader("Access-Control-Max-Age", 600);
    
    return addHeaders ($response);
});

$app->get('/api/user', function (Request $request, Response $response, $args) {   
    $data = array('nom' => 'toto', 'prenom' => 'titi','adresse' => '6 rue des fleurs', 'tel' => '0606060607');
    $response->getBody()->write(json_encode($data));

    return $response;
});



$app->options('/api/login', function (Request $request, Response $response, $args) {
    
    // Evite que le front demande une confirmation à chaque modification
    $response = $response->withHeader("Access-Control-Max-Age", 600);
    
    return addHeaders ($response);
});
// APi d'authentification générant un JWT
$app->post('/api/login', function (Request $request, Response $response, $args) {   
    $err=false;
    $body = $request->getParsedBody();
    $login = $body ['login'] ?? "";
    $pass = $body ['pass'] ?? "";

    if (!preg_match("/[a-zA-Z0-9]{1,20}/",$login))   {
        $err = true;
    }
    if (!preg_match("/[a-zA-Z0-9]{1,20}/",$pass))  {
        $err=true;
    }

    if (!$err) {
            $response = createJwT ($response);
            $data = array('nom' => 'toto', 'prenom' => 'titi');
            $response->getBody()->write(json_encode($data));
     } else {          
            $response = $response->withStatus(401);
     }
    return $response;
});



$app->options('/api/catalogue', function (Request $request, Response $response, $args) {
    
    // Evite que le front demande une confirmation à chaque modification
    $response = $response->withHeader("Access-Control-Max-Age", 600);
    
    return addHeaders ($response);
});
// API Nécessitant un Jwt valide
$app->get('/api/catalogue/{filtre}', function (Request $request, Response $response, $args) {
    $filtre = $args['filtre'];
    $flux = '[{"titre":"linux","ref":"001","prix":"20"},{"titre":"java","ref":"002","prix":"21"},{"titre":"windows","ref":"003","prix":"22"},{"titre":"angular","ref":"004","prix":"23"},{"titre":"unix","ref":"005","prix":"25"},{"titre":"javascript","ref":"006","prix":"19"},{"titre":"html","ref":"007","prix":"15"},{"titre":"css","ref":"008","prix":"10"}]';
   
    if ($filtre) {
      $data = json_decode($flux, true); 
    	
        $res = array_filter($data, function($obj) use ($filtre)
        { 
            return strpos($obj["titre"], $filtre) !== false;
        });
        $response->getBody()->write(json_encode(array_values($res)));
    } else {
         $response->getBody()->write($flux);
    }

    return $response;
});
$options = [
    "attribute" => "token",
    "header" => "Authorization",
    "regexp" => "/Bearer\s+(.*)$/i",
    "secure" => false,
    "algorithm" => ["HS256"],
    "secret" => JWT_SECRET,
    "path" => ["/api"],
    "ignore" => ["/api/hello","/api/login","/api/createUser"],
    "error" => function ($response, $arguments) {
        $data = array('ERREUR' => 'Connexion', 'ERREUR' => 'JWT Non valide');
        $response = $response->withStatus(401);
        return $response->withHeader("Content-Type", "application/json")->getBody()->write(json_encode($data));
    }
];

$app->add(new Tuupola\Middleware\JwtAuthentication($options));
$app->run ();