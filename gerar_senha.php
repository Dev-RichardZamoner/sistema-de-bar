<?php
// gerar_senha.php

// A senha que queremos usar para o nosso admin
$senhaEmTextoPuro = 'admin123';

// A função mágica do PHP que cria o hash seguro
// PASSWORD_DEFAULT é a recomendação atual, ele usa o algoritmo mais forte disponível
$hashDaSenha = password_hash($senhaEmTextoPuro, PASSWORD_DEFAULT);

// Exibe o resultado na tela
echo $hashDaSenha;
?>