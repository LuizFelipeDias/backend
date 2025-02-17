<?php
function loadPDOConnection() {
    // URL da API que retorna as credenciais
    $apiUrl = 'https://backend-production-6806.up.railway.app/conexaoPDO.php';

    // Obtém as credenciais do banco de dados via API
    $credentials = file_get_contents($apiUrl);
    if ($credentials === false) {
        die("Erro ao carregar as credenciais do banco de dados.");
    }

    // Decodifica o JSON
    $credentials = json_decode($credentials, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Erro ao decodificar as credenciais do banco de dados.");
    }

    // Cria a conexão PDO
    try {
        $pdo = new PDO(
            "mysql:host={$credentials['host']};dbname={$credentials['dbname']};charset=utf8mb4",
            $credentials['username'],
            $credentials['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}

// Carrega a conexão PDO
$pdo = loadPDOConnection();

// Verifica se a conexão foi feita corretamente
if (!$pdo) {
    die("Falha na conexão com o banco de dados.");
}


// Consulta para selecionar todos os preços
$query = "SELECT * FROM categories";

try {
    $resultado = $pdo->query($query); // Usando $pdo, que deve ser definido em conexaoPDO.php
    $dados = $resultado->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dados);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}
?>
