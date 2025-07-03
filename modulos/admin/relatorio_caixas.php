<?php
// modulos/admin/relatorio_caixas.php
require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// Apenas admins podem ver esta página
if ($_SESSION['cargo_usuario'] !== 'admin') {
    die("Acesso Negado.");
}

// Query para buscar todas as sessões de caixa com os dados dos usuários
$stmt = $pdo->query("
    SELECT 
        sc.*, 
        u.nome as nome_usuario
    FROM 
        sessoes_caixa sc
    JOIN 
        usuarios u ON sc.id_usuario = u.id
    ORDER BY 
        sc.data_abertura DESC
");
$sessoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Caixas</title>
</head>
<body>
    <?php include_once '../../includes/header.php'; ?>
    <div class="container">
        <h2>Relatório de Sessões de Caixa</h2>
        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Abertura</th>
                    <th>Valor Abertura</th>
                    <th>Total Vendas</th>
                    <th>Fechamento</th>
                    <th>Valor Fechamento</th>
                    <th>Diferença</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessoes as $sessao): ?>
                    <?php 
                        $diferenca = ($sessao['status'] === 'fechado') 
                            ? $sessao['valor_fechamento'] - ($sessao['valor_abertura'] + $sessao['total_vendas_sessao']) 
                            : null;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($sessao['nome_usuario']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sessao['data_abertura'])) ?></td>
                        <td>R$ <?= number_format($sessao['valor_abertura'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($sessao['total_vendas_sessao'], 2, ',', '.') ?></td>
                        <td><?= $sessao['data_fechamento'] ? date('d/m/Y H:i', strtotime($sessao['data_fechamento'])) : '---' ?></td>
                        <td>R$ <?= $sessao['valor_fechamento'] ? number_format($sessao['valor_fechamento'], 2, ',', '.') : '---' ?></td>
                        <td style="color: <?= is_null($diferenca) ? 'black' : ($diferenca < 0 ? 'red' : 'green') ?>">
                            <?= is_null($diferenca) ? '---' : 'R$ ' . number_format($diferenca, 2, ',', '.') ?>
                        </td>
                        <td><?= ucfirst($sessao['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>