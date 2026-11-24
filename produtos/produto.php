<?php
session_start();
require_once __DIR__ . '/../includes/catalogo.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id && defined('PRODUTO_ID')) $id = PRODUTO_ID;
$produto = $id ? produtoPorId((int)$id) : null;
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= e($produto['nome']) ?> | Mãos de Fada</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="product-page"><div class="product-detail"><div><img class="product-detail-image" src="<?= e(imagemProduto($produto)) ?>" onerror="this.onerror=null;this.src='<?= e(rtrim(BASE_URL, '/')) ?>/assets/img/produtos/sem-imagem.svg';" alt="<?= e($produto['nome']) ?>"></div><div class="product-detail-info"><span class="product-category"><?= e($produto['categoria']) ?></span><h1><?= e($produto['nome']) ?> ♡</h1><p class="description"><?= e($produto['descricao']) ?></p><div class="product-detail-price"><?= money($produto['preco']) ?></div><span class="<?= $produto['estoque'] > 0 ? 'stock-ok' : 'stock-out' ?>"><?= $produto['estoque'] > 0 ? 'Em estoque: '.(int)$produto['estoque'] : 'Produto esgotado' ?></span><?php if ($produto['estoque'] > 0): ?><div><a class="hero-btn" href="../paginas/compras/carrinho.php?acao=adicionar&id=<?= (int)$produto['id'] ?>">ADICIONAR AO CARRINHO</a></div><?php endif; ?></div></div></main>
<?php include __DIR__ . '/../includes/footer.php'; ?></body></html>
