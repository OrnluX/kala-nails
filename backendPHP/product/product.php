<?php
/**
 * Modulo que recibe un input de datos, verifica y sanitiza las variables. Devuelve un arreglo asociativo con los datos.
 * @param OBJECT $data
 * @return ARRAY
 */
function sanitizeData ($data) {
     //Validamos y sanitizamos variables
     $nombre = filter_var($data->nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
     $descripcion = filter_var($data->descripcion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
     $precio = filter_var($data->precio, FILTER_VALIDATE_FLOAT);
     $imagen = filter_var($data->imagen, FILTER_VALIDATE_URL);
     
     try {
         if (empty($data->nombre) || empty($data->precio)) {
             throw new Exception("Los campos son obligatorios");
         } else if (!$precio) {
             throw new Exception("El precio debe ser un número válido.");
         } else {
            return array (
                "nombre" => $nombre,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "imagen" => $imagen
            );
         }
     } catch (Exception $e) {
         http_response_code(400);
         echo json_encode(array(
           "message" => $e->getMessage()
         ));
     } 

}

/**
 * Función que maneja la consulta para traer todos los productos de la DB. Método GET. Imprime el mensaje de respuesta al cliente en formato JSON con todos los productos existentes.
 * @param OBJECT $connection
 */
function getProducts($connection) {
    $query = "SELECT * FROM productos";
    $stmt = $connection->prepare($query);
    $stmt->execute();

    if ($stmt) {
        $data=[];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[]=$row;
        }
        echo json_encode($data);
    }
}

/**
 * Función que maneja las consultas que permiten ingresar productos a la DB. Método POST. Imprime el mensaje de respuesta al cliente si el producto fue cargado con éxito.
 * @param OBJECT $connection
 */
function postProducts($connection) {
    try {
        $data = json_decode(file_get_contents("php://input"));
        $sanitizedData = sanitizeData($data);//Llamada a la funcion sanitizeData para validar los datos ingresados por el usuario
        
        $nombre = $sanitizedData["nombre"];
        $descripcion = $sanitizedData["descripcion"];
        $precio = $sanitizedData["precio"];
        $imagen = $sanitizedData["imagen"];

        //Preparamos la consulta
        $query = "INSERT INTO productos (nombre, descripcion, precio, imagen)
        VALUES (:nombre, :descripcion, :precio, :imagen)";
        $stmt = $connection->prepare($query);

        //Bind params
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':imagen', $imagen);

        //Ejecutar consulta
        $stmt->execute();

        http_response_code(200);
        echo json_encode(array(
            "message" => "Producto cargado correctamente!"
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            "message" => $e->getMessage()
        ));
    }
}

/**
 * Función que maneja las consultas que permiten editar productos a la DB. Método PUT. 
 * @param INT $id
 * @param OBJECT $connection
 */
function editProducts($connection) {
    try {
        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id; //Obtenemos el ID del producto a editar. No es necesario validar, el cliente no tiene acceso a editarlo.
        $sanitizedData = sanitizeData($data); //LLamada a la funcion sanitizeData para validar datos ingresados por el usuario. 
        
        $nombre = $sanitizedData["nombre"];
        $descripcion = $sanitizedData["descripcion"];
        $precio = $sanitizedData["precio"];
        $imagen = $sanitizedData["imagen"];

        //Preparamos la consulta
        $query = "UPDATE productos
                    SET nombre = :nombre,
                        descripcion = :descripcion,
                        precio = :precio,
                        imagen = :imagen
                    WHERE id = :id";
        $stmt = $connection->prepare($query);

        //Bind params
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':imagen', $imagen);

        //Ejecutar la consulta
        $stmt->execute();

        http_response_code(200);
        echo json_encode(array(
            "message" => "Producto actualizado correctamente!"
        ));
    } catch (Exception $e) {
        echo json_encode(array(
            "message" => $e->getMessage()
        ));
    }
}

//PROGRAMA PRINCIPAL
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Content-Type");

require_once '../vendor/autoload.php'; // Importar Dotenv y JWT
use Dotenv\Dotenv; //Biblioteca dotenv para variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../config');//Ruta variables de entorno.
$dotenv->load(); //Inicialización de dotenv

require_once '../conection.php'; // Conexión a la base de datos
$conn = testConexion(); //Conexión a la base de datos

//Según con qué método se realice la consulta, se llamará a un módulo u otro
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        getProducts($conn);
        break;
    case 'POST':
        postProducts($conn);
        break;
    case 'PUT':
        editProducts($conn);
        break;
    case 'DELETE':
        echo "DELETE";
        break;
    
    default:
        http_response_code(405);
        echo json_encode(array(
            "message" => "ERROR. Método no permitido por el servidor."
        ));
        break;
}

?>