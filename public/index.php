<?php
// error_reporting(-1);
// ini_set('display_errors', 'On');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../includes/DbOperations.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,

        'logger' => [
            'name' => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/../logs/app.log',
        ],
    ],
];

$app = new \Slim\App;

/*
endpoint: createUser
parameters : username, email, password
method : POST
*/
$app->post('/createuser', function (Request $request, Response $response ){
	//If we have no empty parameters
	if (!haveEmptyParameters(array('email','username','password'), $response)) {
		$request_data = $request->getParsedBody();
		
		$email = $request_data['email'];
		$password = $request_data['password'];
		$username = $request_data['username'];

	//encrypt the password
		$hash_password = password_hash($password, PASSWORD_DEFAULT);

		$db = new DbOperations;

	 	$result = $db->createUser($username, $email, $password);

	 	if ($result == USER_CREATED) {

			$message = array();
			$message['error'] = false;
			$message['message'] = 'User created succesfully';

			$response->write(json_encode($message));

			return $response
			->withHeader('Content-type', 'application/json')
			->withStatus(201);	

		}else if($result == USER_FAILURE){
			$message = array();
			$message['error'] = true;
			$message['message'] = 'some error occured';

			$response->write(json_encode($message));

			return $response
			->withHeader('Content-type', 'application/json')
			->withStatus(422);	
		 }else if($result == USER_EXISTS){
			$message = array();
			$message['error'] = true;
			$message['message'] = 'User already exists';

			$response->write(json_encode($message));

			return $response
			->withHeader('Content-type', 'application/json')
			->withStatus(422);	
		}
	 }

		return $response
		->withHeader('Content-type', 'application/json')
		->withStatus(422);	

});

function haveEmptyParameters($required_params, $response){
	$error = false;
	$error_params = '';
	$request_params = $_REQUEST;

	foreach ($required_params as $param) {
		if (!isset($request_params[$param]) || strlen($request_params[$param]) <=0 ) {
			$error = true;
			$error_params .= $param.', ';	
		}
	}

	if ($error) {
		$error_detail = array();
		$error_detail['error'] = true;
		$error_detail['message'] = ' Required params '. substr($error_params, 0, -2).' are missing or empty';
		$response->write(json_encode($error_detail)); 
	}
	return $error;
	
}

// $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
//     $name = $args['name'];
//     $response->getBody()->write("Hello, $name");

//     $db = new DbConnect;

//     if($db->connect() != null){
//     	echo 'connection succesful';
//     }

//     return $response;
// });

$app->run();

?>