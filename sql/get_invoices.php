<?php
header('Content-Type: application/json');

// Database connection details
$host = 'localhost';
$dbname = 'sistema_inventario';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch invoice details
    $stmt = $pdo->query("
        SELECT 
            p.nombre_producto AS product_name, 
            s.cantidad AS quantity, 
            s.precio_venta AS unit_price, 
            s.fecha_venta AS sale_date
        FROM ventas s 
        JOIN productos p ON s.codigo_producto = p.codigo_producto 
        ORDER BY s.fecha_venta DESC
    ");
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'invoices' => $invoices]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
