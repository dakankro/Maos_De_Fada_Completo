<?php
session_start();
require_once __DIR__ . '/../includes/catalogo.php';
if (empty($_SESSION['funcionario_id'])) {
    header('Location: ../paginas/conta/login_funcionario.php');
    exit;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$id=(int)($_GET['id']??$_POST['id']??0);
$st=$pdo->prepare("SELECT * FROM produtos WHERE id=?");$st->execute([$id]);$produto=$st->fetch();
if(!$produto){header('Location: produtos.php?msg=Produto+não+encontrado');exit;}
$categorias=$pdo->query("SELECT id,nome FROM categorias ORDER BY nome")->fetchAll();
$dirImagens=__DIR__.'/../assets/img/produtos/';
$imagens=array_values(array_filter(scandir($dirImagens), function($f) use ($dirImagens){ return is_file($dirImagens.$f) && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i',$f) && $f !== 'sem-imagem.svg'; }));
$erro='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $categoria=(int)($_POST['categoria_id']??0);$nome=trim($_POST['nome']??'');$descricao=trim($_POST['descricao']??'');$preco=str_replace(',','.',trim($_POST['preco']??''));$estoque=(int)($_POST['estoque']??0);$imagem=basename(trim($_POST['imagem']??''));$promocao=isset($_POST['promocao'])?1:0;
 if($categoria<1||$nome===''||!is_numeric($preco)||$preco<0||$estoque<0)$erro='Preencha os campos obrigatórios corretamente.';
 if(!$erro){
  if($imagem !== '' && !in_array($imagem,$imagens,true)) { $erro='Escolha uma imagem interna disponível no projeto.'; }
  if(!$erro){$st=$pdo->prepare("UPDATE produtos SET categoria_id=?,nome=?,descricao=?,preco=?,estoque=?,imagem=?,promocao=? WHERE id=?");$st->execute([$categoria,$nome,$descricao,(float)$preco,$estoque,$imagem,$promocao,$id]);header('Location: produtos.php?msg=Produto+alterado+com+sucesso');exit;}
}
 $produto=array_merge($produto,['categoria_id'=>$categoria,'nome'=>$nome,'descricao'=>$descricao,'preco'=>$preco,'estoque'=>$estoque,'imagem'=>$imagem,'promocao'=>$promocao]);
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Editar produto - Mãos de Fada</title><link rel="stylesheet" href="admin.css"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"></head><body><div class="admin-wrap"><header class="admin-top"><div class="brand">Mãos de Fada<small>Painel do funcionário</small></div><div class="admin-user"><span>Olá, <strong><?= h($_SESSION['funcionario_nome'] ?? 'Funcionário') ?></strong></span><a class="btn btn-light btn-small" href="logout.php">Sair</a></div></header><div class="layout"><aside class="sidebar"><a class="" href="index.php">📊 Dashboard</a><a class="active" href="produtos.php">📦 Produtos</a><a class="" href="adicionar_produto.php">➕ Adicionar produto</a><a class="" href="estoque.php">📋 Estoque</a><a class="" href="pedidos.php">🛒 Pedidos</a></aside><main class="content"><h1 class="title">Editar produto</h1><p class="subtitle">Altere os dados do produto selecionado.</p><?php if($erro): ?><div class="alert error"><?= h($erro) ?></div><?php endif; ?><div class="section"><form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= h($produto['id']) ?>"><div class="form-grid"><div class="field"><label>Categoria *</label><select name="categoria_id" required><?php foreach($categorias as $c): ?><option value="<?= h($c['id']) ?>" <?= $produto['categoria_id']==$c['id']?'selected':'' ?>><?= h($c['nome']) ?></option><?php endforeach; ?></select></div><div class="field"><label>Nome *</label><input name="nome" required value="<?= h($produto['nome']) ?>"></div><div class="field"><label>Preço *</label><input type="number" step="0.01" min="0" name="preco" required value="<?= h($produto['preco']) ?>"></div><div class="field"><label>Estoque *</label><input type="number" min="0" name="estoque" required value="<?= h($produto['estoque']) ?>"></div><div class="field full"><label>Imagem do produto</label><?php $imgAtual=imagemProduto($produto); ?><img src="<?= h($imgAtual) ?>" style="max-width:180px;max-height:180px;object-fit:cover;border-radius:10px;margin:8px 0" alt="Imagem atual"><br><label>Escolher imagem interna</label><select name="imagem"><option value="">Sem imagem</option><?php foreach($imagens as $img): ?><option value="<?= h($img) ?>" <?= (($produto['imagem']??'')===$img)?'selected':'' ?>><?= h($img) ?></option><?php endforeach; ?></select><small>As imagens ficam em <strong>assets/img/produtos/</strong>.</small></div><div class="field full"><label>Descrição</label><textarea name="descricao"><?= h($produto['descricao']) ?></textarea></div><div class="field full"><label><input type="checkbox" name="promocao" <?= $produto['promocao']?'checked':'' ?>> Produto em promoção</label></div></div><div class="form-actions"><button class="btn btn-primary">Salvar alterações</button><a class="btn btn-light" href="produtos.php">Cancelar</a></div></form></div></main></div></div></body></html>