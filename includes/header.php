<?php
require_once __DIR__ . '/config.php';
// Header compartilhado do site Mãos de Fada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$qtdCarrinho = array_sum($_SESSION['carrinho'] ?? []);

$nomeConta = $_SESSION['usuario_nome'] ?? null;
$quantidadeCarrinho = isset($_SESSION['carrinho'])
    ? array_sum(array_map('intval', $_SESSION['carrinho']))
    : 0;
?>
<!-- Barra Superior -->
<div class="topbar">
    Frete grátis para compras acima de R$150,00 ♡
</div>

<!-- Header -->
<header class="header">
    <div class="header-inner">

        <!-- Logo -->
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/index.php" class="logo">
            <img
                src="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada' ?>/assets/img/logo-maos-de-fada.png"
                alt="Mãos de Fada"
                class="logo-image"
                style="display:block!important;width:120px!important;height:auto!important;max-width:120px!important;max-height:68px!important;object-fit:contain!important;">
        </a>

        <!-- Pesquisa -->
        <form class="search" action="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/pesquisa.php" method="GET">
            <input
                type="search"
                name="q"
                data-validacao="busca"
                maxlength="80"
                placeholder="O que você procura?"
                aria-label="Pesquisar produtos">
            <button type="submit">🔍</button>
        </form>

        <!-- Ações -->
        <div class="header-actions">
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/conta/entrar.php" class="header-btn">
                <span>👤</span>
                <span><?= $nomeConta ? htmlspecialchars($nomeConta, ENT_QUOTES, 'UTF-8') : 'Minha Conta' ?></span>
            </a>

            <a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/compras/carrinho.php" class="header-btn">
                <span>🛍️</span>
                <span>Carrinho (<?= $quantidadeCarrinho ?>)</span>
            </a>
        </div>

    </div>
</header>

<!-- Navegação -->
<nav class="nav">
    <ul>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/index.php">Início</a></li>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/index.php#categorias">Categorias</a></li>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/produtos/produtos.php">Produtos</a></li>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/produtos/promocoes.php">Promoções</a></li>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/institucional/sobre.php">Sobre Nós</a></li>
        <li><a href="<?= defined('BASE_URL') ? BASE_URL : '/Maos_De_Fada_Armarinho_reorganizado' ?>/paginas/institucional/contato.php">Contato</a></li>
    </ul>
</nav>
