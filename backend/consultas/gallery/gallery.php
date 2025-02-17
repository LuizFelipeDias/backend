<?php
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once '../../../conexao/conexaoPDO.php'; // Subindo 3 níveis

// Consulta para selecionar todos os preços
$query = "SELECT image_url, product_id, id FROM galleries";

try {
    $resultado = $pdo->query($query); // Usando $pdo, que deve ser definido em conexaoPDO.php
    $dados = $resultado->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dados);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro na consulta: " . $e->getMessage()]);
}
?>
