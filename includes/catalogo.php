<?php

/**
 * Catálogo do projeto Mãos de Fada.
 * Os produtos agora são carregados do banco de dados MySQL.
 */

require_once __DIR__ . '/config.php';

/**
 * Retorna todos os produtos junto com suas categorias.
 */
function catalogoProdutos(): array
{
    global $pdo;

    $sql = "
        SELECT
            p.id,
            p.nome,
            p.descricao,
            p.preco,
            p.estoque,
            p.imagem,
            p.promocao,
            c.nome AS categoria,
            c.id AS categoria_id
        FROM produtos p
        INNER JOIN categorias c
            ON p.categoria_id = c.id
        ORDER BY p.id ASC
    ";

    $stmt = $pdo->query($sql);
    $produtos = [];

    // Imagens originais usadas pelo site. Servem como fallback caso
    // a coluna imagem esteja vazia no banco.
    $imagensPadrao = [
        1 => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600&h=600&fit=crop',
        2 => 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=600&h=600&fit=crop',
        3 => 'https://images.unsplash.com/photo-1582562124811-c09040d0a901?w=600&h=600&fit=crop',
        4 => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=600&h=600&fit=crop'
    ];

    while ($produto = $stmt->fetch()) {
        $id = (int) $produto['id'];

        if (empty($produto['imagem'])) {
            $produto['imagem'] = rtrim(BASE_URL, '/') . '/assets/img/produtos/sem-imagem.svg';
        }

        $produto['id'] = $id;
        $produto['preco'] = (float) $produto['preco'];
        $produto['estoque'] = (int) $produto['estoque'];
        $produto['promocao'] = (bool) $produto['promocao'];
        $produto['categoria_id'] = (int) $produto['categoria_id'];

        $produtos[$id] = $produto;
    }

    return $produtos;
}

/**
 * Retorna um produto pelo ID.
 */
function produtoPorId(int $id): ?array
{
    global $pdo;

    $sql = "
        SELECT
            p.id,
            p.nome,
            p.descricao,
            p.preco,
            p.estoque,
            p.imagem,
            p.promocao,
            c.nome AS categoria,
            c.id AS categoria_id
        FROM produtos p
        INNER JOIN categorias c
            ON p.categoria_id = c.id
        WHERE p.id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $produto = $stmt->fetch();

    if (!$produto) {
        return null;
    }

    $imagensPadrao = [
        1 => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=600&h=600&fit=crop',
        2 => 'https://images.unsplash.com/photo-1607344645866-009c320b63e0?w=600&h=600&fit=crop',
        3 => 'https://images.unsplash.com/photo-1582562124811-c09040d0a901?w=600&h=600&fit=crop',
        4 => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=600&h=600&fit=crop'
    ];

    $produto['id'] = (int) $produto['id'];
    $produto['preco'] = (float) $produto['preco'];
    $produto['estoque'] = (int) $produto['estoque'];
    $produto['promocao'] = (bool) $produto['promocao'];
    $produto['categoria_id'] = (int) $produto['categoria_id'];

    if (empty($produto['imagem'])) {
        $produto['imagem'] = rtrim(BASE_URL, '/') . '/assets/img/produtos/sem-imagem.svg';
    }

    return $produto;
}

/**
 * Garante uma imagem para qualquer produto, inclusive os cadastrados
 * pelo painel do funcionário sem URL de imagem.
 */
function imagemProduto(array $produto): string
{
    $imagem = trim((string)($produto['imagem'] ?? ''));

    // URL externa
    if ($imagem !== '' && preg_match('~^https?://~i', $imagem)) {
        return $imagem;
    }

    // Arquivo salvo pelo painel
    if ($imagem !== '') {
        $nome = basename($imagem);
        return rtrim(BASE_URL, '/') . '/assets/img/produtos/' . rawurlencode($nome);
    }

    // Imagem padrão local: não depende de internet.
    return rtrim(BASE_URL, '/') . '/assets/img/produtos/sem-imagem.svg';
}

/**
 * Retorna produtos de uma categoria pelo nome, usando o ID da categoria.
 * Isso evita problemas quando o nome da categoria tem pequenas diferenças.
 */
function produtosPorCategoria(string $nomeCategoria): array
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT id FROM categorias WHERE nome = ? LIMIT 1');
    $stmt->execute([$nomeCategoria]);
    $categoria = $stmt->fetch();

    if (!$categoria) {
        return [];
    }

    return array_values(array_filter(
        catalogoProdutos(),
        fn($p) => (int)$p['categoria_id'] === (int)$categoria['id']
    ));
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}
