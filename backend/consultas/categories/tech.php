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


$query = "
    SELECT 
        p.id, p.name, p.brand, p.description, p.in_stock, 
        g.image_url, pr.amount, pr.currency_symbol,
        a.id AS attribute_id, a.name AS attribute_name, a.type AS attribute_type,
        ai.display_value, ai.value
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN galleries g ON p.id = g.product_id
    LEFT JOIN prices pr ON p.id = pr.product_id
    LEFT JOIN attributes a ON p.id = a.product_id
    LEFT JOIN attribute_items ai ON a.id = ai.attribute_id
    WHERE c.name = 'tech' 
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
