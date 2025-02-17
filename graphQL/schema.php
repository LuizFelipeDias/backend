<?php
require __DIR__ . '/../conexaoPDO.php'; // Ajuste o caminho se necessário
require 'vendor/autoload.php'; // Biblioteca GraphQL

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;

// Definição do tipo Produto
$produtoType = new ObjectType([
    'name' => 'Produto',
    'fields' => [
        'id' => ['type' => Type::string()],
        'name' => ['type' => Type::string()],
        'in_stock' => ['type' => Type::boolean()],
        'description' => ['type' => Type::string()],
        'category_id' => ['type' => Type::int()], // bigint tratado como int no GraphQL
        'brand' => ['type' => Type::string()],
    ]
]);

// Definição da Query
$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'produtos' => [
            'type' => Type::listOf($produtoType),
            'resolve' => function () use ($pdo) {
                try {
                    $stmt = $pdo->query("SELECT * FROM products");
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    error_log("Erro ao buscar produtos: " . $e->getMessage());
                    return null;
                }
            }
        ]
    ]
]);

// Criando o Schema
$schema = new Schema([
    'query' => $queryType
]);

// Processando a Query via GET
try {
    if (!isset($_GET['query'])) {
        throw new Exception("Nenhuma consulta GraphQL recebida.");
    }

    $query = $_GET['query'];
    error_log("Consulta recebida: " . $query); // Log da consulta

    $result = GraphQL::executeQuery($schema, $query);
    $output = $result->toArray();
} catch (\Exception $e) {
    $output = ['error' => $e->getMessage()];
    error_log("Erro: " . $e->getMessage()); // Log do erro
}

header('Content-Type: application/json');
echo json_encode($output);
?>
