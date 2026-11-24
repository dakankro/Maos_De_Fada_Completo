<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós | Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="institutional-page">
    <section class="page-hero soft-rose">
        <span class="eyebrow">NOSSA HISTÓRIA</span>
        <h1>Sobre a Mãos de Fada ♡</h1>
        <p>Um cantinho feito para quem transforma ideias em peças únicas.</p>
    </section>

    <section class="about-layout">
        <div class="about-card about-main-card">
            <span class="about-icon">🧶</span>
            <h2>Criatividade em cada detalhe</h2>
            <p>A Mãos de Fada foi criada para reunir materiais que ajudam artesãos a colocar a criatividade em prática. Aqui você encontra opções para crochê, tricô, costura, pintura e diferentes projetos artesanais.</p>
            <p>Nosso objetivo é tornar sua experiência de compra simples, acolhedora e organizada, desde a escolha do produto até a finalização do pedido.</p>
        </div>

        <div class="about-side-grid">
            <article class="about-card">
                <span class="mini-icon">💗</span>
                <h3>Feito com carinho</h3>
                <p>Uma experiência delicada, pensada para quem ama criar.</p>
            </article>
            <article class="about-card">
                <span class="mini-icon">✨</span>
                <h3>Para criar</h3>
                <p>Produtos organizados por categorias para facilitar sua busca.</p>
            </article>
            <article class="about-card">
                <span class="mini-icon">🤝</span>
                <h3>Atendimento</h3>
                <p>Um espaço para tirar dúvidas e receber ajuda quando precisar.</p>
            </article>
            <article class="about-card">
                <span class="mini-icon">🎨</span>
                <h3>Inspiração</h3>
                <p>Materiais para transformar ideias em projetos artesanais.</p>
            </article>
        </div>
    </section>

    <section class="about-cta">
        <div>
            <span class="eyebrow">VAMOS CRIAR?</span>
            <h2>Encontre materiais para o seu próximo projeto.</h2>
        </div>
        <a href="../produtos/produtos.php" class="hero-btn">VER PRODUTOS</a>
    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
