<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

require_once '../vendor/autoload.php'; // Importar Dotenv y JWT
use Firebase\JWT\JWT; // Biblioteca JWT (JSON Web Token)
use Firebase\JWT\Key;
use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/');//Ruta variables de entorno.
$dotenv->load(); //Inicialización de dotenv



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"));
    $token = $data->token; //Token enviado desde el cliente
    $secretKey = $_ENV['SECRET_KEY'];
    $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'));
    $decodedToken = JWT::decode($token, new Key($secretKey, 'HS256'), $headers = new stdClass());
        if ($decodedToken) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "token verificado",
            ));
        }
        else {
            http_response_code(500);
            echo json_encode(array("message" => "error verified token"));
        }
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "error token cliente"));
    }

?>