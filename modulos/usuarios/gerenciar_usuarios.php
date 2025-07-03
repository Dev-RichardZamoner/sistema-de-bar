<?php
require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// VERIFICAÇÃO DE SEGURANÇA ADICIONAL: SÓ ADMIN PODE ACESSAR
if ($_SESSION['cargo_usuario'] !== 'admin') {
    echo "<h1>Acesso Negado!</h1>";
    echo "<p>Você não tem permissão para acessar esta página.</p>";
    exit();
}

// Lógica para adicionar/editar usuários (similar à de produtos)
// ... aqui entraria o código para processar o formulário ...
// Lembre-se de usar password_hash() para a senha!

// Lógica para buscar os usuários
$stmt = $pdo->query("SELECT id, nome, email, cargo FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
</head>
<body>
    <h2>Gerenciar Usuários</h2>

    <h3>Adicionar Novo Usuário</h3>
    <form action="gerenciar_usuarios.php" method="POST">
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="email" name="email" placeholder="Email de Login" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <select name="cargo">
            <option value="vendedor">Vendedor</option>
            <option value="admin">Administrador</option>
        </select>
        <button type="submit">Criar Usuário</button>
    </form>
    
    <hr>
    
    <h3>Usuários Cadastrados</h3>
    <table border="1" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['id']) ?></td>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['cargo']) ?></td>
                <td><a href="editar_usuario.php?id=<?= $usuario['id'] ?>">Editar</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>