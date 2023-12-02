<?php
header('Access-Control-Allow-Origin: http://localhost:5173/');
require_once 'vendor/autoload.php';

use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
use Firebase\JWT\JWT; // Biblioteca JWT (Jason Web Token)

$dotenv = Dotenv::createImmutable(__DIR__ . '/config');//Ruta variables de entorno.
$dotenv->load();

// Datos de conexión a la base de datos
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = '';
$dbname = $_ENV['DB_NAME'];

// Probar conexion a la base de datos
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Error al conectar a la base de datos: " . $e->getMessage();
    exit(); // Terminar la ejecución si hay un error en la conexión
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"));

    // Verificar si se recibieron los campos requeridos (usuario y contraseña)
    if($data->username != "" && $data->password != "") {
        //Sanitizar las variables de carácteres especiales
        $username = filter_var($data->username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_var($data->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Consulta para buscar al usuario en la base de datos
        $query = "SELECT * FROM usuarios WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //Validamos primero si el usuario es correcto, luego la contraseña.
        if ($user && password_verify($password, $user['password'])) {
                
            $tokenPayload = array(
                "username" => $user['username'],
            );
            $secretKey = $_ENV['SECRET_KEY']; //Llave secreta para firmar el token
            $token = JWT::encode($tokenPayload, $secretKey, 'HS256');

            // La contraseña es correcta, el token existe. Inicio de sesión exitoso
            if ($token) {
                 http_response_code(200);
                echo json_encode(array(
                "message" => "Login Exitoso",
                "token" => $token
                ));
            }
            else {
                echo json_encode(array("message" => "Acceso denegado al servidor"));
                http_response_code(401);
            }
        } else {
            // Usuario no encontrado en la base de datos
            http_response_code(404);
            echo json_encode(array("message" => "Usuario y/o contraseña incorrectos"));
        }
    } 
    else {
        // Campos faltantes en la solicitud
        http_response_code(400);
        echo json_encode(array("message" => "Faltan datos de usuario y/o contraseña"));
    }
}
?>