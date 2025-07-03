// public/js/vendas.js (versão com MODAL DE CONFIRMAÇÃO)

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. SELEÇÃO DOS ELEMENTOS ---
    // Painel de Venda Principal
    const buscaProdutoInput = document.getElementById('busca-produto');
    const resultadosBuscaDiv = document.getElementById('resultados-busca');
    const quantidadeProdutoInput = document.getElementById('quantidade-produto');
    const btnAddProduto = document.getElementById('btn-add-produto');
    const corpoTabelaVenda = document.getElementById('corpo-tabela-venda'); // ID CORRETO E DEFINITIVO
    const valorTotalSpan = document.getElementById('valor-total');
    const formaPagamentoSelect = document.getElementById('forma-pagamento');
    const valorRecebidoInput = document.getElementById('valor-recebido');
    const valorTrocoSpan = document.getElementById('valor-troco');
    const btnRevisarVenda = document.getElementById('btn-revisar-venda');

    // Modal de Confirmação
    const modalConfirmacao = document.getElementById('modal-confirmacao');
    const resumoModalDiv = document.getElementById('resumo-modal');
    const checkImprimir = document.getElementById('check-imprimir');
    const btnVoltarEditar = document.getElementById('btn-voltar-editar');
    const btnConfirmarSalvar = document.getElementById('btn-confirmar-salvar');
    
    // Outras Telas e Elementos
    const containerVenda = document.getElementById('container-venda');
    const telaSucesso = document.getElementById('tela-sucesso');
    const btnNovaVenda = document.getElementById('btn-nova-venda');
    const areaImpressao = document.getElementById('area-impressao');

    // --- 2. ESTADO DA VENDA ---
    let carrinho = [];
    let ultimaVendaCompleta = null;
    let produtoSelecionadoParaAdicionar = null;

    // --- 3. FUNÇÕES ---

    // As funções de busca (buscarProdutos, mostrarResultadosBusca, selecionarProduto)
    // e de manipulação do carrinho (adicionarProdutoAoCarrinho, atualizarTabelaEValores, etc.)
    // continuam as mesmas da versão anterior. Cole-as aqui.

    // (Vou incluir todas para garantir a integridade do arquivo)
    
    async function buscarProdutos() {
        const termo = buscaProdutoInput.value.trim();
        if (termo.length < 1) {
            resultadosBuscaDiv.classList.add('hidden');
            return;
        }
        try {
            const response = await fetch(`buscar_produtos.php?term=${encodeURIComponent(termo)}`);
            const produtos = await response.json();
            mostrarResultadosBusca(produtos);
        } catch (error) { console.error('Erro ao buscar produtos:', error); }
    }

    function mostrarResultadosBusca(produtos) {
        resultadosBuscaDiv.innerHTML = '';
        if (produtos.length === 0) {
            resultadosBuscaDiv.innerHTML = '<div class="p-2 text-gray-500">Nenhum produto encontrado.</div>';
        } else {
            produtos.forEach(produto => {
                const divItem = document.createElement('div');
                divItem.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                divItem.textContent = `${produto.nome} (Estoque: ${produto.quantidade_estoque})`;
                divItem.dataset.produto = JSON.stringify(produto);
                divItem.addEventListener('click', selecionarProduto);
                resultadosBuscaDiv.appendChild(divItem);
            });
        }
        resultadosBuscaDiv.classList.remove('hidden');
    }

    function selecionarProduto(event) {
        const produtoData = JSON.parse(event.target.dataset.produto);
        buscaProdutoInput.value = produtoData.nome;
        produtoSelecionadoParaAdicionar = produtoData;
        resultadosBuscaDiv.classList.add('hidden');
        quantidadeProdutoInput.focus();
    }

    function adicionarProdutoAoCarrinho() {
        if (!produtoSelecionadoParaAdicionar) {
            alert('Por favor, busque e selecione um produto da lista primeiro.');
            return;
        }
        const quantidade = parseInt(quantidadeProdutoInput.value) || 1;
        if (quantidade <= 0) { return; }
        const itemExistente = carrinho.find(item => item.id === produtoSelecionadoParaAdicionar.id);
        if (itemExistente) {
            itemExistente.quantidade += quantidade;
        } else {
            carrinho.push({ id: produtoSelecionadoParaAdicionar.id, nome: produtoSelecionadoParaAdicionar.nome, preco: parseFloat(produtoSelecionadoParaAdicionar.preco_venda), quantidade: quantidade });
        }
        buscaProdutoInput.value = '';
        quantidadeProdutoInput.value = 1;
        produtoSelecionadoParaAdicionar = null;
        buscaProdutoInput.focus();
        atualizarTabelaEValores();
    }
    
    function atualizarTabelaEValores() {
        corpoTabelaVenda.innerHTML = '';
        let total = 0;
        carrinho.forEach((item, index) => {
            const subtotal = item.preco * item.quantidade;
            total += subtotal;
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="px-4 py-2">${item.nome}</td><td class="px-4 py-2 text-center">${item.quantidade}</td><td class="px-4 py-2">R$ ${item.preco.toFixed(2)}</td><td class="px-4 py-2">R$ ${subtotal.toFixed(2)}</td><td class="px-4 py-2 text-center"><button class="btn-remover text-red-500 hover:text-red-700 font-semibold" data-index="${index}">Remover</button></td>`;
            corpoTabelaVenda.appendChild(tr);
        });
        valorTotalSpan.textContent = total.toFixed(2);
        calcularTroco();
    }

    function removerItemDoCarrinho(event) {
        if (event.target.classList.contains('btn-remover')) {
            const index = parseInt(event.target.dataset.index);
            carrinho.splice(index, 1);
            atualizarTabelaEValores();
        }
    }

    function calcularTroco() {
        const total = parseFloat(valorTotalSpan.textContent);
        const recebido = parseFloat(valorRecebidoInput.value) || 0;
        const troco = recebido - total;
        valorTrocoSpan.textContent = troco >= 0 ? troco.toFixed(2) : '0.00';
    }

    /**
     * NOVO FLUXO: Prepara e exibe o modal de confirmação.
     */
    function revisarVenda() {
        if (carrinho.length === 0) {
            alert('Adicione pelo menos um produto à venda!');
            return;
        }

        const total = parseFloat(valorTotalSpan.textContent);
        const recebido = parseFloat(valorRecebidoInput.value) || 0;
        const formaPagamento = formaPagamentoSelect.value;
        const troco = parseFloat(valorTrocoSpan.textContent);

        if (formaPagamento === 'Dinheiro' && recebido < total) {
            alert('Para pagamento em dinheiro, o valor recebido não pode ser menor que o total da venda!');
            return;
        }

        // Monta o resumo no modal
        resumoModalDiv.innerHTML = `
            <p><strong>Total da Venda:</strong> R$ ${total.toFixed(2)}</p>
            <p><strong>Forma de Pagamento:</strong> ${formaPagamento}</p>
            <p><strong>Valor Recebido:</strong> R$ ${recebido.toFixed(2)}</p>
            <p><strong>Troco:</strong> R$ ${troco.toFixed(2)}</p>
        `;
        
        // Exibe o modal
        modalConfirmacao.classList.remove('hidden');
    }

    /**
     * NOVO FLUXO: Ação final que salva a venda no banco.
     */
    async function confirmarSalvarVenda() {
        // Desabilita o botão para evitar cliques duplos
        btnConfirmarSalvar.disabled = true;
        btnConfirmarSalvar.textContent = 'Salvando...';

        const dadosVenda = {
            carrinho: carrinho,
            valor_total: parseFloat(valorTotalSpan.textContent),
            valor_recebido: parseFloat(valorRecebidoInput.value) || 0,
            troco: parseFloat(valorTrocoSpan.textContent),
            forma_pagamento: formaPagamentoSelect.value
        };

        try {
            const response = await fetch('../vendas/processa_venda.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dadosVenda)
            });
            const resultado = await response.json();

            if (resultado.status === 'success') {
                ultimaVendaCompleta = dadosVenda; // Salva para impressão
                
                if (checkImprimir.checked) {
                    imprimirUltimoRecibo();
                }

                containerVenda.style.display = 'none';
                modalConfirmacao.classList.add('hidden');
                telaSucesso.classList.remove('hidden');
            } else {
                alert('Erro: ' + resultado.message);
            }
        } catch (error) {
            console.error('Erro ao processar a venda:', error);
            alert('Ocorreu um erro de comunicação com o servidor.');
        } finally {
            // Reabilita o botão
            btnConfirmarSalvar.disabled = false;
            btnConfirmarSalvar.textContent = 'Confirmar e Salvar Venda';
        }
    }

    function imprimirUltimoRecibo() {
        if (!ultimaVendaCompleta) { return; }
        // A lógica de montar o recibo continua a mesma aqui
        const nomeLoja = "Nome da Sua Loja";
        const cnpjLoja = "XX.XXX.XXX/0001-XX";
        // ... resto do código de montagem do recibo ...
        areaImpressao.innerHTML = '...'; // Seu HTML do recibo aqui
        window.print();
    }

    function iniciarNovaVenda() {
        // Reseta tudo para o estado inicial
        carrinho = [];
        ultimaVendaCompleta = null;
        valorRecebidoInput.value = '';
        formaPagamentoSelect.value = 'Dinheiro';
        checkImprimir.checked = false;
        atualizarTabelaEValores();

        // Alterna a visibilidade das telas
        telaSucesso.classList.add('hidden');
        containerVenda.style.display = 'block';
    }

    // --- 4. EVENT LISTENERS ---
    buscaProdutoInput.addEventListener('input', buscarProdutos);
    btnAddProduto.addEventListener('click', adicionarProdutoAoCarrinho);
    valorRecebidoInput.addEventListener('input', calcularTroco);
    formaPagamentoSelect.addEventListener('change', calcularTroco);
    corpoTabelaVenda.addEventListener('click', removerItemDoCarrinho);

    // Novos Listeners para o fluxo do modal
    btnRevisarVenda.addEventListener('click', revisarVenda);
    btnVoltarEditar.addEventListener('click', () => modalConfirmacao.classList.add('hidden'));
    btnConfirmarSalvar.addEventListener('click', confirmarSalvarVenda);
    btnNovaVenda.addEventListener('click', iniciarNovaVenda);
    
    // Listener para fechar a busca se clicar fora
    document.addEventListener('click', (event) => {
        if (!resultadosBuscaDiv.contains(event.target) && event.target !== buscaProdutoInput) {
            resultadosBuscaDiv.classList.add('hidden');
        }
    });
});