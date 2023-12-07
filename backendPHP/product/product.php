<?php 
    /**
     * Función que maneja consultas select a la base de datos. Método GET
     * @param OBJECT
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
     * Función que maneja consultas que permiten ingresar datos a la DB. Método POST
     */
    function postProducts() {
        $data = json_decode(file_get_contents("php://input"));
        
        //Validamos los datos. Si los campos requeridos están vaciós se devuelve el código de respuesta HTTP y se termina la conexión.
        if (empty($data->nombre) && empty($data->precio)) {
            http_response_code(400);
            json_encode(array(
                "message" => "Este campo es obligatorio"
            ));
            exit();
        } 
        
        $nombre = filter_var($data->nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $descripcion = filter_var($data->descripcion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $precio = filter_var($data->precio, FILTER_VALIDATE_FLOAT);
        $imagen = filter_var($data->imagen, FILTER_VALIDATE_URL);

    }
    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Content-Type");
    
    require_once '../conection.php';
    $conn = testConexion(); //Conexión a la base de datos
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            getProducts($conn);
            break;
        case 'POST':
            echo "POST";
            break;
        case 'PUT':
            echo "PUT";
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