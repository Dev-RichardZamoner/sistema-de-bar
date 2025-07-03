<?php
// Inclui os arquivos necessários
require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// Lógica para processar o formulário de adição/edição de produtos (quando enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco_venda = $_POST['preco_venda'];
    $quantidade_estoque = $_POST['quantidade_estoque'];
    $codigo_barras = $_POST['codigo_barras'];

    // Prepara e executa a inserção no banco
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco_venda, quantidade_estoque, codigo_barras) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $descricao, $preco_venda, $quantidade_estoque, $codigo_barras]);

    // Redireciona para a mesma página para evitar reenvio do formulário
    header("Location: gerenciar_estoque.php");
    exit();
}

// Lógica para buscar todos os produtos para listar na tabela
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Estoque</title>
    </head>
<body>
    <?php include_once '../../includes/header.php'; // Um cabeçalho comum com menu, se houver ?>

    <div class="container">
        <h2>Gerenciar Estoque</h2>

        <h3>Adicionar Novo Produto</h3>
        <form action="gerenciar_estoque.php" method="POST">
            <input type="text" name="nome" placeholder="Nome do Produto" required>
            <textarea name="descricao" placeholder="Descrição"></textarea>
            <input type="number" step="0.01" name="preco_venda" placeholder="Preço de Venda (R$)" required>
            <input type="number" name="quantidade_estoque" placeholder="Quantidade Inicial" required>
            <input type="text" name="codigo_barras" placeholder="Código de Barras (Opcional)">
            <button type="submit">Adicionar Produto</button>
        </form>

        <hr>

        <h3>Produtos Cadastrados</h3>
        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço (R$)</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= htmlspecialchars($produto['id']) ?></td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= number_format($produto['preco_venda'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($produto['quantidade_estoque']) ?></td>
                    <td>
                        <a href="editar_produto.php?id=<?= $produto['id'] ?>">Editar</a>
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>