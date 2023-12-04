<?php
header('Access-Control-Allow-Origin: http://localhost:5173/');
require_once 'vendor/autoload.php';

use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
use Firebase\JWT\JWT; // Biblioteca JWT (JSON Web Token)

$dotenv = Dotenv::createImmutable(__DIR__ . '/config');//Ruta variables de entorno.
$dotenv->load(); //Inicialización de dotenv

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
        if ($user && password_verify($password, $user['password'])) { //password_verify() verifica si el password coincide con el hash almacenado
                
            $tokenPayload = array(
                "username" => $user['username'],
                "id"=> $user['id'],
                "token" => $user['token']
            );
            $secretKey = $_ENV['SECRET_KEY']; //Llave secreta para firmar el token
            $token = JWT::encode($tokenPayload, $secretKey, 'HS256'); //Almacena el token encriptado con el algoritmo HS256
            $tokenPayload["token"] = $token; //Almacena el token para enviarlo en la response
            
            //Almacenar el token en la base de datos una vez generado
            $updateTokenQuery = "UPDATE usuarios SET token = :token WHERE id = :id";
            $stmtUpdateToken = $conn->prepare($updateTokenQuery);
            $stmtUpdateToken->bindParam(':token', $token);
            $stmtUpdateToken->bindParam(':id', $user['id']);
            $stmtUpdateToken->execute();

            http_response_code(200); // La contraseña es correcta, el token fue generado. Inicio de sesión exitoso
            echo json_encode(array(
            "message" => "Login Exitoso",
            "userData" => $tokenPayload
            ));
           
        } else {
            // Usuario no encontrado en la base de datos
            http_response_code(401);
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