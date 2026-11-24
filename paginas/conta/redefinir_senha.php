<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/validacao.php';
$token = (string)($_POST['token'] ?? $_GET['token'] ?? '');
$mensagem = ''; $sucesso = false;
$valido = preg_match('/^[a-f0-9]{64}$/D', $token) === 1;
$registro = null;
if ($valido) {
    $stmt = $pdo->prepare('SELECT id, usuario_id FROM recuperacao_senha WHERE token_hash = ? AND usado_em IS NULL AND expira_em > NOW() LIMIT 1');
    $stmt->execute([hash('sha256', $token)]);
    $registro = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $registro) {
    $senha = (string)($_POST['senha'] ?? '');
    $confirmacao = (string)($_POST['confirmar_senha'] ?? '');
    if (!senha_valida($senha)) $mensagem = 'A senha deve ter pelo menos 8 caracteres, uma letra e um número.';
    elseif (!hash_equals($senha, $confirmacao)) $mensagem = 'As senhas não são iguais.';
    else {
        try {
            // Atualiza somente se o token ainda estiver disponível.
            // A tabela login é MyISAM no dump original: para atomicidade plena, migre as tabelas para InnoDB.
            $stmt = $pdo->prepare('UPDATE recuperacao_senha SET usado_em = NOW() WHERE id = ? AND usado_em IS NULL AND expira_em > NOW()');
            $stmt->execute([$registro['id']]);
            if ($stmt->rowCount() === 1) {
                $stmt = $pdo->prepare('UPDATE login SET senha = ? WHERE usuario_id = ?');
                $stmt->execute([password_hash($senha, PASSWORD_DEFAULT), $registro['usuario_id']]);
                if ($stmt->rowCount() !== 1) throw new RuntimeException('Login não atualizado');
                $pdo->prepare('UPDATE recuperacao_senha SET usado_em = NOW() WHERE usuario_id = ? AND usado_em IS NULL')->execute([$registro['usuario_id']]);
                $sucesso = true; $mensagem = 'Sua senha foi alterada com sucesso!';
            } else $mensagem = 'Este link já foi utilizado ou expirou.';
        } catch (Throwable $e) { $mensagem = 'Não foi possível alterar a senha. Tente solicitar outro link.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Redefinir senha | Mãos de Fada</title><link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../assets/css/conta-ajustes.css?v=2"></head><body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<main class="password-page"><section class="password-card"><div class="password-card-head"><span class="account-avatar">♡</span><span class="eyebrow">SEGURANÇA DA CONTA</span><h1>Criar nova senha</h1><p>Escolha uma senha segura para continuar aproveitando o Mãos de Fada.</p></div><div class="password-card-body"><?php if ($mensagem): ?><p class="password-notice"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><?php if ($sucesso): ?><a class="back-link" href="entrar.php">Entrar com a nova senha →</a><?php elseif (!$registro): ?><p>Link inválido, expirado ou já utilizado.</p><a class="back-link" href="esqueci_senha.php">Solicitar outro link →</a><?php else: ?><form method="post"><input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><div class="password-fields"><div><label for="senha">Nova senha</label><input type="password" id="senha" name="senha" minlength="8" autocomplete="new-password" required></div><div><label for="confirmar_senha">Confirmar senha</label><input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" autocomplete="new-password" required></div></div><button class="hero-btn" type="submit">SALVAR NOVA SENHA ♡</button></form><?php endif; ?><a class="back-link" href="entrar.php">← Voltar para o login</a></div></section></main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body></html>
