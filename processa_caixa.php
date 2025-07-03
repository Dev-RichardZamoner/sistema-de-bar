<?php
require_once 'config/db.php';
require_once 'includes/verificar_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];

    if ($acao === 'abrir') {
        $valor_abertura = $_POST['valor_abertura'];
        
        $stmt = $pdo->prepare("INSERT INTO sessoes_caixa (id_usuario, valor_abertura, data_abertura, status) VALUES (?, ?, NOW(), 'aberto')");
        $stmt->execute([$_SESSION['id_usuario'], $valor_abertura]);
        
        header("Location: dashboard.php");
        exit();
    }
    
    if ($acao === 'fechar') {
        $id_sessao = $_SESSION['id_sessao_caixa'];
        $valor_fechamento_informado = $_POST['valor_fechamento_informado'];

        // Calcula o total de vendas da sessão
        $stmt_vendas = $pdo->prepare("SELECT SUM(valor_total) as total FROM vendas WHERE id_sessao_caixa = ?");
        $stmt_vendas->execute([$id_sessao]);
        $total_vendas = $stmt_vendas->fetchColumn() ?: 0;

        $stmt = $pdo->prepare("UPDATE sessoes_caixa SET valor_fechamento = ?, total_vendas_sessao = ?, data_fechamento = NOW(), status = 'fechado' WHERE id = ?");
        $stmt->execute([$valor_fechamento_informado, $total_vendas, $id_sessao]);
        
        // Limpa a sessão do caixa e redireciona para a dashboard
        unset($_SESSION['id_sessao_caixa']);
        header("Location: dashboard.php?caixa=fechado");
        exit();
    }
}
?>