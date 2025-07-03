<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Vendas</title>
    <link rel="stylesheet" href="public/css/style.css"> </head>
<body>
    <div class="login-container">
        <h2>Acessar o Sistema</h2>
        <form action="auth.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
        <?php
            // Se houver uma mensagem de erro na URL, exiba-a
            if (isset($_GET['erro'])) {
                echo '<p class="error">Email ou senha inválidos!</p>';
            }
        ?>
    </div>
</body>
</html>