<?php
// includes/verificar_login.php

// A sessão já deve ter sido iniciada no db.php, mas é bom garantir.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se a variável de sessão 'id_usuario' não existir, significa que o usuário não está logado.
if (!isset($_SESSION['id_usuario'])) {
    // Destrói qualquer resquício de sessão e redireciona para o login
    session_destroy();
    // O caminho para o login.php precisa ser absoluto a partir da raiz do site.
    // Ajuste o caminho se necessário.
    header("Location: /sistema-estoque-vendas/login.php?erro=2"); // erro=2 significa acesso negado
    exit();
}
?>