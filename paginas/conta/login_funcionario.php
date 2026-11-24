<?php
session_start();
require_once __DIR__ . '/../../includes/validacao.php';
require_once __DIR__ . '/../../includes/config.php';
$id=$senha=$nome='';$idError=$senhaError=$nomeError=$loginError='';
function verificar_entrada($entrada){return texto_limpo(trim(stripslashes((string)$entrada)));}
function validarNome($nome){$nome=trim($nome);if(strlen($nome)<2)return ['valido'=>false,'mensagem'=>'Informe um nome válido.'];if(!preg_match("/^[A-Za-zÀ-ÿ]+(?:[ \'-][A-Za-zÀ-ÿ]+)*$/u",$nome))return ['valido'=>false,'mensagem'=>'Use apenas letras, espaços, hífen ou apóstrofo no nome.'];return ['valido'=>true,'mensagem'=>''];}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=verificar_entrada($_POST['id']??'');$nome=verificar_entrada($_POST['nome']??'');$senha=$_POST['senha']??'';
 if($id===''||!ctype_digit($id)||((int)$id)<1)$idError='Informe um ID numérico válido.';
 if(!validarNome($nome)['valido'])$nomeError='Use apenas letras, espaços, hífen ou apóstrofo no nome.';
 if(!senha_valida($senha))$senhaError='A senha deve ter pelo menos 8 caracteres, com letras e números. Símbolos também são permitidos.';
 if(!$idError&&!$nomeError&&!$senhaError){
  $stmt = $pdo->prepare('SELECT id, nome, senha FROM funcionarios WHERE id = ? LIMIT 1');
  $stmt->execute([(int)$id]);
  $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
  if($funcionario && strcasecmp(trim($funcionario['nome']), trim($nome)) === 0 && password_verify($senha, $funcionario['senha'])){
      $_SESSION['funcionario_id']=(int)$funcionario['id'];
      $_SESSION['funcionario_nome']=$funcionario['nome'];
      header('Location: ../../funcionario/index.php');
      exit;
  }
  $loginError='ID, nome ou senha incorretos.';
 }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Funcionário - Mãos de Fada</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            max-width: 550px;
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

        .login-header h1 .badge {
            font-size: 14px;
            background: linear-gradient(135deg, #c97b8a, #d4a5b5);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
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

        .form-group {
            margin-bottom: 18px;
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

        .btn-entrar {
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
            margin-top: 10px;
        }

        .btn-entrar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 123, 138, 0.4);
        }

        .btn-entrar:active {
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

        /* Badge de funcionário */
        .funcionario-badge {
            display: inline-block;
            background: #f5f0fa;
            color: #c97b8a;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e8e0e5;
            margin-top: 10px;
        }

        .funcionario-badge span {
            font-size: 16px;
        }

        /* Responsivo */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                max-width: 100%;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-header h1 .badge {
                font-size: 12px;
                margin-left: 5px;
                padding: 2px 10px;
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

<!-- Main Content -->
<main>
    <div class="login-container">
        <div class="login-header">
            <a href="login.php" class="voltar">← Voltar para usuário</a>
            <h1>
                Login Funcionário
                <span class="badge">🔑</span>
            </h1>
            <div class="subtitle">
                Acesso restrito à equipe Mãos de Fada
            </div>
            <div class="funcionario-badge">
                <span>👤</span> Área administrativa
            </div>
        </div>

        <?php if (!empty($loginError)): ?>
            <div class="erro-geral">
                <span>⚠</span> <?php echo $loginError; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate id="formLogin">

            <!-- ID do Funcionário -->
            <div class="form-group">
                <label for="id">ID do Funcionário <span>*</span></label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        id="id"
                        name="id"
                        data-validacao="numero"
                        inputmode="numeric"
                        placeholder="Digite seu ID (ex: 1)"
                        value="<?php echo htmlspecialchars($id); ?>"
                        required
                        maxlength="10"
                        pattern="[0-9]+"
                        title="Digite apenas números"
                    >
                </div>
                <?php if (!empty($idError)): ?>
                    <span class="erro"><?php echo $idError; ?></span>
                <?php else: ?>
                    <div class="validation-hint">
                        <span>💡</span> Seu ID de funcionário (apenas números)
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nome -->
            <div class="form-group">
                <label for="nome">Nome completo <span>*</span></label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        data-validacao="nome"
                        placeholder="Seu nome completo"
                        value="<?php echo htmlspecialchars($nome); ?>"
                        required
                        pattern="[^\s][a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\s\-']*[^\s]"
                        title="Use apenas letras, espaços simples, hífen e apóstrofo"
                    >
                </div>
                <?php if (!empty($nomeError)): ?>
                    <span class="erro"><?php echo $nomeError; ?></span>
                <?php else: ?>
                    <div class="validation-hint">
                        <span>💡</span> Use letras, espaços, hífen ou apóstrofo
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
                        placeholder="Sua senha"
                        required
                    >
                </div>
                <?php if (!empty($senhaError)): ?>
                    <span class="erro"><?php echo $senhaError; ?></span>
                <?php else: ?>
                    <div class="validation-hint">
                        <span>💡</span> Mínimo 6 caracteres, com letra e número
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-entrar">ENTRAR ♡</button>

        </form>

        <div class="login-footer">
            <p>
                <a href="login.php">← Voltar para login do cliente</a>
            </p>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação do ID (apenas números)
        const idInput = document.getElementById('id');
        if (idInput) {
            idInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value !== this.value) {
                    this.value = value;
                }
            });

            idInput.addEventListener('keyup', function(e) {
                const value = this.value;
                const hintSpan = this.parentElement.parentElement.querySelector('.validation-hint');
                const errorSpan = this.parentElement.parentElement.querySelector('.erro');

                if (value && hintSpan && !errorSpan) {
                    if (value.length >= 1) {
                        hintSpan.innerHTML = '<span class="valid">✓</span> ID válido';
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else {
                        hintSpan.innerHTML = '<span class="invalid">✗</span> Digite um ID numérico válido';
                        this.classList.add('error');
                        this.classList.remove('success');
                    }
                }
            });
        }

        // Validação do nome
        const nomeInput = document.getElementById('nome');
        if (nomeInput) {
            function corrigirNome(value) {
                let cleaned = value.trim();
                cleaned = cleaned.replace(/\s{2,}/g, ' ');
                cleaned = cleaned.replace(/[^a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\s\-\']/g, '');
                return cleaned;
            }

            nomeInput.addEventListener('input', function(e) {
                let value = this.value;
                let cleaned = corrigirNome(value);
                if (cleaned !== value) {
                    this.value = cleaned;
                }
            });

            nomeInput.addEventListener('keyup', function(e) {
                const value = this.value;
                const hintSpan = this.parentElement.parentElement.querySelector('.validation-hint');
                const errorSpan = this.parentElement.parentElement.querySelector('.erro');

                if (value && hintSpan && !errorSpan) {
                    let hasError = false;
                    let msg = '';

                    if (value[0] === ' ') {
                        hasError = true;
                        msg = 'Não pode começar com espaço';
                    } else if (value.includes('  ')) {
                        hasError = true;
                        msg = 'Não use múltiplos espaços';
                    } else if (/[^a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ\s\-\']/.test(value)) {
                        hasError = true;
                        msg = 'Apenas letras, espaços, - e \'';
                    }

                    if (hasError) {
                        hintSpan.innerHTML = `<span class="invalid">✗</span> ${msg}`;
                        this.classList.add('error');
                        this.classList.remove('success');
                    } else if (value.length >= 2) {
                        hintSpan.innerHTML = '<span class="valid">✓</span> Nome válido';
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else {
                        hintSpan.innerHTML = '<span>💡</span> Use letras, espaços, hífen ou apóstrofo';
                        this.classList.remove('error');
                        this.classList.remove('success');
                    }
                }
            });
        }

        // Validação da senha
        const senhaInput = document.getElementById('senha');
        if (senhaInput) {
            senhaInput.addEventListener('input', function(e) {
                const value = this.value;
                const hintSpan = this.parentElement.parentElement.querySelector('.validation-hint');

                if (value && hintSpan) {
                    let hasLetter = /[a-zA-Z]/.test(value);
                    let hasNumber = /[0-9]/.test(value);
                    let hasMinLength = value.length >= 6;

                    if (hasMinLength && hasLetter && hasNumber) {
                        hintSpan.innerHTML = '<span class="valid">✓</span> Senha forte';
                        this.classList.remove('error');
                        this.classList.add('success');
                    } else {
                        let msg = '';
                        if (!hasMinLength) msg = 'Mínimo 6 caracteres';
                        else if (!hasLetter) msg = 'Adicione uma letra';
                        else if (!hasNumber) msg = 'Adicione um número';
                        hintSpan.innerHTML = `<span class="invalid">✗</span> ${msg}`;
                        this.classList.add('error');
                        this.classList.remove('success');
                    }
                }
            });
        }
    });
</script>

<?php
include __DIR__ . '/../../includes/footer.php';
?>

</body>
</html>