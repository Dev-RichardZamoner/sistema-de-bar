<?php
// auth.php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Usar prepared statements para evitar SQL Injection
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe e se a senha está correta
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido!
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nome_usuario'] = $usuario['nome'];
        $_SESSION['cargo_usuario'] = $usuario['cargo'];

        // Redireciona para a tela principal de vendas
        header("Location: modulos/vendas/tela_vendas.php");
        exit();
    } else {
        // Falha no login
        header("Location: login.php?erro=1");
        exit();
    }
} else {
    // Se não for POST, redireciona para o login
    header("Location: login.php");
    exit();
}
?>