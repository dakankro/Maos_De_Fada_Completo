<?php
require_once __DIR__ . '/config.php';

function pedidoItensDoCarrinho(array $carrinho): array
{
    global $pdo;
    $itens = [];

    $stmt = $pdo->prepare(
        'SELECT id, nome, preco, estoque FROM produtos WHERE id = :id LIMIT 1'
    );

    foreach ($carrinho as $produtoId => $quantidade) {
        $produtoId = (int) $produtoId;
        $quantidade = (int) $quantidade;
        if ($produtoId <= 0 || $quantidade <= 0) continue;

        $stmt->execute([':id' => $produtoId]);
        $produto = $stmt->fetch();
        if (!$produto || (int)$produto['estoque'] <= 0) continue;

        $quantidade = min($quantidade, (int)$produto['estoque']);
        $itens[] = [
            'id' => $produtoId,
            'nome' => $produto['nome'],
            'preco' => (float)$produto['preco'],
            'quantidade' => $quantidade,
            'subtotal' => (float)$produto['preco'] * $quantidade,
        ];
    }

    return $itens;
}

function criarPedido(int $usuarioId, array $itens): int
{
    global $pdo;

    if ($usuarioId <= 0 || empty($itens)) {
        throw new RuntimeException('Pedido sem usuário ou sem produtos.');
    }

    $total = array_sum(array_column($itens, 'subtotal'));

    try {
        $pdo->beginTransaction();

        // 1) Cria o pedido principal.
        $pedidoStmt = $pdo->prepare(
            'INSERT INTO pedidos (usuario_id, total, status) VALUES (?, ?, ?)'
        );
        $pedidoStmt->execute([$usuarioId, $total, 'pendente']);
        $pedidoId = (int) $pdo->lastInsertId();

        if ($pedidoId <= 0) {
            throw new RuntimeException('O pedido foi criado, mas não foi possível obter o ID.');
        }

        // 2) Prepara as duas consultas usando apenas parâmetros posicionais.
        // Isso evita o HY093 causado por repetir o mesmo parâmetro nomeado.
        $produtoStmt = $pdo->prepare(
            'SELECT estoque, preco FROM produtos WHERE id = ? FOR UPDATE'
        );

        $itemStmt = $pdo->prepare(
            'INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)'
        );

        $updateEstoque = $pdo->prepare(
            'UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?'
        );

        $itensInseridos = 0;

        foreach ($itens as $item) {
            $produtoId = (int) $item['id'];
            $quantidade = (int) $item['quantidade'];
            $preco = (float) $item['preco'];

            if ($produtoId <= 0 || $quantidade <= 0) {
                throw new RuntimeException('Produto ou quantidade inválida.');
            }

            // Confere estoque novamente dentro da transação.
            $produtoStmt->execute([$produtoId]);
            $produto = $produtoStmt->fetch();

            if (!$produto) {
                throw new RuntimeException('O produto #' . $produtoId . ' não existe no banco.');
            }

            if ((int) $produto['estoque'] < $quantidade) {
                throw new RuntimeException('Estoque insuficiente para o produto #' . $produtoId . '.');
            }

            // Grava o item do pedido.
            $itemStmt->execute([
                $pedidoId,
                $produtoId,
                $quantidade,
                $preco
            ]);

            if ($itemStmt->rowCount() !== 1) {
                throw new RuntimeException('O item do pedido não foi gravado.');
            }

            $itensInseridos++;

            // Atualiza o estoque.
            $updateEstoque->execute([
                $quantidade,
                $produtoId,
                $quantidade
            ]);

            if ($updateEstoque->rowCount() !== 1) {
                throw new RuntimeException('Não foi possível atualizar o estoque do produto #' . $produtoId . '.');
            }
        }

        if ($itensInseridos === 0) {
            throw new RuntimeException('Nenhum item foi inserido no pedido.');
        }

        $pdo->commit();
        return $pedidoId;

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

function pedidosDoUsuario(int $usuarioId): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT id, total, status, criado_em
         FROM pedidos
         WHERE usuario_id = :usuario_id
         ORDER BY criado_em DESC, id DESC'
    );
    $stmt->execute([':usuario_id' => $usuarioId]);
    return $stmt->fetchAll();
}

function itensDoPedido(int $pedidoId): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT i.quantidade, i.preco_unitario, p.nome
         FROM itens_pedido i
         INNER JOIN produtos p ON p.id = i.produto_id
         WHERE i.pedido_id = :pedido_id
         ORDER BY i.id'
    );
    $stmt->execute([':pedido_id' => $pedidoId]);
    return $stmt->fetchAll();
}
