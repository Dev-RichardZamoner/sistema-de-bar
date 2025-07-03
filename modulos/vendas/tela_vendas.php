<?php
require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa - Tela de Vendas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS para a impressão, sem alterações */
        @media print {
            body * { visibility: hidden; }
            #area-impressao, #area-impressao * { visibility: visible; }
            #area-impressao { position: absolute; left: 0; top: 0; width: 100%; font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100" data-nome-operador="<?= htmlspecialchars($_SESSION['nome_usuario']) ?>">
    
    <?php include_once '../../includes/header.php'; ?>

    <main id="container-venda" class="container p-4 mx-auto">
        <div class="flex flex-col lg:flex-row gap-6">
            
            <div class="w-full lg:w-2/3 space-y-6">
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Adicionar Produto</h3>
                    <div class="relative">
                        <div class="flex flex-col sm:flex-row items-center gap-2">
                            <input type="text" id="busca-produto" placeholder="Digite o nome ou código do produto" autocomplete="off" class="flex-grow w-full p-2 border border-gray-300 rounded-md">
                            <input type="number" id="quantidade-produto" value="1" min="1" class="w-full sm:w-24 p-2 text-center border border-gray-300 rounded-md">
                            <button id="btn-add-produto" class="w-full sm:w-auto p-2 bg-blue-600 text-white rounded-md">Adicionar</button>
                        </div>
                        <div id="resultados-busca" class="absolute z-10 w-full bg-white border rounded-md mt-1 shadow-lg hidden"></div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Itens da Venda</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Produto</th>
                                    <th class="px-4 py-3">Qtd</th>
                                    <th class="px-4 py-3">Preço Unit.</th>
                                    <th class="px-4 py-3">Subtotal</th>
                                    <th class="px-4 py-3">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="corpo-tabela-venda"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 rounded-lg shadow-md lg:sticky lg:top-4">
                    <h3 class="text-xl font-bold">Resumo da Venda</h3>
                    <div class="mt-4"><p class="text-gray-500">Total</p><p class="text-4xl font-extrabold">R$ <span id="valor-total">0.00</span></p></div>
                    <hr class="my-6">
                    <div class="space-y-4">
                        <div>
                            <label for="forma-pagamento" class="block text-sm font-medium">Forma de Pagamento</label>
                            <select id="forma-pagamento" class="mt-1 block w-full p-2 border rounded-md"><option>Dinheiro</option><option>Cartão de Crédito</option><option>Cartão de Débito</option><option>Pix</option></select>
                        </div>
                        <div>
                            <label for="valor-recebido" class="block text-sm font-medium">Valor Recebido (R$)</label>
                            <input type="number" id="valor-recebido" step="0.01" placeholder="0.00" class="mt-1 block w-full p-2 border rounded-md">
                        </div>
                        <div><p class="text-gray-500">Troco</p><p class="text-2xl font-bold">R$ <span id="valor-troco">0.00</span></p></div>
                    </div>
                    <div class="mt-8">
                        <button id="btn-revisar-venda" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700">REVISAR E FINALIZAR</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <div id="modal-confirmacao" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-4">Revisão da Venda</h2>
            <div id="resumo-modal" class="text-left mb-6">
                </div>
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="check-imprimir" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <span class="ml-2 text-gray-700">Imprimir Recibo após Salvar</span>
                </label>
            </div>
            <div class="flex justify-end gap-4">
                <button id="btn-voltar-editar" class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Voltar e Editar</button>
                <button id="btn-confirmar-salvar" class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">Confirmar e Salvar Venda</button>
            </div>
        </div>
    </div>

    <div id="tela-sucesso" class="hidden fixed inset-0 bg-white flex flex-col items-center justify-center">
        <h2 class="text-3xl font-bold text-green-700 mb-4">Venda Finalizada com Sucesso!</h2>
        <button id="btn-nova-venda" class="mt-6 px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">Iniciar Nova Venda</button>
    </div>

    <div id="area-impressao" class="hidden"></div>
    
    <script src="../../public/js/vendas.js"></script>
</body>
</html>