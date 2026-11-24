document.addEventListener('DOMContentLoaded', () => {
    // Limpeza simples de campos.
    document.querySelectorAll('[data-validacao="nome"]').forEach((el) => {
        el.addEventListener('input', () => {
            el.value = el.value
                .replace(/[^\p{L}\s\-']/gu, '')
                .replace(/\s{2,}/g, ' ')
                .replace(/^\s+/, ' ');
        });
    });

    document.querySelectorAll('[data-validacao="busca"]').forEach((el) => {
        el.addEventListener('input', () => {
            el.value = el.value
                .replace(/[^\p{L}\p{N}\s\-]/gu, '')
                .replace(/\s{2,}/g, ' ');
        });
    });

    document.querySelectorAll('[data-validacao="mensagem"]').forEach((el) => {
        el.addEventListener('input', () => {
            el.value = el.value.replace(/[<>]/g, '');
        });
    });

    // CPF: 000.000.000-00
    document.querySelectorAll('[data-validacao="cpf"]').forEach((el) => {
        el.addEventListener('input', () => {
            let value = el.value.replace(/\D/g, '').slice(0, 11);
            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }
            el.value = value;
        });
    });

    // Telefone: (00) 00000-0000 ou (00) 0000-0000
    document.querySelectorAll('[data-validacao="telefone"]').forEach((el) => {
        el.addEventListener('input', () => {
            let value = el.value.replace(/\D/g, '').slice(0, 11);

            if (value.length > 10) {
                value = value.replace(/(\d{2})(\d{5})(\d{1,4})/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/(\d{2})(\d{1,5})/, '($1) $2');
            }

            el.value = value;
        });
    });

    // Validação visual do nome.
    document.querySelectorAll('[data-validacao="nome"]').forEach((el) => {
        el.addEventListener('blur', () => {
            const valor = el.value.trim();
            const grupo = el.closest('.form-group');
            const dica = grupo?.querySelector('.validation-hint');
            if (!dica || !valor) return;

            const valido = /^[\p{L}]+(?:[ '\-][\p{L}]+)+$/u.test(valor);
            dica.innerHTML = valido
                ? '<span class="valid">✓</span> Nome válido'
                : '<span class="invalid">✗</span> Informe nome e sobrenome';
            el.classList.toggle('success', valido);
            el.classList.toggle('error', !valido);
        });
    });

    // E-mail.
    document.querySelectorAll('input[type="email"]').forEach((el) => {
        el.addEventListener('blur', () => {
            const grupo = el.closest('.form-group') || el.parentElement;
            const dica = grupo?.querySelector('.validation-hint');
            if (!dica || !el.value) return;

            const valido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value);
            dica.innerHTML = valido
                ? '<span class="valid">✓</span> E-mail válido'
                : '<span class="invalid">✗</span> Informe um e-mail válido';
            el.classList.toggle('success', valido);
            el.classList.toggle('error', !valido);
        });
    });

    // Senha: agora aceita símbolos e exige 8 caracteres + letra + número.
    document.querySelectorAll('[data-validacao="senha"]').forEach((el) => {
        el.addEventListener('input', () => {
            const grupo = el.closest('.form-group');
            const dica = grupo?.querySelector('.validation-hint');
            if (!dica) return;

            const tamanho = el.value.length >= 8;
            const letra = /[A-Za-z]/.test(el.value);
            const numero = /\d/.test(el.value);
            const valido = tamanho && letra && numero;

            if (valido) {
                dica.innerHTML = '<span class="valid">✓</span> Senha válida';
            } else if (!tamanho) {
                dica.innerHTML = '<span class="invalid">✗</span> Use pelo menos 8 caracteres';
            } else if (!letra) {
                dica.innerHTML = '<span class="invalid">✗</span> Adicione uma letra';
            } else {
                dica.innerHTML = '<span class="invalid">✗</span> Adicione um número';
            }

            el.classList.toggle('success', valido);
            el.classList.toggle('error', !valido && el.value.length > 0);
        });
    });
});
