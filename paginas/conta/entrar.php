<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/validacao.php';
require_once __DIR__ . '/../../includes/catalogo.php';
require_once __DIR__ . '/../../includes/pedidos.php';

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');

    if (!email_valido($email) || $senha === '') {
        $erro = 'Informe um e-mail e uma senha válidos.';
    } else {
        try {
            $sql = 'SELECT usuarios.id, usuarios.nome, login.senha
                    FROM usuarios
                    INNER JOIN login ON usuarios.id = login.usuario_id
                    WHERE usuarios.email = :email
                    LIMIT 1';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int) $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];

                $destino = (($_GET['redirect'] ?? '') === 'pagamento')
                    ? '../compras/pagamento.php'
                    : '../../index.php';

                header('Location: ' . $destino);
                exit;
            }

            $erro = 'E-mail ou senha inválidos.';
        } catch (PDOException $e) {
            $erro = 'Erro ao consultar o banco de dados.';
        }
    }
}

$logado = !empty($_SESSION['usuario_id']);
$pedidos = $logado ? pedidosDoUsuario((int)$_SESSION['usuario_id']) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta | Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../assets/css/conta-ajustes.css?v=2">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="account-page">
<?php if ($logado): ?>
    <section class="account-hero">
        <div class="account-avatar">♡</div>
        <div>
            <span class="eyebrow">MINHA CONTA</span>
            <h1>Olá, <?= e($_SESSION['usuario_nome']) ?>!</h1>
            <p>Gerencie seus pedidos e acesse as principais áreas da loja.</p>
        </div>
    </section>

    <section class="account-grid">
        <a class="account-card" href="../compras/pedidos.php"><span>📦</span><div><h2>Meus pedidos</h2><p>Acompanhe os pedidos salvos na sua conta.</p></div><b>→</b></a>
        <a class="account-card" href="../compras/carrinho.php"><span>🛍️</span><div><h2>Meu carrinho</h2><p>Veja produtos, quantidades e total da compra.</p></div><b>→</b></a>
        <a class="account-card" href="../produtos/produtos.php"><span>✨</span><div><h2>Continuar comprando</h2><p>Volte para o catálogo e encontre novos produtos.</p></div><b>→</b></a>
        <a class="account-card logout-card" href="logout.php"><span>↪</span><div><h2>Sair da conta</h2><p>Encerrar a sessão atual com segurança.</p></div><b>→</b></a>
    </section>

    <section class="account-orders-preview">
        <div class="section-heading"><div><span class="eyebrow">HISTÓRICO</span><h2>Últimos pedidos</h2></div><a href="../compras/pedidos.php">Ver todos →</a></div>
        <?php if (!$pedidos): ?>
            <div class="empty-state"><span>📦</span><h3>Nenhum pedido ainda</h3><p>Quando você finalizar uma compra, ela aparecerá aqui.</p><a class="hero-btn" href="../produtos/produtos.php">COMEÇAR A COMPRAR</a></div>
        <?php else: ?>
            <div class="order-list">
            <?php foreach (array_slice($pedidos, 0, 3) as $pedido): ?>
                <div class="order-row"><div><strong>Pedido #<?= (int)$pedido['id'] ?></strong><span><?= e(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></span></div><span class="status-pill"><?= e(ucfirst($pedido['status'])) ?></span><strong><?= money($pedido['total']) ?></strong></div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="login-page-card">
        <div class="login-side">
            <span class="account-avatar">♡</span>
            <span class="eyebrow">BEM-VINDO(A)</span>
            <h1>Minha Conta</h1>
            <p>Entre para acessar seus pedidos e finalizar suas compras com mais praticidade.</p>
            <div class="login-benefits"><span>✓ Acompanhe seus pedidos</span><span>✓ Tenha seu carrinho sempre à mão</span><span>✓ Continue suas compras</span></div>
        </div>
        <div class="login-form-area">
            <h2>Entrar</h2>
            <?php if ($erro): ?><div class="error-box"><?= e($erro) ?></div><?php endif; ?>
            <form method="post" class="clean-form">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" maxlength="150" value="<?= e($email) ?>" placeholder="voce@email.com" required>
                <label for="senha">Senha</label>
                <input id="senha" type="password" name="senha" data-validacao="senha" placeholder="Sua senha" required>
                <p class="forgot-password"><a href="esqueci_senha.php">Esqueceu sua senha?</a></p>
                <button class="hero-btn" type="submit">ENTRAR</button>
            </form>
            <div class="login-divider"><span>ou</span></div>
            <p class="create-account">Ainda não tem conta? <a href="login.php">Criar minha conta</a></p>
        </div>
    </section>
<?php endif; ?>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
