<?php
/** Configure um servidor SMTP/serviço de e-mail no ambiente de hospedagem.
 * mail() depende da configuração do servidor; no Windows local geralmente não envia.
 * Nunca exiba o link de recuperação na tela nem registre o token em logs.
 */
function enviar_email_recuperacao(string $destinatario, string $link): bool {
    $origem = getenv('MAOS_DE_FADA_EMAIL_FROM') ?: '';
    if (!filter_var($origem, FILTER_VALIDATE_EMAIL)) return false;
    $assunto = 'Redefinir senha - Maos de Fada';
    $corpo = "Você solicitou a redefinição da senha do Mãos de Fada.\n\n" .
             "Acesse o link abaixo em até 30 minutos:\n" . $link . "\n\n" .
             "Se não foi você, ignore esta mensagem.\n";
    return mail($destinatario, $assunto, $corpo, [
        'From' => $origem,
        'Content-Type' => 'text/plain; charset=UTF-8',
    ]);
}
