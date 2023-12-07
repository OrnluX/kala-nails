<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Content-Type");
    require_once 'vendor/autoload.php'; // Importar Dotenv y JWT
    require_once 'conection.php'; // Conexión a la base de datos

    use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
 
    $dotenv = Dotenv::createImmutable(__DIR__ . '/config');//Ruta variables de entorno.
    $dotenv->load(); //Inicialización de dotenv
    $conn = testConexion();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"));
        $token = $data->token; //Token enviado desde el cliente

        if ($token ) {
            $query = "SELECT token FROM usuarios WHERE token = :token";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $verifiedToken = $stmt->fetch(PDO::FETCH_ASSOC); //Token almacenado en DB, correspondiente al usuario

            if ($verifiedToken) {
                http_response_code(200);
                echo json_encode(array(
                    "message" => "token verificado",
                    "token" => $token,
                    "dbToken" => $verifiedToken["token"]
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
    }
?>