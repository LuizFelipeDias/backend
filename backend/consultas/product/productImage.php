<?php
// Função para carregar a conexão PDO de forma segura
function loadPDOConnection() {
    // Carrega o conteúdo do arquivo remoto
    $pdoContent = file_get_contents('https://backend-production-6806.up.railway.app/conexaoPDO.php');
    if ($pdoContent === false) {
        die("Erro ao carregar o arquivo de conexão PDO.");
    }

    // Verifica se o conteúdo contém a mensagem de sucesso
    if (strpos($pdoContent, "Conexão com o banco de dados estabelecida com sucesso!") === false) {
        die("Falha na conexão com o banco de dados: arquivo remoto retornou um erro.");
    }

    // Inclui o arquivo de conexão diretamente (se estiver no mesmo servidor)
    // Se estiver em um servidor remoto, use uma API para obter as credenciais
    require 'https://backend-production-6806.up.railway.app/conexaoPDO.php'; // Substitua pelo caminho correto, se necessário

    // Verifica se a variável $pdo foi definida e é uma instância do PDO
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        die("Falha na conexão com o banco de dados: variável \$pdo não definida ou inválida.");
    }

    return $pdo;
}

// Carrega a conexão PDO
$pdo = loadPDOConnection();

// Verifica se a conexão foi feita corretamente
if (!$pdo) {
    die("Falha na conexão com o banco de dados.");
}

$query = "
    SELECT 
        p.id, p.name, p.brand, p.description, p.in_stock, 
        g.image_url, pr.amount, pr.currency_symbol,
        a.id AS attribute_id, a.name AS attribute_name, a.type AS attribute_type,
        ai.display_value, ai.value
    FROM products p
    LEFT JOIN galleries g ON p.id = g.product_id
    LEFT JOIN prices pr ON p.id = pr.product_id
    LEFT JOIN attributes a ON p.id = a.product_id
    LEFT JOIN attribute_items ai ON a.id = ai.attribute_id
    ORDER BY p.id, a.id
";

try {
    $stmt = $pdo->query($query);
    $products = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productId = $row["id"];

        if (!isset($products[$productId])) {
            $products[$productId] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "brand" => $row["brand"],
                "description" => $row["description"],
                "amount" => $row["amount"] ?? 0, 
                "currency_symbol" => $row["currency_symbol"] ?? "$", 
                "in_stock" => $row["in_stock"] ?? 0, // Adiciona a informação de estoque
                "images" => [],
                "attributes" => []
            ];
        }

        if (!empty($row["image_url"])) {
            $products[$productId]["images"][] = $row["image_url"];
        }

        if (!empty($row["attribute_id"])) {
            $products[$productId]["attributes"][] = [
                "id" => $row["attribute_id"],
                "name" => $row["attribute_name"],
                "type" => $row["attribute_type"],
                "value" => $row["value"] ?? "N/A",
                "display_value" => $row["display_value"] ?? "N/A"
            ];
        }
    }

    echo json_encode(array_values($products), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
