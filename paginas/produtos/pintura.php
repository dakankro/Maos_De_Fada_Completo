<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
$produtos = produtosPorCategoria('Pintura e Decoração');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pintura e Decoração ♡ | Mãos de Fada</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<main class="category-page">
<section class="category-hero rose"><div class="category-icon">🎨</div><span class="eyebrow">MÃOS DE FADA ♡</span><h1>Pintura e Decoração</h1><p>Materiais para pintar, decorar e transformar suas peças com criatividade.</p></section>
<nav class="category-nav" aria-label="Categorias"><a href="linhas.php" class="">🧶 Linhas e Lãs</a><a href="tecidos.php" class="">🧵 Tecidos</a><a href="aviamentos.php" class="">🪡 Aviamentos</a><a href="pintura.php" class="active">🎨 Pintura</a><a href="artesanato.php" class="">💝 Artesanato</a></nav>
<section>
<div class="category-products-head"><div><h2>Produtos de Pintura e Decoração</h2><p>Escolha seus materiais favoritos para o próximo projeto.</p></div><p><?= count($produtos) ?> produto(s)</p></div>
<div class="prod-grid">
<?php foreach ($produtos as $produto): ?>
<article class="prod-card">
    <div class="prod-media">
        <?php if (!empty($produto['promocao'])): ?><span class="prod-badge">PROMOÇÃO</span><?php endif; ?>
        <img class="prod-img" src="<?= e(imagemProduto($produto)) ?>" alt="<?= e($produto['nome']) ?>">
    </div>
    <div class="prod-info">
        <span class="prod-category"><?= e($produto['categoria']) ?></span>
        <h3><?= e($produto['nome']) ?></h3>
        <p class="prod-description"><?= e($produto['descricao']) ?></p>
        <div class="prod-bottom">
            <div>
                <p class="price"><?= money($produto['preco']) ?></p>
                <p class="prod-stock <?= (int)$produto['estoque'] > 0 ? 'available' : 'unavailable' ?>"><?= (int)$produto['estoque'] > 0 ? '● Em estoque' : '● Esgotado' ?></p>
            </div>
            <?php if ((int)$produto['estoque'] > 0): ?>
                <a href="../../produtos/produto.php?id=<?= (int)$produto['id'] ?>" class="prod-btn">VER PRODUTO</a>
            <?php else: ?>
                <span class="prod-btn disabled">ESGOTADO</span>
            <?php endif; ?>
        </div>
    </div>
</article>
<?php endforeach; ?>
<?php if (!$produtos): ?><div class="category-empty"><strong>Ainda não temos produtos nesta categoria.</strong><p>Em breve teremos novidades para você. ♡</p></div><?php endif; ?>
</div></section>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body></html>