<?php
// config/db.php

$host = 'localhost'; // ou o host do seu servidor de banco de dados
$dbname = 'bar';
$user = 'root';
$pass = '';

// Iniciar a sessão em todas as páginas que usarem este arquivo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Em um ambiente de produção, você não deveria exibir o erro detalhado
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>