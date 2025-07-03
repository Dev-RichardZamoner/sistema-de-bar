<?php
require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';
include_once '../../includes/header.php';

// Buscar produtos para a busca
$stmt = $pdo->query("SELECT id, nome, preco_venda, quantidade_estoque FROM produtos ORDER BY nome");
$produtos_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Caixa - Tela de Vendas</title>
    <style>
        /* Estilos básicos para o layout */
        .container { display: flex; gap: 20px; }
        .venda-painel { flex: 2; }
        .resumo-painel { flex: 1; border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <h2>Tela de Vendas - Caixa Aberto</h2>
    <p>Operador: <?= htmlspecialchars($_SESSION['nome_usuario']) ?></p>

    <div class="container">
        <div class="venda-painel">
            <h3>Adicionar Produto à Venda</h3>
            <div>
                <label for="busca-produto">Buscar Produto:</label>
                <input type="text" id="busca-produto" list="lista-produtos" placeholder="Digite o nome do produto">
                <datalist id="lista-produtos">
                    <?php foreach ($produtos_disponiveis as $p): ?>
                        <option value="<?= htmlspecialchars($p['nome']) ?>" data-id="<?= $p['id'] ?>" data-preco="<?= $p['preco_venda'] ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <button id="btn-add-produto">Adicionar</button>
            </div>
            
            <hr>
            <h3>Itens da Venda</h3>
            <table id="tabela-venda-itens" border="1" width="100%">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
        <div class="resumo-painel">
            <h3>Resumo da Venda</h3>
            <h2>Total: R$ <span id="valor-total">0.00</span></h2>
            <hr>
            <div>
                <label for="valor-recebido">Valor Recebido (R$):</label>
                <input type="number" id="valor-recebido" step="0.01" placeholder="0.00">
            </div>
            <h3>Troco: R$ <span id="valor-troco">0.00</span></h3>
            <hr>
            <button id="btn-finalizar-venda" style="width: 100%; padding: 15px; background-color: green; color: white;">FINALIZAR VENDA</button>
        </div>
    </div>
    
    <script src="../../public/js/vendas.js"></script>
</body>
</html>