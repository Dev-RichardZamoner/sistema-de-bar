<?php
require_once 'config/db.php';
require_once 'includes/verificar_login.php';

// Verifica se o usuário já tem uma sessão de caixa aberta
$stmt = $pdo->prepare("SELECT * FROM sessoes_caixa WHERE id_usuario = ? AND status = 'aberto'");
$stmt->execute([$_SESSION['id_usuario']]);
$sessao_aberta = $stmt->fetch(PDO::FETCH_ASSOC);

// Salva o ID da sessão na sessão do PHP para usar na tela de vendas
if ($sessao_aberta) {
    $_SESSION['id_sessao_caixa'] = $sessao_aberta['id'];
} else {
    // Garante que não há lixo de uma sessão anterior
    unset($_SESSION['id_sessao_caixa']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Vendas</title>
</head>
<body>
    <?php include_once 'includes/header.php'; // Nosso menu de navegação ?>

    <div class="container">
        <h2>Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!</h2>

        <?php if ($sessao_aberta): // Se JÁ EXISTE uma sessão aberta ?>
            
            <h3>Caixa Aberto</h3>
            <p>Sua sessão de caixa foi iniciada em <?= date('d/m/Y H:i', strtotime($sessao_aberta['data_abertura'])) ?> com um valor de R$ <?= number_format($sessao_aberta['valor_abertura'], 2, ',', '.') ?>.</p>
            <a href="modulos/vendas/tela_vendas.php" style="font-size: 1.2em; padding: 10px; background: green; color: white;">Continuar para Vendas</a>
            <a href="fechar_caixa.php" style="font-size: 1.2em; padding: 10px; background: red; color: white;">Fechar Caixa</a>

        <?php else: // Se NÃO EXISTE uma sessão aberta ?>
            
            <h3>Abrir Caixa</h3>
            <p>Você precisa abrir o caixa para iniciar as vendas.</p>
            <form action="processa_caixa.php" method="POST">
                <label for="valor_abertura">Valor inicial em caixa (fundo de troco):</label><br>
                <input type="number" step="0.01" name="valor_abertura" id="valor_abertura" required min="0.01">
                <input type="hidden" name="acao" value="abrir">
                <br><br>
                <button type="submit">Abrir Caixa</button>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>