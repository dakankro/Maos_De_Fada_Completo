<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/validacao.php';
require_once __DIR__ . '/../../includes/email_recuperacao.php';
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    if (!email_valido($email)) {
        $mensagem = 'Informe um e-mail válido.';
    } else {
        // Resposta genérica: não revela se a conta existe.
        $mensagem = 'Se este e-mail estiver cadastrado, você receberá um link para redefinir a senha.';
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $id = $stmt->fetchColumn();
            if ($id) {
                // Limita solicitações a uma por conta a cada 5 minutos.
                $limite = $pdo->prepare('SELECT COUNT(*) FROM recuperacao_senha WHERE usuario_id = ? AND criado_em > DATE_SUB(NOW(), INTERVAL 5 MINUTE)');
                $limite->execute([$id]);
                if ((int)$limite->fetchColumn() === 0) {
                    $token = bin2hex(random_bytes(32));
                    $hash = hash('sha256', $token);
                    $stmt = $pdo->prepare('INSERT INTO recuperacao_senha (usuario_id, token_hash, expira_em) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))');
                    $stmt->execute([$id, $hash]);
                    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    // Configure uma URL pública HTTPS antes de disponibilizar a recuperação na internet.
                    $base = getenv('MAOS_DE_FADA_PUBLIC_URL') ?: (($https ? 'https' : 'http') . '://' . $host . BASE_URL);
                    $link = rtrim($base, '/') . '/paginas/conta/redefinir_senha.php?token=' . urlencode($token);
                    if (!enviar_email_recuperacao($email, $link)) {
                        // Falha de envio não é exposta ao usuário. Remover token não entregue.
                        $pdo->prepare('DELETE FROM recuperacao_senha WHERE token_hash = ?')->execute([$hash]);
                        error_log('Mãos de Fada: envio de recuperação de senha falhou; configure o serviço de e-mail.');
                    }
                }
            }
        } catch (Throwable $e) {
            error_log('Mãos de Fada: falha na recuperação de senha.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Recuperar senha | Mãos de Fada</title><link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../assets/css/conta-ajustes.css?v=2"></head><body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<main class="password-page"><section class="password-card"><div class="password-card-head"><span class="account-avatar">♡</span><span class="eyebrow">MINHA CONTA</span><h1>Esqueceu sua senha?</h1><p>Informe o e-mail cadastrado e enviaremos um link para você criar uma nova senha.</p></div><div class="password-card-body"><?php if ($mensagem): ?><p class="password-notice"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><form method="post"><label for="email">E-mail</label><input type="email" id="email" name="email" maxlength="150" autocomplete="email" placeholder="seu@email.com" required><button class="hero-btn" type="submit">ENVIAR LINK ♡</button></form><a class="back-link" href="entrar.php">← Voltar para o login</a></div></section></main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body></html>
