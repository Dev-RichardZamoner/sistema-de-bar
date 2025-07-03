<?php
// modulos/vendas/processa_venda.php

// Define o cabeçalho como JSON para a resposta
header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// Resposta padrão de erro
$response = ['status' => 'error', 'message' => 'Ocorreu um erro inesperado.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega o corpo da requisição (que é o nosso JSON enviado pelo JavaScript)
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    // Validação básica dos dados recebidos
    if (!$data || !isset($data->carrinho) || empty($data->carrinho)) {
        $response['message'] = 'Nenhum item na venda.';
        echo json_encode($response);
        exit();
    }

    $carrinho = $data->carrinho;
    $id_usuario = $_SESSION['id_usuario']; // Pega o ID do usuário logado
    $valor_recebido = $data->valor_recebido;
    $troco = $data->troco;
    
    // INICIA A TRANSAÇÃO: OU TUDO FUNCIONA, OU NADA É SALVO.
    try {
        $pdo->beginTransaction();

        // IMPORTANTE: Recalcular o total no backend para segurança.
        // Nunca confie no total enviado pelo cliente.
        $valor_total_backend = 0;
        foreach ($carrinho as $item) {
            $stmt = $pdo->prepare("SELECT preco_venda FROM produtos WHERE id = ?");
            $stmt->execute([$item->id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($produto) {
                $valor_total_backend += $produto['preco_venda'] * $item->quantidade;
            } else {
                throw new Exception("Produto com ID {$item->id} não encontrado.");
            }
        }
        
        // 1. Inserir na tabela `vendas`
        $id_sessao_caixa = $_SESSION['id_sessao_caixa']; // Pega o ID da sessão de caixa atual

$stmt_venda = $pdo->prepare(
    "INSERT INTO vendas (id_usuario, id_sessao_caixa, valor_total, valor_recebido, troco) VALUES (?, ?, ?, ?, ?)"
);
$forma_pagamento = $data->forma_pagamento; // Pega a nova informação

$stmt_venda = $pdo->prepare(
    "INSERT INTO vendas (id_usuario, id_sessao_caixa, valor_total, valor_recebido, troco, forma_pagamento) VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt_venda->execute([$id_usuario, $id_sessao_caixa, $valor_total_backend, $valor_recebido, $troco, $forma_pagamento]);
    
        // Pega o ID da venda que acabamos de inserir
        $id_venda = $pdo->lastInsertId();

        // 2. Inserir na tabela `vendas_itens` e atualizar o estoque
        $stmt_item = $pdo->prepare(
            "INSERT INTO vendas_itens (id_venda, id_produto, quantidade, preco_unitario_venda) VALUES (?, ?, ?, ?)"
        );
        $stmt_update_estoque = $pdo->prepare(
            "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?"
        );

        foreach ($carrinho as $item) {
            // Insere o item da venda
            $stmt_item->execute([$id_venda, $item->id, $item->quantidade, $item->preco]);
            
            // Dá baixa no estoque
            $stmt_update_estoque->execute([$item->quantidade, $item->id]);
        }
        
        // Se tudo ocorreu bem até aqui, confirma as alterações no banco
        $pdo->commit();
        
        $response['status'] = 'success';
        $response['message'] = 'Venda registrada com sucesso!';

    } catch (Exception $e) {
        // Se qualquer erro ocorrer, desfaz todas as alterações
        $pdo->rollBack();
        
        http_response_code(500); // Define um código de erro no servidor
        $response['message'] = 'Falha ao registrar a venda: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

// Envia a resposta final em formato JSON
echo json_encode($response);
?>