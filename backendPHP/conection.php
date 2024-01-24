<?php
    /**
     * Función que prueba la conexión a la base de datos, con las correspondientes credenciales. Retorna el objeto con los resultados de la respuesta.
     * @return OBJECT
     */
    function testConexion() {
        // Datos de conexión a la base de datos
        $servername = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USER'];
        $password = '';
        $dbname = $_ENV['DB_NAME'];

        $conn = '';
        // Probar conexion a la base de datos
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo json_encode("Error al conectar a la base de datos: " . $e->getMessage());
            exit(); // Terminar la ejecución si hay un error en la conexión
        }
        return $conn;
    }
?>