<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "sistema_inventario";

    // Verificar que se recibió un ID de informe
    if (!isset($_GET['report_id'])) {
        throw new Exception('No se proporcionó un ID de informe');
    }

    $reportId = $_GET['report_id'];

    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener detalles del informe anual específico
    $stmtReport = $pdo->prepare("SELECT * FROM cuentas WHERE id = :id");
    $stmtReport->execute([':id' => $reportId]);
    $reportDetails = $stmtReport->fetch(PDO::FETCH_ASSOC);

    if (!$reportDetails) {
        throw new Exception('Informe no encontrado');
    }

    // Calcular productos vendidos en el período anual
    $stmtProducts = $pdo->prepare("
    SELECT 
        p.nombre_producto AS name,
        SUM(v.cantidad) AS quantity
    FROM ventas v
    INNER JOIN productos p ON v.codigo_producto = p.codigo_producto
    WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
    GROUP BY p.codigo_producto, p.nombre_producto
    ");

    $stmtProducts->execute([
        ':fecha_inicio' => $reportDetails['fecha_creacion'],
        ':fecha_fin' => $reportDetails['fecha_fin'] ?? date('Y-m-d H:i:s')
    ]);

    $soldProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    // Ordenar productos por cantidad
    usort($soldProducts, function($a, $b) {
        return $b['quantity'] - $a['quantity'];
    });

    // Preparar detalles del informe para la respuesta
    $details = [
        ['concept' => 'Total Ventas', 'value' => $reportDetails['total_ventas']],
        ['concept' => 'Total Gastos', 'value' => $reportDetails['total_gastos']],
        ['concept' => 'Ganancia Neta', 'value' => $reportDetails['ganancia_neta']]
    ];

    echo json_encode([
        'success' => true,
        'details' => $details,
        'soldProducts' => $soldProducts,
        'totalProductsSold' => array_sum(array_column($soldProducts, 'quantity'))
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
