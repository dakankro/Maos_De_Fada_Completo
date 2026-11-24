<?php
session_start();
require_once __DIR__ . '/includes/catalogo.php';
require_once __DIR__ . '/includes/validacao.php';

$termo = texto_limpo(trim((string)($_GET['q'] ?? '')));
$produtos = [];
$erro = '';

if ($termo !== '' && !preg_match('/^[\p{L}\p{N}\s\-]+$/u', $termo)) {
    $erro = 'Use apenas letras, números, espaços ou hífen na pesquisa.';
    $termo = '';
}

if ($termo !== '') {
    $like = '%' . $termo . '%';
    $stmt = $pdo->prepare(
        'SELECT p.id, p.nome, p.descricao, p.preco, p.estoque, p.imagem,
                p.promocao, c.nome AS categoria, c.id AS categoria_id
         FROM produtos p
         INNER JOIN categorias c ON p.categoria_id = c.id
         WHERE p.nome LIKE :nome
            OR p.descricao LIKE :descricao
            OR c.nome LIKE :categoria
         ORDER BY p.nome'
    );
    $stmt->execute([
        ':nome' => $like,
        ':descricao' => $like,
        ':categoria' => $like,
    ]);
    $produtos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesquisa | Mãos de Fada</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>
<main class="products" style="min-height:60vh;">
    <div class="products-header"><h2>Resultados da pesquisa ♡</h2></div>
    <?php if ($termo === '' && !$erro): ?>
        <p>Digite um produto no campo de pesquisa.</p>
    <?php elseif ($erro): ?>
        <p><?= e($erro) ?></p>
    <?php elseif (!$produtos): ?>
        <p>Nenhum produto encontrado para <strong><?= e($termo) ?></strong>.</p>
    <?php else: ?>
        <p>Encontramos <?= count($produtos) ?> produto(s) para <strong><?= e($termo) ?></strong>.</p>
        <div class="prod-grid" style="margin-top:25px;">
        <?php foreach ($produtos as $produto): ?>
            <div class="prod-card">
                <img class="prod-img" src="<?= e(imagemProduto($produto)) ?>" alt="<?= e($produto['nome']) ?>" onerror="this.onerror=null;this.src='<?= e(rtrim(BASE_URL, '/')) ?>/assets/img/produtos/sem-imagem.svg';">
                <div class="prod-info">
                    <h3><?= e($produto['nome']) ?></h3>
                    <p><?= e($produto['categoria']) ?></p>
                    <p class="price"><?= money($produto['preco']) ?></p>
                    <?php if ((int)$produto['estoque'] > 0): ?><a href="produtos/produto.php?id=<?= (int)$produto['id'] ?>" class="prod-btn">VER PRODUTO</a><?php else: ?><span class="prod-btn" style="display:block;text-align:center;opacity:.55">ESGOTADO</span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
