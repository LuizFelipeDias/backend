<?php
$host = '207.126.166.43'; // Endereço IP do servidor do banco de dados
$dbname = 'ecommerce';    // Nome do banco de dados
$username = 'railway_user';       // Usuário do banco de dados
$password = 'L&fda220320002';  // Senha do banco de dados (substitua pela senha correta)

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão com o banco de dados estabelecida com sucesso!";
} catch (PDOException $e) {
    // Em caso de erro, exibe a mensagem de erro
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
