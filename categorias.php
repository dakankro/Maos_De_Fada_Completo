<?php
session_start();
require_once __DIR__ . '/includes/catalogo.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias | Mãos de Fada ♡</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="categories-page">

    <section class="categories-hero">
        <div class="categories-hero-content">
            <span class="categories-kicker">MÃOS DE FADA ♡</span>
            <h1>Encontre a categoria<br>ideal para seu projeto</h1>
            <p>
                Explore nossos materiais para crochê, tricô, costura,
                pintura e artesanato e encontre tudo para colocar
                suas ideias em prática.
            </p>
        </div>
    </section>

    <section class="categories-list">
        <div class="categories-title">
            <div class="stitch"></div>
            <div>
                <span>EXPLORE</span>
                <h2>Nossas Categorias ♡</h2>
            </div>
            <div class="stitch"></div>
        </div>

        <div class="categories-page-grid">

            <a href="paginas/produtos/linhas.php" class="category-large category-rose">
                <span class="category-large-icon">🧶</span>
                <div>
                    <h3>Linhas e Lãs</h3>
                    <p>Para crochê, tricô, bordado e outros trabalhos com fios.</p>
                </div>
                <span class="category-link">Ver produtos →</span>
            </a>

            <a href="paginas/produtos/tecidos.php" class="category-large category-lilac">
                <span class="category-large-icon">🧵</span>
                <div>
                    <h3>Tecidos</h3>
                    <p>Opções para costura, artesanato e trabalhos personalizados.</p>
                </div>
                <span class="category-link">Ver produtos →</span>
            </a>

            <a href="paginas/produtos/aviamentos.php" class="category-large category-cream">
                <span class="category-large-icon">🪡</span>
                <div>
                    <h3>Aviamentos</h3>
                    <p>Botões, zíperes, agulhas e acessórios para seus projetos.</p>
                </div>
                <span class="category-link">Ver produtos →</span>
            </a>

            <a href="paginas/produtos/pintura.php" class="category-large category-rose">
                <span class="category-large-icon">🎨</span>
                <div>
                    <h3>Pintura e Decoração</h3>
                    <p>Materiais para pintar, decorar e personalizar suas peças.</p>
                </div>
                <span class="category-link">Ver produtos →</span>
            </a>

            <a href="paginas/produtos/artesanato.php" class="category-large category-lilac">
                <span class="category-large-icon">💝</span>
                <div>
                    <h3>Artesanato Criativo</h3>
                    <p>Materiais e acessórios para dar vida às suas ideias.</p>
                </div>
                <span class="category-link">Ver produtos →</span>
            </a>

        </div>
    </section>

    <section class="categories-bottom">
        <h2>Pronto para criar? ♡</h2>
        <p>Confira todos os nossos produtos e encontre o material perfeito para seu próximo projeto.</p>
        <a href="paginas/produtos/produtos.php" class="hero-btn">VER TODOS OS PRODUTOS</a>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
