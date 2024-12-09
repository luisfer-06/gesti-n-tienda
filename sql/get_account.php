<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_inventario";

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare SQL to select all sales
    $stmt = $pdo->query('SELECT nombre_producto, codigo_producto, cantidad, precio_venta FROM ventas');

    // Fetch all sales
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($sales);
} catch (PDOException $e) {
    // Error handling
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar ventas: ' . $e->getMessage()
    ]);
}
?>
