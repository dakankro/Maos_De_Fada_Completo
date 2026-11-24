<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';

if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
$catalogo = catalogoProdutos();
$acao = $_GET['acao'] ?? '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($acao === 'adicionar' && $id) {
    $produto = produtoPorId((int)$id);
    if ($produto && $produto['estoque'] > 0) {
        $atual = (int)($_SESSION['carrinho'][$id] ?? 0);
        $_SESSION['carrinho'][$id] = min($atual + 1, (int)$produto['estoque']);
    }
    header('Location: carrinho.php'); exit;
}
if ($acao === 'remover' && $id) { unset($_SESSION['carrinho'][$id]); header('Location: carrinho.php'); exit; }
if ($acao === 'limpar') { $_SESSION['carrinho'] = []; header('Location: carrinho.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    foreach (($_POST['quantidade'] ?? []) as $pid => $quantidade) {
        if (!ctype_digit((string)$pid) || !ctype_digit((string)$quantidade)) continue;
        $pid = (int)$pid;
        $quantidade = (int)$quantidade;
        if (!isset($catalogo[$pid]) || $quantidade <= 0) unset($_SESSION['carrinho'][$pid]);
        else $_SESSION['carrinho'][$pid] = min($quantidade, (int)$catalogo[$pid]['estoque']);
    }
    header('Location: carrinho.php'); exit;
}

$produtos = [];
$total = 0;
$totalItens = 0;
foreach ($_SESSION['carrinho'] as $pid => $quantidade) {
    $pid = (int)$pid;
    $quantidade = (int)$quantidade;
    if (!isset($catalogo[$pid]) || $quantidade <= 0) { unset($_SESSION['carrinho'][$pid]); continue; }
    $quantidade = min($quantidade, (int)$catalogo[$pid]['estoque']);
    $_SESSION['carrinho'][$pid] = $quantidade;
    $produtos[$pid] = $catalogo[$pid];
    $total += $catalogo[$pid]['preco'] * $quantidade;
    $totalItens += $quantidade;
}
$frete = $total >= 150 || $total === 0 ? 0 : 15;
$final = $total + $frete;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho | Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="cart-page">
    <section class="page-hero compact-hero">
        <span class="eyebrow">SEU PEDIDO</span>
        <h1>Meu Carrinho ♡</h1>
        <p><?= $totalItens ?> <?= $totalItens === 1 ? 'item selecionado' : 'itens selecionados' ?></p>
    </section>

    <?php if (!$produtos): ?>
        <section class="empty-cart-card">
            <div class="empty-cart-icon">🛍️</div>
            <h2>Seu carrinho está vazio</h2>
            <p>Que tal explorar nossos produtos e encontrar algo para o seu próximo projeto?</p>
            <a href="../produtos/produtos.php" class="hero-btn">CONTINUAR COMPRANDO</a>
        </section>
    <?php else: ?>
        <section class="cart-layout">
            <div class="cart-products-card">
                <div class="cart-card-title"><h2>Produtos</h2><span><?= $totalItens ?> <?= $totalItens === 1 ? 'item' : 'itens' ?></span></div>
                <form method="post">
                    <input type="hidden" name="atualizar" value="1">
                    <?php foreach ($produtos as $pid => $produto): $q = (int)$_SESSION['carrinho'][$pid]; $subtotal = $produto['preco'] * $q; ?>
                        <article class="cart-product-row">
                            <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>">
                            <div class="cart-product-info">
                                <span class="cart-category"><?= e($produto['categoria']) ?></span>
                                <h3><?= e($produto['nome']) ?></h3>
                                <span class="cart-unit-price"><?= money($produto['preco']) ?> cada</span>
                                <a class="remove-link" href="carrinho.php?acao=remover&id=<?= $pid ?>">Remover</a>
                            </div>
                            <div class="quantity-control">
                                <label for="q<?= $pid ?>">Quantidade</label>
                                <input id="q<?= $pid ?>" type="number" name="quantidade[<?= $pid ?>]" value="<?= $q ?>" min="1" max="<?= (int)$produto['estoque'] ?>">
                            </div>
                            <strong class="cart-subtotal"><?= money($subtotal) ?></strong>
                        </article>
                    <?php endforeach; ?>
                    <div class="cart-update"><button class="outline-btn" type="submit">ATUALIZAR QUANTIDADES</button><a class="clear-link" href="carrinho.php?acao=limpar">Limpar carrinho</a></div>
                </form>
            </div>

            <aside class="cart-summary">
                <h2>Resumo da compra</h2>
                <div class="summary-line"><span>Subtotal</span><strong><?= money($total) ?></strong></div>
                <div class="summary-line"><span>Frete</span><strong><?= $frete === 0 ? 'Grátis' : money($frete) ?></strong></div>
                <?php if ($frete > 0): ?><p class="shipping-note">Frete grátis em compras acima de R$ 150,00.</p><?php endif; ?>
                <div class="summary-total"><span>Total</span><strong><?= money($final) ?></strong></div>
                <a href="pagamento.php" class="hero-btn checkout-btn">FINALIZAR COMPRA</a>
                <a href="../produtos/produtos.php" class="continue-link">← Continuar comprando</a>
            </aside>
        </section>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
