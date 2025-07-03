<?php
// modulos/estoque/editar_produto.php

require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// --- LÓGICA DE PROCESSAMENTO DO FORMULÁRIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];

    // VERIFICA SE A AÇÃO É DE EXCLUIR
    if (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        
        // **AVISO DE SEGURANÇA IMPORTANTE**
        // Antes de excluir, o ideal seria verificar se o produto não está associado a nenhuma venda
        // para não quebrar o histórico. Por simplicidade, faremos a exclusão direta.
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: gerenciar_estoque.php?mensagem=excluido");
        exit();

    } else { // SE NÃO FOR EXCLUIR, É ATUALIZAR
        
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $preco_venda = $_POST['preco_venda'];
        $quantidade_estoque = $_POST['quantidade_estoque'];
        $codigo_barras = $_POST['codigo_barras'];

        $stmt = $pdo->prepare(
            "UPDATE produtos SET 
                nome = ?, 
                descricao = ?, 
                preco_venda = ?, 
                quantidade_estoque = ?, 
                codigo_barras = ? 
            WHERE id = ?"
        );
        $stmt->execute([$nome, $descricao, $preco_venda, $quantidade_estoque, $codigo_barras, $id]);

        header("Location: gerenciar_estoque.php?mensagem=alterado");
        exit();
    }
}


// --- LÓGICA PARA CARREGAR DADOS DO PRODUTO (GET) ---

// Pega o ID da URL (?id=...)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do produto não fornecido.");
}
$id_produto = $_GET['id'];

// Busca os dados atuais do produto no banco
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id_produto]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrar o produto, encerra a execução
if (!$produto) {
    die("Produto não encontrado.");
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <style>
        .btn-excluir { background-color: #dc3545; color: white; border: none; padding: 10px; cursor: pointer; }
        .btn-salvar { background-color: #28a745; color: white; border: none; padding: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <?php include_once '../../includes/header.php'; ?>

    <div class="container">
        <h2>Editar Produto: <?= htmlspecialchars($produto['nome']) ?></h2>

        <form action="editar_produto.php" method="POST">
            <input type="hidden" name="id" value="<?= $produto['id'] ?>">

            <p>
                <label for="nome">Nome do Produto:</label><br>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </p>
            <p>
                <label for="descricao">Descrição:</label><br>
                <textarea id="descricao" name="descricao"><?= htmlspecialchars($produto['descricao']) ?></textarea>
            </p>
            <p>
                <label for="preco_venda">Preço de Venda (R$):</label><br>
                <input type="number" id="preco_venda" step="0.01" name="preco_venda" value="<?= htmlspecialchars($produto['preco_venda']) ?>" required>
            </p>
            <p>
                <label for="quantidade_estoque">Quantidade em Estoque:</label><br>
                <input type="number" id="quantidade_estoque" name="quantidade_estoque" value="<?= htmlspecialchars($produto['quantidade_estoque']) ?>" required>
            </p>
            <p>
                <label for="codigo_barras">Código de Barras:</label><br>
                <input type="text" id="codigo_barras" name="codigo_barras" value="<?= htmlspecialchars($produto['codigo_barras']) ?>">
            </p>
            <p>
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
            </p>
        </form>

        <hr>

        <h3>Excluir Produto</h3>
        <p>Atenção: esta ação não pode ser desfeita.</p>
        <form action="editar_produto.php" method="POST" onsubmit="return confirm('Tem certeza absoluta que deseja excluir este produto?');">
            <input type="hidden" name="id" value="<?= $produto['id'] ?>">
            <input type="hidden" name="acao" value="excluir">
            <button type="submit" class="btn-excluir">Excluir Produto Permanentemente</button>
        </form>

    </div>
</body>
</html>