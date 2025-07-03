<?php
// modulos/estoque/gerenciar_estoque.php (VERSÃO COMPLETA E ATUALIZADA)

require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

$mensagem = '';
$erro = '';

// Lógica para processar o formulário de adição de produtos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco_venda = $_POST['preco_venda'];
    $quantidade_estoque = $_POST['quantidade_estoque'];
    
    // **AQUI ESTÁ A CORREÇÃO PRINCIPAL DO BUG**
    // Se o código de barras não estiver vazio, use o valor. Senão, use NULL.
    $codigo_barras = !empty(trim($_POST['codigo_barras'])) ? trim($_POST['codigo_barras']) : null;

    if (empty($nome) || empty($preco_venda) || !isset($quantidade_estoque)) {
        $erro = "Os campos Nome, Preço e Quantidade são obrigatórios.";
    } else {
        try {
            // **TRATAMENTO DE ERRO ADICIONADO**
            $stmt = $pdo->prepare(
                "INSERT INTO produtos (nome, descricao, preco_venda, quantidade_estoque, codigo_barras) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nome, $descricao, $preco_venda, $quantidade_estoque, $codigo_barras]);
            $mensagem = "Produto adicionado com sucesso!";

        } catch (PDOException $e) {
            // Código '23000' geralmente indica violação de chave única (UNIQUE)
            if ($e->getCode() == 23000) {
                $erro = "Erro: O código de barras informado já está cadastrado.";
            } else {
                $erro = "Erro ao adicionar o produto: " . $e->getMessage();
            }
        }
    }
}

// Lógica para buscar todos os produtos para listar na tabela
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Estoque</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <?php include_once '../../includes/header.php'; ?>

    <main class="container p-4 mx-auto">
        <h2 class="text-2xl font-bold mb-6">Gerenciar Estoque</h2>

        <?php if ($mensagem): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($mensagem) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($erro) ?></span>
            </div>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Adicionar Novo Produto</h3>
                    <form action="gerenciar_estoque.php" method="POST" class="space-y-4">
                        <input type="hidden" name="acao" value="adicionar">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <input type="text" id="nome" name="nome" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div>
                            <label for="preco_venda" class="block text-sm font-medium text-gray-700">Preço de Venda (R$)</label>
                            <input type="number" id="preco_venda" step="0.01" name="preco_venda" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="quantidade_estoque" class="block text-sm font-medium text-gray-700">Quantidade Inicial</label>
                            <input type="number" id="quantidade_estoque" name="quantidade_estoque" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="codigo_barras" class="block text-sm font-medium text-gray-700">Código de Barras (Opcional)</label>
                            <input type="text" id="codigo_barras" name="codigo_barras" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <button type="submit" class="w-full mt-2 p-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">
                                Adicionar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="w-full lg:w-2/3">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Produtos Cadastrados</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3">ID</th>
                                    <th scope="col" class="px-4 py-3">Produto</th>
                                    <th scope="col" class="px-4 py-3">Código Barras</th>
                                    <th scope="col" class="px-4 py-3">Preço</th>
                                    <th scope="col" class="px-4 py-3">Estoque</th>
                                    <th scope="col" class="px-4 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produtos)): ?>
                                    <tr class="bg-white border-b"><td colspan="6" class="px-4 py-4 text-center text-gray-500">Nenhum produto cadastrado.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-4 py-4 font-medium text-gray-900"><?= htmlspecialchars($produto['id']) ?></td>
                                            <td class="px-4 py-4 font-medium text-gray-900"><?= htmlspecialchars($produto['nome']) ?></td>
                                            <td class="px-4 py-4"><?= htmlspecialchars($produto['codigo_barras'] ?: 'N/A') ?></td>
                                            <td class="px-4 py-4">R$ <?= number_format($produto['preco_venda'], 2, ',', '.') ?></td>
                                            <td class="px-4 py-4"><?= htmlspecialchars($produto['quantidade_estoque']) ?></td>
                                            <td class="px-4 py-4">
                                                <a href="editar_produto.php?id=<?= $produto['id'] ?>" class="font-medium text-blue-600 hover:underline">Editar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>