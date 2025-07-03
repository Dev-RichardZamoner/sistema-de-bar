<?php
// modulos/vendas/buscar_produtos.php (versão revisada e corrigida)

// Requer o arquivo de configuração do banco de dados.
// O caminho ../../ sobe duas pastas para chegar na raiz do projeto e encontrar a pasta config.
require_once '../../config/db.php';

// Define o cabeçalho da resposta como JSON, essencial para o JavaScript entender os dados.
header('Content-Type: application/json');

// Pega o termo de busca enviado pelo JavaScript.
$term = isset($_GET['term']) ? $_GET['term'] : '';

// **CORREÇÃO**: Agora busca mesmo com 1 caractere. Se o termo estiver vazio, retorna um array vazio.
if (empty($term)) {
    echo json_encode([]);
    exit();
}

try {
    // Prepara a query para buscar tanto no nome quanto no código de barras.
    $stmt = $pdo->prepare(
        "SELECT id, nome, preco_venda, quantidade_estoque, codigo_barras 
         FROM produtos 
         WHERE nome LIKE ? OR codigo_barras LIKE ?
         LIMIT 10"
    );
    
    // O '%' é um coringa do SQL que significa "qualquer sequência de caracteres".
    // Então, '%coca%' busca por qualquer produto que contenha "coca" no nome.
    $searchTerm = '%' . $term . '%';
    $stmt->execute([$searchTerm, $searchTerm]);
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Retorna os produtos encontrados em formato JSON.
    echo json_encode($produtos);

} catch (PDOException $e) {
    // Em caso de erro no banco de dados, retorna um array vazio e loga o erro (idealmente).
    // Isso evita que a aplicação quebre na frente do usuário.
    http_response_code(500); 
    echo json_encode(['erro' => 'Falha ao buscar produtos']);
}
?>