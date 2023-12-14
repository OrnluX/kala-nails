<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type");
require_once 'vendor/autoload.php'; // Importar Dotenv y JWT
require_once 'conection.php'; // Conexión a la base de datos

use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
use Firebase\JWT\JWT; // Biblioteca JWT (JSON Web Token)

$dotenv = Dotenv::createImmutable(__DIR__ . '/config');//Ruta variables de entorno.
$dotenv->load(); //Inicialización de dotenv
$conn = testConexion(); //Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"));
    
    // Verificar si se recibieron los campos requeridos (usuario y contraseña)
    if(!empty($data->username)  && !empty($data->password)) {
        //Sanitizar las variables de carácteres especiales
        $username = filter_var($data->username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_var($data->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Consulta para buscar al usuario en la base de datos
        $query = "SELECT * FROM usuarios WHERE username = :username";
        $stmt = $conn->prepare($query); //Preparar consulta
        $stmt->bindParam(':username', $username); //Vincular variables
        $stmt->execute(); 
        $user = $stmt->fetch(PDO::FETCH_ASSOC); //Guardamos la fila correspondiente al usuario

        //Validamos primero si el usuario existe en la base de datos, luego la contraseña.
        if ($user && password_verify($password, $user['password'])) { //password_verify() verifica si el password coincide con el hash almacenado
            
            $tokenPayload = array(
                "authorized" => true
            );
            $secretKey = $_ENV['SECRET_KEY']; //Llave secreta para firmar el token
            $token = JWT::encode($tokenPayload, $secretKey, 'HS256'); //Almacena el token encriptado con el algoritmo HS256
            
            // //Almacenar el token en la base de datos una vez generado
            $updateTokenQuery = "UPDATE usuarios SET token = :token WHERE id = :id";
            $stmtUpdateToken = $conn->prepare($updateTokenQuery);
            $stmtUpdateToken->bindParam(':token', $token);
            $stmtUpdateToken->bindParam(':id', $user['id']);
            $stmtUpdateToken->execute();

            http_response_code(200); // La contraseña es correcta. Inicio de sesión exitoso
            
            echo json_encode(array(
            "message" => "Login Exitoso",
            "userData" => $tokenPayload,
            "token" => $token
            ));
        
        } else {
            // Usuario no encontrado en la base de datos
            http_response_code(401);
            echo json_encode(array(
                "message" => "Usuario y/o contraseña incorrectos"
            ));
        }
    } 
    else {
        // Campos faltantes en la solicitud
        http_response_code(400);
        echo json_encode(array(
            "message" => "Faltan datos de usuario y/o contraseña
            "));
    }
}
?>