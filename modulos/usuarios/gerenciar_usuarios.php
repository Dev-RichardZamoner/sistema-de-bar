<?php
// modulos/usuarios/gerenciar_usuarios.php

require_once '../../config/db.php';
require_once '../../includes/verificar_login.php';

// ===================================================================
// || PASSO DE SEGURANÇA MAIS IMPORTANTE DESTA PÁGINA               ||
// ===================================================================
// || Verifica se o cargo do usuário logado é 'admin'.              ||
// || Se não for, exibe uma mensagem de acesso negado e encerra o   ||
// || script. Isso impede que vendedores acessem esta página.       ||
// ===================================================================
if ($_SESSION['cargo_usuario'] !== 'admin') {
    // Pode criar uma página de "acesso_negado.php" mais bonita se quiser
    die("<h1>Acesso Negado!</h1><p>Você não tem permissão para acessar esta página.</p>");
}

// --- LÓGICA PARA CADASTRAR NOVO USUÁRIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cargo = $_POST['cargo'];

    // Validação simples (pode ser melhorada)
    if (empty($nome) || empty($email) || empty($senha) || empty($cargo)) {
        $erro = "Todos os campos são obrigatórios!";
    } else {
        // **SEGURANÇA:** Cria o hash da senha antes de salvar no banco
        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, cargo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senha_hashed, $cargo]);
            
            header("Location: gerenciar_usuarios.php?mensagem=sucesso");
            exit();

        } catch (PDOException $e) {
            // Código '23000' é de violação de integridade (ex: email duplicado)
            if ($e->getCode() == 23000) {
                $erro = "Este email já está cadastrado!";
            } else {
                $erro = "Erro ao cadastrar usuário: " . $e->getMessage();
            }
        }
    }
}

// --- LÓGICA PARA LISTAR OS USUÁRIOS (GET) ---
$stmt_usuarios = $pdo->query("SELECT id, nome, email, cargo FROM usuarios ORDER BY nome ASC");
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
</head>
<body>
    <?php include_once '../../includes/header.php'; ?>

    <div class="container">
        <h2>Gerenciar Usuários</h2>

        <h3>Adicionar Novo Usuário</h3>

        <?php if (isset($erro)): ?>
            <p style="color: red;"><?= $erro ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['mensagem']) && $_GET['mensagem'] === 'sucesso'): ?>
            <p style="color: green;">Usuário cadastrado com sucesso!</p>
        <?php endif; ?>

        <form action="gerenciar_usuarios.php" method="POST">
            <p>
                <label for="nome">Nome Completo:</label><br>
                <input type="text" id="nome" name="nome" required>
            </p>
            <p>
                <label for="email">Email (para login):</label><br>
                <input type="email" id="email" name="email" required>
            </p>
            <p>
                <label for="senha">Senha:</label><br>
                <input type="password" id="senha" name="senha" required>
            </p>
            <p>
                <label for="cargo">Cargo:</label><br>
                <select id="cargo" name="cargo" required>
                    <option value="vendedor">Vendedor</option>
                    <option value="admin">Administrador</option>
                </select>
            </p>
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['cargo']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>