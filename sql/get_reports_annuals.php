<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_inventario;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Filtrar solo cuentas anuales
    $stmt = $pdo->query("SELECT id, nombre_cuenta AS name, MONTH(fecha_creacion) AS month, YEAR(fecha_creacion) AS year 
                         FROM cuentas 
                         WHERE tipo_cuenta = 'anual'");
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver JSON para que JavaScript lo maneje
    echo json_encode([
        'success' => true, 
        'reports' => $reports
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>