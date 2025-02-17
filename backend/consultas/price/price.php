<?php
require_once '../../../conexao/conexaoPDO.php'; // Subindo 3 níveis


// Consulta para selecionar todos os preços
$query = "SELECT * FROM prices";

try {
    $resultado = $pdo->query($query); // Usando $pdo, que deve ser definido em conexaoPDO.php
    $dados = $resultado->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dados);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
?>
