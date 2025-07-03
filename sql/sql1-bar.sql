-- Tabela para os funcionários/usuários do sistema
CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL, -- Usaremos hash para segurança
  `cargo` ENUM('admin', 'vendedor') NOT NULL DEFAULT 'vendedor',
  `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para os produtos e controle de estoque
CREATE TABLE `produtos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo_barras` VARCHAR(100) UNIQUE,
  `nome` VARCHAR(200) NOT NULL,
  `descricao` TEXT,
  `preco_venda` DECIMAL(10, 2) NOT NULL,
  `quantidade_estoque` INT NOT NULL DEFAULT 0,
  `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para registrar cada venda
CREATE TABLE `vendas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL, -- Quem realizou a venda
  `valor_total` DECIMAL(10, 2) NOT NULL,
  `valor_recebido` DECIMAL(10, 2) NOT NULL,
  `troco` DECIMAL(10, 2) NOT NULL,
  `data_venda` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`)
);

-- Tabela de ligação entre vendas e produtos
CREATE TABLE `vendas_itens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venda` INT NOT NULL,
  `id_produto` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario_venda` DECIMAL(10, 2) NOT NULL, -- Preço no momento da venda
  FOREIGN KEY (`id_venda`) REFERENCES `vendas`(`id`),
  FOREIGN KEY (`id_produto`) REFERENCES `produtos`(`id`)
);

INSERT INTO `usuarios` (`nome`, `email`, `senha`, `cargo`)
VALUES ('Administrador', 'admin@sistema.com', '$2y$10$5jcCODYJEmEGZmtSJtIBKeGQyXKM2t0rlBKQadZlTVZVIm2otXI56', 'admin');