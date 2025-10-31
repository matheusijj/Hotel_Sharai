/**
 * Arquivo de validações para o formulário de reserva
 * Criado para validar CPF, Email, Telefone e Datas
 */

// ============================================
// Função para validar CPF
// ============================================
function validarCPF(cpf) {
    // Primeiro, tirar tudo que não é número
    cpf = cpf.replace(/[^\d]/g, '');
    
    // Verificar se tem 11 números
    if (cpf.length !== 11) {
        return false;
    }
    
    // Verificar se todos os números são iguais (ex: 111.111.111-11)
    // Isso não é um CPF válido
    var todosIguais = true;
    for (var i = 1; i < cpf.length; i++) {
        if (cpf[i] !== cpf[0]) {
            todosIguais = false;
            break;
        }
    }
    if (todosIguais) {
        return false;
    }
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) {
        return false;
    }
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) {
        return false;
    }
    
    return true;
}

// ============================================
// VALIDAÇÃO DE E-MAIL
// ============================================
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// ============================================
// VALIDAÇÃO DE TELEFONE
// ============================================
function validarTelefone(telefone) {
    // Remove caracteres não numéricos
    const numeros = telefone.replace(/[^\d]/g, '');
    
    // Verifica se tem 10 ou 11 dígitos (com ou sem 9º dígito)
    return numeros.length === 10 || numeros.length === 11;
}

// ============================================
// VALIDAÇÃO DE DATAS
// ============================================
function validarDatas(dataEntrada, dataSaida) {
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    
    const entrada = new Date(dataEntrada);
    const saida = new Date(dataSaida);
    
    // Data de entrada não pode ser no passado
    if (entrada < hoje) {
        return {
            valido: false,
            mensagem: 'Data de entrada não pode ser no passado'
        };
    }
    
    // Data de saída deve ser posterior à entrada
    if (saida <= entrada) {
        return {
            valido: false,
            mensagem: 'Data de saída deve ser posterior à data de entrada'
        };
    }
    
    return {
        valido: true,
        mensagem: 'Datas válidas'
    };
}

// ============================================
// MÁSCARAS DE FORMATAÇÃO
// ============================================
function mascaraCPF(input) {
    let valor = input.value.replace(/[^\d]/g, '');
    
    if (valor.length <= 11) {
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = valor;
}

function mascaraTelefone(input) {
    let valor = input.value.replace(/[^\d]/g, '');
    
    if (valor.length <= 11) {
        if (valor.length <= 10) {
            // Formato: (11) 1234-5678
            valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
            valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            // Formato: (11) 91234-5678
            valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
            valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
        }
    }
    
    input.value = valor;
}

// ============================================
// EXIBIR MENSAGENS DE ERRO
// ============================================
function mostrarErro(inputId, mensagem) {
    const input = document.getElementById(inputId);
    const feedbackDiv = input.parentElement.querySelector('.invalid-feedback') || 
                        document.createElement('div');
    
    feedbackDiv.className = 'invalid-feedback';
    feedbackDiv.style.display = 'block';
    feedbackDiv.textContent = mensagem;
    
    if (!input.parentElement.querySelector('.invalid-feedback')) {
        input.parentElement.appendChild(feedbackDiv);
    }
    
    input.classList.add('is-invalid');
    input.classList.remove('is-valid');
}

function mostrarSucesso(inputId) {
    const input = document.getElementById(inputId);
    const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
    
    if (feedbackDiv) {
        feedbackDiv.style.display = 'none';
    }
    
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}

function limparValidacao(inputId) {
    const input = document.getElementById(inputId);
    const feedbackDiv = input.parentElement.querySelector('.invalid-feedback');
    
    if (feedbackDiv) {
        feedbackDiv.style.display = 'none';
    }
    
    input.classList.remove('is-invalid');
    input.classList.remove('is-valid');
}

// ============================================
// VALIDAÇÃO EM TEMPO REAL
// ============================================
function configurarValidacaoTempoReal() {
    // CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function() {
            mascaraCPF(this);
        });
        
        cpfInput.addEventListener('blur', function() {
            if (this.value) {
                if (validarCPF(this.value)) {
                    mostrarSucesso('cpf');
                } else {
                    mostrarErro('cpf', 'CPF inválido');
                }
            }
        });
    }
    
    // E-mail
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value) {
                if (validarEmail(this.value)) {
                    mostrarSucesso('email');
                } else {
                    mostrarErro('email', 'E-mail inválido');
                }
            }
        });
    }
    
    // Telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function() {
            mascaraTelefone(this);
        });
        
        telefoneInput.addEventListener('blur', function() {
            if (this.value) {
                if (validarTelefone(this.value)) {
                    mostrarSucesso('telefone');
                } else {
                    mostrarErro('telefone', 'Telefone inválido');
                }
            }
        });
    }
    
    // Datas
    const entradaInput = document.getElementById('entrada');
    const saidaInput = document.getElementById('saida');
    
    if (entradaInput && saidaInput) {
        function validarCampoDatas() {
            if (entradaInput.value && saidaInput.value) {
                const resultado = validarDatas(entradaInput.value, saidaInput.value);
                
                if (resultado.valido) {
                    mostrarSucesso('entrada');
                    mostrarSucesso('saida');
                } else {
                    if (resultado.mensagem === 'Data de saída deve ser posterior à data de entrada') {
                        limparValidacao('saida');
                        abrirModalDatasInvalidas();
                    } else {
                        mostrarErro('saida', resultado.mensagem);
                    }
                }
            }
        }
        
        entradaInput.addEventListener('change', validarCampoDatas);
        saidaInput.addEventListener('change', validarCampoDatas);
    }
}

