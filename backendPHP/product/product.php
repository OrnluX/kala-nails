<?php
/**
 * Módulo que maneja la subida de imágenes al server vía POST
 * @param STRING $img
 * @return STRING
 */
function handleImages($img) {
    //STRING $imagen, $target_dir
    $imagen = "http://localhost/kalanailsmenu/backendPHP/product/uploads/";
    try {
        if (strlen($img) > 0) {
            if (filter_var($img, FILTER_SANITIZE_URL) && filter_var($img,FILTER_VALIDATE_URL)) {
                $imagen = $img;
            }
            else {
                throw new Exception("Debe ingresar una URL válida");
            }
        } else if (strlen($img) == 0 && isset($_FILES["imgFile"])) {
            if ($_FILES["imgFile"]["error"] == 0) {
                $target_dir = __DIR__ . "/uploads/" ; //Directorio donde se almacenará la imagen
                $target_file = $target_dir . basename($_FILES["imgFile"]["name"]);
                
                if (move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
                    $imagen = $imagen . basename($_FILES["imgFile"]["name"]);
                } else {
                    throw new Exception("Error al mover el archivo!");
                }
            } 
            else {
                throw new Exception("Error al subir el archivo. Intente nuevamente");
            }
        }
        else {
            throw new Exception("Por favor, proporcione una imagen para el producto");
        }
    
        return $imagen;
    
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array(
        "message" => $e->getMessage()
        ));
    }
}

/**
 * Módulo que recibe un input de datos, verifica y sanitiza las variables. Devuelve un arreglo asociativo con los datos.
 * @param OBJECT $data
 * @return ARRAY
 */
function sanitizeData($data) {
    //Validamos y sanitizamos variables
    $nameFormat ='/^[a-zA-Z][0-9a-zA-Z\h]*$/u'; //Formato que debe tener el string "nombre".
    $nombre = filter_var($data->nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $descripcion = filter_var($data->descripcion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $precio = filter_var($data->precio, FILTER_VALIDATE_FLOAT);
     
    try{
        if (empty($data->nombre) || empty($data->precio)) {
            throw new Exception("Faltan completar campos!");
        } else if (!$precio) { 
            throw new Exception("El precio debe ser un número válido");
        } else if (!(preg_match($nameFormat, $nombre))) { //Validamos que el nombre no empiece con un número y/o un caracter especial.
            throw new Exception("Debe ingresar un nombre válido");
        }else {
            return array (
                "nombre" => $nombre,
                "descripcion" => $descripcion,
                "precio" => $precio,
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
 * Función que maneja la petición para traer todos los productos de la DB. Método GET. Imprime el mensaje de respuesta al cliente en formato JSON con todos los productos existentes.
 * @param OBJECT $connection
 * @return VOID
 */
function getProducts($connection) {
    $query = "SELECT * FROM productos";
    $stmt = $connection->prepare($query);
    $stmt->execute();

    if ($stmt) {
        $data=[];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Decodificar entidades HTML en cada valor del array
            $decodedRow = array_map('html_entity_decode', $row);
            $data[]=$decodedRow;
        }
        echo json_encode($data);
    }   
}

/**
 * Función que maneja las peticiones que permiten ingresar productos a la DB. Método POST. Imprime el mensaje de respuesta al cliente si el producto fue cargado con éxito.
 * @param OBJECT $connection
 * @return VOID
 */
function postProducts($connection) {
    try {

        $data = new stdClass();
        $data->nombre = $_POST['nombre'];
        $data->descripcion = $_POST['descripcion'];
        $data->precio = $_POST['precio'];
        $data->imagen = $_POST['imagen'];
        
        $sanitizedData = sanitizeData($data);//Llamada a la funcion sanitizeData para validar los datos ingresados por el usuario.
        
        if (isset($sanitizedData)){
            $nombre = $sanitizedData["nombre"];
            $descripcion = $sanitizedData["descripcion"];
            $precio = $sanitizedData["precio"];
            
            $imagen = handleImages($data->imagen); //Llamada a la función handeImages para manejar la subida de la imagen, o la URL de la misma.
            
            if(isset($imagen)) {
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
                    "message" => "Producto cargado correctamente!",
                ));  
            }
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array(
            "message" => $e->getMessage()
        ));
    } 
}

/**
 * Función que maneja las peticiones que permiten editar productos en la DB. Método PUT. Imprime el mensaje de respuesta al cliente si el producto fue editado con éxito.
 * @param OBJECT $connection
 * @return VOID
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

/**
 * Función que maneja las peticiones que permiten eliminar un producto de la DB. Método DELENTE. Imprime el mensaje de respuesta al cliente si el producto fue eliminado con éxito.
 * @param OBJECT $connection
 * @return VOID
 */
function deleteProducts($connection) {
    try {
        $id = $_GET['id']; //Obtenemos el ID del producto a editar. No es necesario validar, el cliente no tiene acceso a editarlo.

        //Preparamos la consulta
        $query = "DELETE FROM productos WHERE id = :id";
        $stmt = $connection->prepare($query);

        //Bind params
        $stmt->bindParam(':id', $id);

        //Ejecutar la consulta
        $stmt->execute();

        if (isset($id)) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Producto eliminado correctamente!"
        ));
        } 
        else {
            throw new Exception("Error! Proporcione un ID correcto");
        }
    }catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array(
            "message" => $e->getMessage()
        ));
    }
}

//PROGRAMA PRINCIPAL
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

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
        deleteProducts($conn);
        break;
    
    default:
        http_response_code(405);
        echo json_encode(array(
            "message" => "ERROR. Método no permitido por el servidor."
        ));
        break;
}

?>