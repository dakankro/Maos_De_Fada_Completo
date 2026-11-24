<?php
session_start();
require_once __DIR__ . '/includes/catalogo.php';
// Mostra os produtos mais recentes cadastrados pelo funcionário.
$produtosDestaque = array_slice(array_reverse(array_values(catalogoProdutos())), 0, 4);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mãos de Fada ♡</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php
include __DIR__ . '/includes/header.php';
?>

<!-- Hero -->

<section class="hero">

    <img
            class="hero-img"
            src="https://media.base44.com/images/public/6a1ec21b4bd258439048fb71/c34d8ef96_generated_image.png"
            alt="Materiais para artesanato">

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <div class="hero-card">

            <h1>
                Aqui sua criatividade
                <br>
                ganha vida! ♡
            </h1>

            <p>
                Materiais de qualidade para bordado, crochê,
                costura, pintura e artesanato em geral.
            </p>

            <a href="paginas/produtos/produtos.php" class="hero-btn">
                COMPRE AGORA
            </a>

        </div>

    </div>

</section>

<!-- Categorias -->

<section class="categories" id="categorias">

    <div class="section-title">

        <div class="stitch"></div>

        <h2>
            NOSSAS CATEGORIAS ♡
        </h2>

        <div class="stitch"></div>

    </div>

    <div class="cat-grid">

        <a href="paginas/produtos/linhas.php"
           class="cat-card"
           style="background: var(--rose-light);">

            <span class="cat-icon">🧶</span>

            <span>
                    Linhas e Lãs
                </span>

        </a>

        <a href="paginas/produtos/tecidos.php"
           class="cat-card"
           style="background: var(--lilac-light);">

            <span class="cat-icon">🧵</span>

            <span>
                    Tecidos
                </span>

        </a>

        <a href="paginas/produtos/aviamentos.php"
           class="cat-card"
           style="background: var(--cream);">

            <span class="cat-icon">🪡</span>

            <span>
                    Aviamentos
                </span>

        </a>

        <a href="paginas/produtos/pintura.php"
           class="cat-card"
           style="background: var(--rose-light);">

            <span class="cat-icon">🎨</span>

            <span>
                    Pintura e Decoração
                </span>

        </a>

        <a href="paginas/produtos/artesanato.php"
           class="cat-card"
           style="background: var(--lilac-light);">

            <span class="cat-icon">💝</span>

            <span>
                    Artesanato Criativo
                </span>

        </a>

    </div>

</section>
<!-- Produtos em Destaque -->
<section class="products">
<div class="products-header"><h2>PRODUTOS EM DESTAQUE ♡</h2><a href="paginas/produtos/produtos.php">Ver todos →</a></div>
<div class="prod-grid">
<?php foreach ($produtosDestaque as $produto): ?>
<article class="prod-card">
    <div class="prod-media">
        <?php if (!empty($produto['promocao'])): ?><span class="prod-badge">PROMOÇÃO</span><?php endif; ?>
        <img class="prod-img" src="<?= e(imagemProduto($produto)) ?>" onerror="this.onerror=null;this.src='<?= e(rtrim(BASE_URL, '/')) ?>/assets/img/produtos/sem-imagem.svg';" alt="<?= e($produto['nome']) ?>">
    </div>
    <div class="prod-info">
        <span class="prod-category"><?= e($produto['categoria']) ?></span>
        <h3><?= e($produto['nome']) ?></h3>
        <div class="prod-bottom">
            <div>
                <p class="price"><?= money($produto['preco']) ?></p>
                <p class="prod-stock <?= (int)$produto['estoque'] > 0 ? 'available' : 'unavailable' ?>"><?= (int)$produto['estoque'] > 0 ? '● Em estoque' : '● Esgotado' ?></p>
            </div>
            <?php if ((int)$produto['estoque'] > 0): ?><a href="produtos/produto.php?id=<?= (int)$produto['id'] ?>" class="prod-btn">COMPRAR</a><?php else: ?><span class="prod-btn disabled">ESGOTADO</span><?php endif; ?>
        </div>
    </div>
</article>
<?php endforeach; ?>
</div></section>

<!-- Benefícios -->

<section class="benefits">

    <div class="benefits-grid">


        <div class="benefit">

                <span class="benefit-icon">
                    🚚
                </span>

            <span class="t">
                    Envio rápido
                </span>

            <span class="s">
                    para todo o Brasil
                </span>

        </div>



        <div class="benefit">

                <span class="benefit-icon">
                    ⭐
                </span>

            <span class="t">
                    Produtos
                </span>

            <span class="s">
                    de qualidade
                </span>

        </div>



        <div class="benefit">

                <span class="benefit-icon">
                    🔒
                </span>

            <span class="t">
                    Compra 100%
                </span>

            <span class="s">
                    segura
                </span>

        </div>



        <div class="benefit">

                <span class="benefit-icon">
                    💗
                </span>

            <span class="t">
                    Atendimento
                </span>

            <span class="s">
                    especial
                </span>

        </div>


    </div>

</section>
<?php
include __DIR__ . '/includes/footer.php';
?>



</body>

</html>