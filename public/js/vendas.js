// public/js/vendas.js

document.addEventListener('DOMContentLoaded', () => {

    // --- 1. SELEÇÃO DOS ELEMENTOS DO HTML (DOM) ---
    const buscaProdutoInput = document.getElementById('busca-produto');
    const listaProdutosDataList = document.getElementById('lista-produtos');
    const btnAddProduto = document.getElementById('btn-add-produto');
    const tabelaVendaItens = document.querySelector('#tabela-venda-itens tbody');
    const valorTotalSpan = document.getElementById('valor-total');
    const valorRecebidoInput = document.getElementById('valor-recebido');
    const valorTrocoSpan = document.getElementById('valor-troco');
    const btnFinalizarVenda = document.getElementById('btn-finalizar-venda');

    // --- 2. ESTADO DA VENDA (VARIÁVEIS) ---
    // O carrinho será um array de objetos, onde cada objeto representa um item na venda.
    let carrinho = [];

    // --- 3. FUNÇÕES PRINCIPAIS ---

    /**
     * Adiciona um produto ao carrinho ou incrementa sua quantidade se já existir.
     */
    function adicionarProdutoAoCarrinho() {
        const nomeProduto = buscaProdutoInput.value;
        if (!nomeProduto) return; // Não faz nada se o campo estiver vazio

        // Encontra a <option> correspondente ao nome digitado
        const optionSelecionada = Array.from(listaProdutosDataList.options).find(opt => opt.value === nomeProduto);

        if (!optionSelecionada) {
            alert('Produto não encontrado!');
            return;
        }

        const id = optionSelecionada.dataset.id;
        const preco = parseFloat(optionSelecionada.dataset.preco);

        // Verifica se o produto já está no carrinho
        const itemExistente = carrinho.find(item => item.id === id);

        if (itemExistente) {
            itemExistente.quantidade++;
        } else {
            carrinho.push({ id, nome: nomeProduto, preco, quantidade: 1 });
        }

        // Limpa o input de busca e atualiza a tela
        buscaProdutoInput.value = '';
        atualizarTabelaEValores();
    }

    /**
     * Redesenha a tabela de itens e recalcula os totais.
     */
    function atualizarTabelaEValores() {
        // Limpa a tabela
        tabelaVendaItens.innerHTML = '';
        let total = 0;

        carrinho.forEach((item, index) => {
            const subtotal = item.preco * item.quantidade;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.nome}</td>
                <td>${item.quantidade}</td>
                <td>R$ ${item.preco.toFixed(2)}</td>
                <td>R$ ${subtotal.toFixed(2)}</td>
                <td><button class="btn-remover" data-index="${index}">Remover</button></td>
            `;
            tabelaVendaItens.appendChild(tr);
        });

        valorTotalSpan.textContent = total.toFixed(2);
        calcularTroco(); // Recalcula o troco sempre que o total muda
    }

    /**
     * Calcula e exibe o troco.
     */
    function calcularTroco() {
        const total = parseFloat(valorTotalSpan.textContent);
        const recebido = parseFloat(valorRecebidoInput.value) || 0;
        const troco = recebido - total;

        // Mostra o troco apenas se o valor recebido for maior ou igual ao total
        valorTrocoSpan.textContent = troco >= 0 ? troco.toFixed(2) : '0.00';
    }

    /**
     * Remove um item do carrinho baseado no seu índice.
     */
    function removerItemDoCarrinho(event) {
        if (event.target.classList.contains('btn-remover')) {
            const index = parseInt(event.target.dataset.index);
            carrinho.splice(index, 1); // Remove o item do array
            atualizarTabelaEValores();
        }
    }

    /**
     * Envia os dados da venda para o backend.
     */
    async function finalizarVenda() {
        if (carrinho.length === 0) {
            alert('Adicione pelo menos um produto à venda!');
            return;
        }

        const total = parseFloat(valorTotalSpan.textContent);
        const recebido = parseFloat(valorRecebidoInput.value) || 0;

        if (recebido < total) {
            alert('O valor recebido não pode ser menor que o total da venda!');
            return;
        }

        const dadosVenda = {
            carrinho: carrinho,
            valor_total: total,
            valor_recebido: recebido,
            troco: recebido - total
        };

        try {
            const response = await fetch('../vendas/processa_venda.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dadosVenda)
            });

            const resultado = await response.json();

            if (resultado.status === 'success') {
                alert(resultado.message);
                // Limpa o carrinho e a tela para a próxima venda
                carrinho = [];
                valorRecebidoInput.value = '';
                atualizarTabelaEValores();
            } else {
                alert('Erro: ' + resultado.message);
            }
        } catch (error) {
            console.error('Erro ao processar a venda:', error);
            alert('Ocorreu um erro de comunicação com o servidor.');
        }
    }


    // --- 4. EVENT LISTENERS (OUVINTES DE EVENTOS) ---
    btnAddProduto.addEventListener('click', adicionarProdutoAoCarrinho);
    valorRecebidoInput.addEventListener('input', calcularTroco);
    btnFinalizarVenda.addEventListener('click', finalizarVenda);
    // Adiciona um ouvinte na tabela para pegar cliques nos botões "Remover"
    tabelaVendaItens.addEventListener('click', removerItemDoCarrinho);

});