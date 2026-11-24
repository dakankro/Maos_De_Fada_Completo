<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
$produtos = array_values(array_filter(catalogoProdutos(), fn($p) => $p['promocao']));
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Promoções ♡ | Mãos de Fada</title><link rel="stylesheet" href="../../assets/css/style.css"></head><body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<section class="products"><div class="products-header"><h2>Promoções ♡</h2></div><div class="prod-grid">
<?php foreach ($produtos as $produto): ?>
<article class="prod-card">
    <div class="prod-media">
        <?php if (!empty($produto['promocao'])): ?><span class="prod-badge">PROMOÇÃO</span><?php endif; ?>
        <img class="prod-img" src="<?= e(imagemProduto($produto)) ?>" alt="<?= e($produto['nome']) ?>">
    </div>
    <div class="prod-info">
        <span class="prod-category"><?= e($produto['categoria']) ?></span>
        <h3><?= e($produto['nome']) ?></h3>
        <div class="prod-bottom">
            <div>
                <p class="price"><?= money($produto['preco']) ?></p>
                <p class="prod-stock <?= (int)$produto['estoque'] > 0 ? 'available' : 'unavailable' ?>"><?= (int)$produto['estoque'] > 0 ? '● Em estoque' : '● Esgotado' ?></p>
            </div>
            <?php if ((int)$produto['estoque'] > 0): ?><a href="../../produtos/produto.php?id=<?= (int)$produto['id'] ?>" class="prod-btn">VER PRODUTO</a><?php else: ?><span class="prod-btn disabled">ESGOTADO</span><?php endif; ?>
        </div>
    </div>
</article>
<?php endforeach; ?>
<?php if (!$produtos): ?><p>Nenhum produto encontrado nesta categoria.</p><?php endif; ?>
</div></section><?php include __DIR__ . '/../../includes/footer.php'; ?></body></html>
