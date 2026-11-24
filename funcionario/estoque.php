<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
if (empty($_SESSION['funcionario_id'])) {
    header('Location: ../paginas/conta/login_funcionario.php');
    exit;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){ $id=(int)($_POST['id']??0);$estoque=(int)($_POST['estoque']??0); if($id>0&&$estoque>=0){$st=$pdo->prepare("UPDATE produtos SET estoque=? WHERE id=?");$st->execute([$estoque,$id]);$msg='Estoque atualizado com sucesso.';} }
$produtos=$pdo->query("SELECT p.id,p.nome,p.estoque,c.nome categoria FROM produtos p INNER JOIN categorias c ON c.id=p.categoria_id ORDER BY p.estoque ASC,p.nome")->fetchAll();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Estoque - Mãos de Fada</title><link rel="stylesheet" href="admin.css"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"></head><body><div class="admin-wrap"><header class="admin-top"><div class="brand">Mãos de Fada<small>Painel do funcionário</small></div><div class="admin-user"><span>Olá, <strong><?= h($_SESSION['funcionario_nome'] ?? 'Funcionário') ?></strong></span><a class="btn btn-light btn-small" href="logout.php">Sair</a></div></header><div class="layout"><aside class="sidebar"><a class="" href="index.php">📊 Dashboard</a><a class="" href="produtos.php">📦 Produtos</a><a class="" href="adicionar_produto.php">➕ Adicionar produto</a><a class="active" href="estoque.php">📋 Estoque</a><a class="" href="pedidos.php">🛒 Pedidos</a></aside><main class="content"><h1 class="title">Controle de estoque</h1><p class="subtitle">Atualize rapidamente a quantidade disponível de cada produto.</p><?php if($msg): ?><div class="alert success"><?= h($msg) ?></div><?php endif; ?><div class="section"><div class="table-wrap"><table><tr><th>ID</th><th>Produto</th><th>Categoria</th><th>Estoque atual</th><th>Nova quantidade</th><th>Status</th><th></th></tr><?php foreach($produtos as $p): ?><tr><td><?= h($p['id']) ?></td><td><?= h($p['nome']) ?></td><td><?= h($p['categoria']) ?></td><td><?= h($p['estoque']) ?></td><td><form method="post" class="actions"><input type="hidden" name="id" value="<?= h($p['id']) ?>"><input type="number" name="estoque" min="0" value="<?= h($p['estoque']) ?>" style="width:100px;padding:8px;border:1px solid #ddcfd5;border-radius:8px"><button class="btn btn-primary btn-small">Salvar</button></form></td><td class="<?= $p['estoque']<=5?'low':'ok' ?>"><?= $p['estoque']<=5?'Estoque baixo':'Normal' ?></td><td></td></tr><?php endforeach; ?></table></div></div></main></div></div></body></html>