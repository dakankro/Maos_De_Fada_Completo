<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/validacao.php';

$email = $senha = $nome = $cpf = $telefone = '';
$emailError = $senhaError = $confirmarSenhaError = $nomeError = $cpfError = $telefoneError = $loginError = '';

function verificar_entrada($entrada): string {
    return texto_limpo(stripslashes((string) $entrada));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = verificar_entrada($_POST['nome'] ?? '');
    $cpf = preg_replace('/\D/', '', (string) ($_POST['cpf'] ?? ''));
    $telefone = preg_replace('/\D/', '', (string) ($_POST['telefone'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $senha = (string) ($_POST['senha'] ?? '');
    $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

    if ($nome === '') {
        $nomeError = 'O nome é obrigatório.';
    } elseif (mb_strlen($nome) < 3 || !somente_nome($nome) || substr_count($nome, ' ') < 1) {
        $nomeError = 'Informe nome e sobrenome usando apenas letras, espaços, hífen ou apóstrofo.';
    }

    if ($cpf === '') {
        $cpfError = 'O CPF é obrigatório.';
    } elseif (!cpf_valido($cpf)) {
        $cpfError = 'CPF inválido.';
    }

    if ($telefone === '') {
        $telefoneError = 'O telefone é obrigatório.';
    } elseif (!telefone_valido($telefone)) {
        $telefoneError = 'Informe um telefone válido com DDD.';
    }

    if ($email === '') {
        $emailError = 'O e-mail é obrigatório.';
    } elseif (!email_valido($email)) {
        $emailError = 'E-mail inválido.';
    }

    if ($senha === '') {
        $senhaError = 'A senha é obrigatória.';
    } elseif (!senha_valida($senha)) {
        $senhaError = 'A senha deve ter pelo menos 8 caracteres, com pelo menos uma letra e um número.';
    }

    if ($confirmarSenha === '' || !hash_equals($senha, $confirmarSenha)) {
        $confirmarSenhaError = 'As senhas devem ser iguais.';
    }

    if (!$nomeError && !$cpfError && !$telefoneError && !$emailError && !$senhaError && !$confirmarSenhaError) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email OR cpf = :cpf LIMIT 1');
            $stmt->execute([':email' => $email, ':cpf' => $cpf]);

            if ($stmt->fetch()) {
                $pdo->rollBack();
                $loginError = 'Já existe uma conta com este e-mail ou CPF.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, telefone, cpf) VALUES (:nome, :email, :telefone, :cpf)');
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':telefone' => $telefone,
                    ':cpf' => $cpf
                ]);

                $usuarioId = (int) $pdo->lastInsertId();
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare('INSERT INTO login (usuario_id, senha) VALUES (:usuario_id, :senha)');
                $stmt->execute([
                    ':usuario_id' => $usuarioId,
                    ':senha' => $senhaHash
                ]);

                $pdo->commit();

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuarioId;
                $_SESSION['usuario_nome'] = $nome;
                header('Location: ../../index.php');
                exit;
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $loginError = 'Não foi possível criar a conta. Verifique os dados e tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../assets/css/conta-ajustes.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fdf6f0 0%, #f8f0f5 50%, #f5f0fa 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(201, 123, 138, 0.15);
            padding: 50px;
            width: 100%;
            max-width: 650px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #c97b8a, #d4a5b5, #c97b8a);
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-header .voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #c97b8a;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 15px;
            transition: color 0.3s;
        }

        .login-header .voltar:hover {
            color: #a85d6b;
            text-decoration: underline;
        }

        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #333;
            margin-bottom: 5px;
        }

        .login-header .subtitle {
            color: #999;
            font-size: 14px;
        }

        .login-header .subtitle a {
            color: #c97b8a;
            text-decoration: none;
            font-weight: 500;
        }

        .login-header .subtitle a:hover {
            text-decoration: underline;
        }

        .login-header .link-funcionario {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 20px;
            background: #f5f0fa;
            color: #c97b8a;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e8e0e5;
        }

        .login-header .link-funcionario:hover {
            background: #c97b8a;
            color: white;
            border-color: #c97b8a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201, 123, 138, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 5px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group label span {
            color: #c97b8a;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e8e0e5;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: #faf8fa;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #c97b8a;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(201, 123, 138, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #aaa;
            font-size: 14px;
        }

        .input-wrapper input.error {
            border-color: #d32f2f;
            background: #fff5f5;
        }

        .input-wrapper input.success {
            border-color: #4caf50;
            background: #f5fff5;
        }

        .erro {
            color: #d32f2f;
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            min-height: 18px;
        }

        .erro::before {
            content: '•';
            font-size: 14px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .btn-continuar {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #c97b8a 0%, #d4a5b5 100%);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(201, 123, 138, 0.3);
            letter-spacing: 0.5px;
            margin-top: 15px;
        }

        .btn-continuar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 123, 138, 0.4);
        }

        .btn-continuar:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0e8ed;
        }

        .login-footer p {
            color: #777;
            font-size: 13px;
            line-height: 1.8;
        }

        .login-footer a {
            color: #c97b8a;
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .decorative-hearts {
            position: fixed;
            font-size: 20px;
            color: rgba(201, 123, 138, 0.1);
            pointer-events: none;
            animation: float 6s ease-in-out infinite;
        }

        .heart-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .heart-2 { top: 20%; right: 15%; animation-delay: 1s; }
        .heart-3 { bottom: 20%; left: 8%; animation-delay: 2s; }
        .heart-4 { bottom: 30%; right: 10%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Mensagem de erro geral */
        .erro-geral {
            background: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .validation-hint {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
            min-height: 18px;
        }

        .validation-hint .valid {
            color: #4caf50;
        }

        .validation-hint .invalid {
            color: #d32f2f;
        }

        /* Responsivo */
        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
                max-width: 100%;
            }

            .login-header h1 {
                font-size: 26px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group {
                margin-bottom: 18px;
            }
        }
    </style>
</head>
<body>

<?php
include __DIR__ . '/../../includes/header.php';
?>
<!-- Elementos decorativos -->
<span class="decorative-hearts heart-1">♡</span>
<span class="decorative-hearts heart-2">♡</span>
<span class="decorative-hearts heart-3">♡</span>
<span class="decorative-hearts heart-4">♡</span>

<!-- Top Bar -->

<!-- Main Content -->
<main>
    <div class="login-container">
        <div class="login-header">
            <a href="../../index.php" class="voltar">← Voltar</a>
            <h1>Criar conta</h1>
            <div class="subtitle">
                <span>Seus materiais favoritos em um só lugar ♡</span>
            </div>
        </div>

        <?php if (!empty($loginError)): ?>
            <div class="erro-geral">
                <span>⚠</span> <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate id="formCadastro">

            <div class="form-row">
                <!-- Nome e sobrenome -->
                <div class="form-group form-full">
                    <label for="nome">Nome e sobrenome <span>*</span></label>
                    <div class="input-wrapper">
                        <input
                                type="text"
                                id="nome"
                                name="nome"
                                data-validacao="nome"
                                placeholder="Ex: Maria Silva"
                                value="<?php echo htmlspecialchars($nome); ?>"
                                required
                                pattern="[^\s][a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\s\-']*[^\s]"
                                title="Use apenas letras, espaços simples, hífen e apóstrofo. Não use espaços no início ou múltiplos espaços."
                        >
                    </div>
                    <?php if (!empty($nomeError)): ?>
                        <span class="erro"><?php echo $nomeError; ?></span>
                    <?php else: ?>
                        <div class="validation-hint">
                            <span>💡</span> Use apenas letras e espaços simples entre palavras
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CPF -->
                <div class="form-group">
                    <label for="cpf">CPF <span>*</span></label>
                    <div class="input-wrapper">
                        <input
                                type="text"
                                id="cpf"
                                name="cpf"
                                data-validacao="cpf"
                                inputmode="numeric"
                                placeholder="000.000.000-00"
                                value="<?php echo htmlspecialchars($cpf); ?>"
                                required
                                maxlength="14"
                                autocomplete="off"
                        >
                    </div>
                    <?php if (!empty($cpfError)): ?>
                        <span class="erro"><?php echo $cpfError; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Telefone -->
                <div class="form-group">
                    <label for="telefone">Telefone <span>*</span></label>
                    <div class="input-wrapper">
                        <input
                                type="tel"
                                id="telefone"
                                name="telefone"
                                data-validacao="telefone"
                                inputmode="tel"
                                placeholder="(11) 99999-9999"
                                value="<?php echo htmlspecialchars($telefone); ?>"
                                required
                        >
                    </div>
                    <?php if (!empty($telefoneError)): ?>
                        <span class="erro"><?php echo $telefoneError; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group form-full">
                    <label for="email">Email <span>*</span></label>
                    <div class="input-wrapper">
                        <input
                                type="email"
                                id="email"
                                name="email"
                                maxlength="150"
                                placeholder="seu@email.com"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                        >
                    </div>
                    <?php if (!empty($emailError)): ?>
                        <span class="erro"><?php echo $emailError; ?></span>
                    <?php else: ?>
                        <div class="validation-hint">
                            <span>💡</span> Deve conter @ e . (ex: nome@dominio.com)
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Senha -->
                <div class="form-group">
                    <label for="senha">Senha <span>*</span></label>
                    <div class="input-wrapper">
                        <input
                                type="password"
                                id="senha"
                                name="senha"
                                data-validacao="senha"
                                placeholder="Mínimo 8 caracteres"
                                required
                                minlength="8"
                                autocomplete="new-password"
                        >
                    </div>
                    <?php if (!empty($senhaError)): ?>
                        <span class="erro"><?php echo $senhaError; ?></span>
                    <?php else: ?>
                        <div class="validation-hint">
                            <span>💡</span> Mínimo 8 caracteres, com letra e número. Símbolos também são permitidos.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar senha <span>*</span></label>
                    <div class="input-wrapper"><input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita sua senha" autocomplete="new-password" minlength="8" required></div>
                    <?php if ($confirmarSenhaError): ?><span class="erro"><?= htmlspecialchars($confirmarSenhaError, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn-continuar" id="btnContinuar">CONTINUAR ♡</button>

        </form>

        <div class="cadastro-entrar">Já tem uma conta? <a href="entrar.php">Entrar</a></div>

        <div class="login-footer">
            <p>
                Ao criar uma conta, você concorda com nossos<br>
                <a href="termos.php">Termos de Uso</a> e
                <a href="../institucional/privacidade.php">Política de Privacidade</a>
            </p>
        </div>
    </div>
</main>



<script src="../../assets/js/validacao.js"></script>

<?php
include __DIR__ . '/../../includes/footer.php';
?>

</body>
</html>