<?php
session_start();
require_once __DIR__ . '/../../includes/catalogo.php';
require_once __DIR__ . '/../../includes/validacao.php';

$enviado = false;
$erro = '';
$nome = texto_limpo(trim($_POST['nome'] ?? ''));
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!somente_nome($nome) || mb_strlen($nome) < 3) {
        $erro = 'Informe seu nome.';
    } elseif (!email_valido($email)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (!mensagem_valida($mensagem) || mb_strlen($mensagem) < 10) {
        $erro = 'Escreva uma mensagem com pelo menos 10 caracteres.';
    } else {
        $_SESSION['mensagens_contato'] ??= [];
        $_SESSION['mensagens_contato'][] = [
            'nome' => $nome,
            'email' => $email,
            'mensagem' => $mensagem,
            'data' => date('d/m/Y H:i')
        ];
        $enviado = true;
        $nome = $email = $mensagem = '';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato | Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="institutional-page">
    <section class="page-hero soft-lilac">
        <span class="eyebrow">FALE COM A GENTE</span>
        <h1>Contato ♡</h1>
        <p>Tem uma dúvida? Envie uma mensagem e deixe seu recado registrado.</p>
    </section>

    <section class="contact-layout">
        <div class="contact-info-card">
            <span class="contact-big-icon">💌</span>
            <h2>Estamos aqui para ajudar</h2>
            <p>Entre em contato para dúvidas sobre produtos, pedidos, entregas ou funcionamento da loja.</p>
            <div class="contact-detail"><span>✉️</span><div><strong>E-mail</strong><span>contato@maosdefada.com.br</span></div></div>
            <div class="contact-detail"><span>📞</span><div><strong>Telefone</strong><span>(11) 99999-9999</span></div></div>
            <div class="contact-detail"><span>🕐</span><div><strong>Atendimento</strong><span>Segunda a sexta, das 9h às 18h</span></div></div>
        </div>

        <div class="contact-form-card">
            <?php if ($enviado): ?>
                <div class="success-box"><strong>Mensagem enviada! ♡</strong><span>Seu contato foi registrado nesta sessão. Obrigado por falar com a Mãos de Fada.</span></div>
            <?php endif; ?>
            <?php if ($erro): ?><div class="error-box"><?= e($erro) ?></div><?php endif; ?>

            <h2>Envie sua mensagem</h2>
            <form method="post" class="clean-form">
                <label for="nome">Nome</label>
                <input id="nome" type="text" name="nome" data-validacao="nome" value="<?= e($nome) ?>" placeholder="Seu nome" required>

                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" maxlength="150" value="<?= e($email) ?>" placeholder="voce@email.com" required>

                <label for="mensagem">Mensagem</label>
                <textarea id="mensagem" name="mensagem" data-validacao="mensagem" maxlength="1000" rows="6" placeholder="Como podemos ajudar?" required><?= e($mensagem) ?></textarea>

                <button class="hero-btn" type="submit">ENVIAR MENSAGEM</button>
            </form>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
