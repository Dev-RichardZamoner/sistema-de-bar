<?php
require_once 'config/db.php';
require_once 'includes/verificar_login.php';

// Se não houver sessão de caixa, não deveria estar aqui
if (!isset($_SESSION['id_sessao_caixa'])) {
    header("Location: dashboard.php");
    exit();
}

$id_sessao = $_SESSION['id_sessao_caixa'];

// Pega o valor de abertura
$stmt_sessao = $pdo->prepare("SELECT valor_abertura FROM sessoes_caixa WHERE id = ?");
$stmt_sessao->execute([$id_sessao]);
$valor_abertura = $stmt_sessao->fetchColumn();

// Calcula o total de vendas da sessão
$stmt_vendas = $pdo->prepare("SELECT SUM(valor_total) as total FROM vendas WHERE id_sessao_caixa = ?");
$stmt_vendas->execute([$id_sessao]);
$total_vendas = $stmt_vendas->fetchColumn() ?: 0;

$total_esperado_caixa = $valor_abertura + $total_vendas;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Fechar Caixa</title>
</head>
<body>
    <?php include_once 'includes/header.php'; ?>
    <div class="container">
        <h2>Fechamento de Caixa</h2>
        
        <p><strong>Valor de Abertura:</strong> R$ <?= number_format($valor_abertura, 2, ',', '.') ?></p>
        <p><strong>Total de Vendas na Sessão:</strong> R$ <?= number_format($total_vendas, 2, ',', '.') ?></p>
        <hr>
        <p><strong>Total esperado em caixa:</strong> R$ <?= number_format($total_esperado_caixa, 2, ',', '.') ?></p>

        <form action="processa_caixa.php" method="POST">
            <label for="valor_fechamento_informado">Por favor, conte o dinheiro e informe o valor total em caixa:</label><br>
            <input type="number" step="0.01" name="valor_fechamento_informado" id="valor_fechamento_informado" required>
            <input type="hidden" name="acao" value="fechar">
            <br><br>
            <button type="submit" onclick="return confirm('Tem certeza que deseja fechar o caixa? Esta ação não pode ser desfeita.')">Confirmar e Fechar Caixa</button>
        </form>
    </div>
</body>
</html>