// ============================================
// VALIDAÇÃO COMPLETA DO FORMULÁRIO
// ============================================
function validarFormularioCompleto() {
    let valido = true;
    
    // Validar Passo 1
    const entrada = document.getElementById('entrada').value;
    const saida = document.getElementById('saida').value;
    const quarto = document.getElementById('quarto').value;
    const adultos = document.getElementById('adultos').value;
    
    if (!entrada) {
        mostrarErro('entrada', 'Data de entrada é obrigatória');
        valido = false;
    }
    
    if (!saida) {
        mostrarErro('saida', 'Data de saída é obrigatória');
        valido = false;
    }
    
    if (entrada && saida) {
        const resultadoDatas = validarDatas(entrada, saida);
        if (!resultadoDatas.valido) {
            if (resultadoDatas.mensagem === 'Data de saída deve ser posterior à data de entrada') {
                limparValidacao('saida');
                abrirModalDatasInvalidas();
            } else {
                mostrarErro('saida', resultadoDatas.mensagem);
            }
            valido = false;
        }
    }
    
    if (!quarto) {
        mostrarErro('quarto', 'Selecione um quarto');
        valido = false;
    }
    
    if (!adultos) {
        mostrarErro('adultos', 'Selecione o número de adultos');
        valido = false;
    }
    
    // Validar Passo 2
    const nomeCompleto = document.getElementById('nome_completo').value;
    const email = document.getElementById('email').value;
    const cpf = document.getElementById('cpf').value;
    const telefone = document.getElementById('telefone').value;
    
    if (!nomeCompleto || nomeCompleto.trim().length < 3) {
        mostrarErro('nome_completo', 'Nome completo é obrigatório (mínimo 3 caracteres)');
        valido = false;
    }
    
    if (!email) {
        mostrarErro('email', 'E-mail é obrigatório');
        valido = false;
    } else if (!validarEmail(email)) {
        mostrarErro('email', 'E-mail inválido');
        valido = false;
    }
    
    if (!cpf) {
        mostrarErro('cpf', 'CPF é obrigatório');
        valido = false;
    } else if (!validarCPF(cpf)) {
        mostrarErro('cpf', 'CPF inválido');
        valido = false;
    }
    
    if (!telefone) {
        mostrarErro('telefone', 'Telefone é obrigatório');
        valido = false;
    } else if (!validarTelefone(telefone)) {
        mostrarErro('telefone', 'Telefone inválido');
        valido = false;
    }
    
    return valido;
}

// ============================================
// INICIALIZAÇÃO
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    configurarValidacaoTempoReal();
});

function abrirModalDatasInvalidas() {
    const el = document.getElementById('modalDatasInvalidas');
    if (!el) return;
    try {
        const modal = bootstrap && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(el) : null;
        if (modal) modal.show();
    } catch (_) {
        // fallback silencioso caso bootstrap não esteja disponível
        alert('Data de saída deve ser posterior à data de entrada');
    }
}
