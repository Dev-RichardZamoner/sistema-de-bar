<style>
    .navbar { background-color: #f2f2f2; padding: 10px; margin-bottom: 20px; border-bottom: 1px solid #ccc; }
    .navbar a { margin-right: 15px; text-decoration: none; color: #333; font-weight: bold; }
    .navbar a:hover { color: #000; }
</style>
<nav class="navbar">
    <a href="/sistema-estoque-vendas/dashboard.php">Dashboard</a>
    <a href="/sistema-estoque-vendas/modulos/vendas/tela_vendas.php">Caixa / Vendas</a>
    <a href="/sistema-estoque-vendas/modulos/estoque/gerenciar_estoque.php">Gerenciar Estoque</a>
    
    <?php if (isset($_SESSION['cargo_usuario']) && $_SESSION['cargo_usuario'] === 'admin'): ?>
    <a href="/sistema-estoque-vendas/modulos/usuarios/gerenciar_usuarios.php">Gerenciar Usuários</a>
    <a href="/sistema-estoque-vendas/modulos/admin/relatorio_caixas.php">Relatório de Caixas</a>
<?php endif; ?>
    <a href="/sistema-estoque-vendas/logout.php" style="float: right;">Sair</a>
</nav>