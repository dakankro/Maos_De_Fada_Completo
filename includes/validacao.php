<?php
/** Validações centralizadas dos formulários. */

function texto_limpo(string $valor): string {
    return trim(preg_replace('/\s+/u', ' ', $valor));
}

function somente_nome(string $valor): bool {
    $valor = texto_limpo($valor);
    return $valor !== '' && (bool) preg_match("/^[\p{L}]+(?:[ '\-][\p{L}]+)*$/u", $valor);
}

function somente_numeros(string $valor): bool {
    return $valor !== '' && (bool) preg_match('/^\d+$/', $valor);
}

function email_valido(string $valor): bool {
    $valor = trim($valor);
    if ($valor === '' || strlen($valor) > 150) {
        return false;
    }

    return filter_var($valor, FILTER_VALIDATE_EMAIL) !== false;
}

function senha_valida(string $valor): bool {
    // Mínimo de 8 caracteres, pelo menos uma letra e um número.
    // Símbolos são permitidos para deixar a senha mais segura.
    return strlen($valor) >= 8
        && preg_match('/[A-Za-z]/', $valor)
        && preg_match('/\d/', $valor);
}

function mensagem_valida(string $valor): bool {
    // Impede caracteres de controle e tags HTML.
    return $valor !== '' && !preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F<>]/u', $valor);
}

function cpf_valido(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($t = 9; $t <= 10; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $cpf[$i] * (($t + 1) - $i);
        }

        $digito = ((10 * $soma) % 11) % 10;

        if ((int) $cpf[$t] !== $digito) {
            return false;
        }
    }

    return true;
}

function telefone_valido(string $telefone): bool {
    $telefone = preg_replace('/\D/', '', $telefone);

    if (strlen($telefone) !== 10 && strlen($telefone) !== 11) {
        return false;
    }

    // DDD não pode começar com 0.
    if ($telefone[0] === '0') {
        return false;
    }

    // Celular brasileiro com 11 dígitos deve começar com 9 após o DDD.
    if (strlen($telefone) === 11 && $telefone[2] !== '9') {
        return false;
    }

    return true;
}
