<?php
$host = '207.126.166.43'; // Endereço IP do servidor do banco de dados
$dbname = 'ecommerce';    // Nome do banco de dados
$username = 'root';       // Usuário do banco de dados
$password = 'L&fda220320002';  // Senha do banco de dados (substitua pela senha correta)

// Retorna as credenciais em formato JSON
echo json_encode([
    'host' => $host,
    'dbname' => $dbname,
    'username' => $username,
    'password' => $password
]);

?>
