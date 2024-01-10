<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


function optionsCatalogue(Request $request, Response $response, $args)
{
	$response = $response->withHeader('Access-Control-Max-Age', '*');

	return addHeaders($response);
}

function optionsCategories(Request $request, Response $response, $args)
{
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

function getSearchCatalogue(Request $request, Response $response)
{
	global $entityManager;

	$params = $request->getQueryParams();
	$term = $params['term'] ?? null;
	$category = $params['category'] ?? null;

	$produitRepository = $entityManager->getRepository('Produits');
	$queryBuilder = $produitRepository->createQueryBuilder('p');

	if ($term) {
		$queryBuilder->where('LOWER(p.nom) LIKE :filtre OR LOWER(p.description) LIKE :filtre')
			->setParameter('filtre', '%' . strtolower($term) . '%');
	}

	if ($category) {
		$queryBuilder->andWhere('p.categorie = :category')
			->setParameter('category', $category);
	}

	$produits = $queryBuilder->getQuery()->getResult();

	if ($produits) {
		$data = [];
		foreach ($produits as $produit) {
			$data[] = array(
				'id' => $produit->getId(),
				'nom' => $produit->getNom(),
				'img' => $produit->getImg(),
				'description' => $produit->getDescription(),
				'prix' => $produit->getPrix(),
				'categorie' => $produit->getCategorie()->getLabel(),
			);
		}

		$response = addHeaders($response);
		$response = createJwT($response);
		$response->getBody()->write(json_encode($data));
	} else {
		$response = $response->withStatus(404);
		$response->getBody()->write("Aucun produit trouvé pour le filtre fourni.");
	}

	return addHeaders($response);
}

function getCategories(Request $request, Response $response)
{
	global $entityManager;

	$categorieRepository = $entityManager->getRepository('Categories');
	$categories = $categorieRepository->findAll();
	$data = [];
	if ($categories) {
		foreach ($categories as $categorie) {
			$data[] = [
				'id' => $categorie->getId(),
				'label' => $categorie->getLabel(),
			];
		}
	} else {
		$response = $response->withStatus(404);
	}
	$response->getBody()->write(json_encode($data));

	return addHeaders($response);
}

function getCommandes(Request $request, Response $response)
{
	global $entityManager;

	$payload = getJWTToken($request);
	$login  = $payload->userid;

	$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
	$utilisateur = $utilisateurRepository->findOneBy(array('login' => $login));

	$commandeRepository = $entityManager->getRepository('Commandes');
	$commandes = $commandeRepository->findBy(array('utilisateur' => $utilisateur));

	$data = [];

	if ($utilisateur) {

		foreach ($commandes as $commande) {
			$date = $commande->getDate()->format('Y-m-d');
			$data[] = [
				'id' => $commande->getId(),
				'date' => $date,
				'produit' => $commande->getProduit()->getNom(),
				'img' => $commande->getProduit()->getImg(),
				'prix' => $commande->getProduit()->getPrix(),
				'quantite' => $commande->getQuantite(),
			];
		}
	} else {
		$response = $response->withStatus(404);
	}
	$response->getBody()->write(json_encode($data));
	return addHeaders($response);
}

// API Nécessitant un Jwt valide
function getCatalogue(Request $request, Response $response, $args)
{
	global $entityManager;

	$payload = getJWTToken($request);
	$login  = $payload->userid;

	$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
	$utilisateur = $utilisateurRepository->findOneBy(array('login' => $login));

	$produitRepository = $entityManager->getRepository('Produits');
	$produits = $produitRepository->findAll();

	$data = [];

	if ($utilisateur) {

		foreach ($produits as $produit) {
			$data[] = [
				'id' => $produit->getId(),
				'nom' => $produit->getNom(),
				'img' => $produit->getImg(),
				'description' => $produit->getDescription(),
				'prix' => $produit->getPrix(),
				'categorie' => $produit->getCategorie()->getLabel(),
			];
		}
	} else {
		$response = $response->withStatus(404);
	}

	$response->getBody()->write(json_encode($data));

	return addHeaders($response);
}

// API Nécessitant un Jwt valide
function getUtilisateur(Request $request, Response $response, $args)
{
	global $entityManager;

	$payload = getJWTToken($request);
	$login  = $payload->userid;

	$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
	$utilisateur = $utilisateurRepository->findOneBy(array('login' => $login));
	if ($utilisateur) {
		$data = array('nom' => $utilisateur->getNom(), 'prenom' => $utilisateur->getPrenom());
		$response = addHeaders($response);
		$response = createJwT($response);
		$response->getBody()->write(json_encode($data));
	} else {
		$response = $response->withStatus(404);
	}

	return addHeaders($response);
}

function postSignup(Request $request, Response $response)
{
	global $entityManager;
	$err = false;
	$erreur = [];
	$body = $request->getBody();
	$body = json_decode($body, true);

	$nom = $body['nom'] ?? "";
	$prenom = $body['prenom'] ?? "";
	$adresse = $body['adresse'] ?? "";
	$cp = $body['codepostal'] ?? "";
	$ville = $body['ville'] ?? "";
	$email = $body['email'] ?? "";
	$sexe = $body['sexe'] ?? "";
	$login = $body['login'] ?? "";
	$pass = $body['password'] ?? "";
	$tel = $body['telephone'] ?? "";

	// Validation des données
	if (!preg_match("/[a-zA-Z0-9]{3,20}/", $nom)) {
		$err = true;
		array_push($erreur, "Le nom est invalide. Il doit contenir au moins 5 lettres.");
	}
	if (!preg_match("/[a-zA-Z0-9]{3,20}/", $prenom)) {
		array_push($erreur, "Le prenom est invalide. Il doit contenir au moins 5 lettres.");
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{5,20}/", $adresse)) {
		$err = true;
		array_push($erreur, "L'adresse est invalide. Elle doit contenir au moins 5 lettres.");
	}
	if (!preg_match("/[0-9]{5}/", $cp)) {
		array_push($erreur, "Le code postal est invalide. Il doit contenir uniquement 5 chiffres.");
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{5,20}/", $ville)) {
		array_push($erreur, "La ville est invalide. Elle doit contenir au moins 5 lettres.");
		$err = true;
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		array_push($erreur, "L'email est invalide. Elle doit contenir un '.' et un '@'.");
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{1,20}/", $sexe)) {
		array_push($erreur, "Le sexe est invalide.");
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{1,20}/", $login)) {
		array_push($erreur, "Le login est invalide. Il doit contenir uniquement des chiffres et des lettres.");
		$err = true;
	}
	if (strlen($pass) < 8 || !preg_match('/[A-Z]/', $pass) || !preg_match('/[a-z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
		array_push($erreur, "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.");
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{1,20}/", $tel)) {
		array_push($erreur, "Le numéro de téléphone est invalide. Il doit contenir uniquement des chiffres.");
		$err = true;
	}

	if (!$err) {
		$existingUser = $entityManager->getRepository(Utilisateurs::class)->findOneBy(['login' => $login]);

		if ($existingUser) {
			$response = $response->withStatus(409);
			array_push($erreur, "Le login est deja utilise. Veuillez en choisir un autre.");
			$response->getBody()->write(json_encode($erreur));
		} else {
			$hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
			$user = new Utilisateurs();
			$user->setNom($nom);
			$user->setPrenom($prenom);
			$user->setAdresse($adresse);
			$user->setCodepostal($cp);
			$user->setVille($ville);
			$user->setEmail($email);
			$user->setSexe($sexe);
			$user->setLogin($login);
			$user->setPassword($hashedPassword);

			$entityManager->persist($user);
			$entityManager->flush();
			$response = createJwT($response);
			$data = array('id' => $user->getId(), 'nom' => $user->getNom(), 'prenom' => $user->getPrenom());

			$response->getBody()->write(json_encode($data));
		}
	} else {
		$response->getBody()->write(json_encode($erreur));
		$response = $response->withStatus(500);
	}

	return addHeaders($response);
}

function postPay(Request $request, Response $response, $args)
{
	global $entityManager;
	$body = $request->getBody();
	$body = json_decode($body, true);
	$client = $body['client'] ?? "";
	$panier = $body['panier'] ?? "";

	if (!$client || !$panier) {
		$response = $response->withStatus(400);
		$response->getBody()->write("Client ou panier manquant");
		return addHeaders($response);
	}

	$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
	$utilisateur = $utilisateurRepository->findOneBy(array('id' => $client['id']));
	if (!$utilisateur) {
		$response = $response->withStatus(404);
		$response->getBody()->write("Client non trouvé");
		return addHeaders($response);
	}

	$commande = new Commandes();
	$commande->setDate(new \DateTime());
	$commande->setUtilisateur($utilisateur);

	foreach ($panier as $produit) {
		$quantite = $produit['quantite'] ?? "";
		$produitId = $produit['produit']['id'];
		$quantite = $produit['quantite'];
		$prod = $entityManager->find('Produits', $produitId);

		if (!$produit) {
			continue;
		}

		$commande = new Commandes();
		$commande->setDate(new \DateTime());
		$commande->setUtilisateur($utilisateur);
		$commande->setProduit($prod);
		$commande->setQuantite($quantite);
		$entityManager->persist($commande);
	}

	$entityManager->flush();
	$response = $response->withStatus(200);
	$response->getBody()->write(json_encode(["message" => "Paiement effectué pour le client"]));
	return addHeaders($response);
}

function postLogin(Request $request, Response $response, $args)
{
	global $entityManager;
	$err = false;
	$body = $request->getParsedBody();
	$login = $body['login'] ?? "";
	$pass = $body['password'] ?? "";

	if (!preg_match("/[a-zA-Z0-9]{1,20}/", $login)) {
		$err = true;
	}
	if (!preg_match("/[a-zA-Z0-9]{1,20}/", $pass)) {
		$err = true;
	}
	if (!$err) {
		$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
		$utilisateur = $utilisateurRepository->findOneBy(array('login' => $login));
		if ($utilisateur && password_verify($pass, $utilisateur->getPassword())) {
			$response = addHeaders($response);
			$response = createJwT($response);
			$data = array('id' => $utilisateur->getId(), 'nom' => $utilisateur->getNom(), 'prenom' => $utilisateur->getPrenom());
			$response->getBody()->write(json_encode($data));
		} else {

			$response = $response->withStatus(403);
		}
	} else {
		$response = $response->withStatus(500);
	}

	return addHeaders($response);
}
