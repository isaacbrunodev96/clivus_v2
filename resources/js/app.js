import './bootstrap';

// Formatação de valores monetários
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CPF
    const cpfInputs = document.querySelectorAll('input[name="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            }
        });
    });

    // Formatação de valores monetários nos inputs antigos
    const amountInputs = document.querySelectorAll('input[name="amount"], input[name="balance"]');
    amountInputs.forEach(input => {
        input.addEventListener('blur', function(e) {
            if (e.target.value) {
                // If it already has a comma, we leave it or replace it, but let's just let it be
                let valStr = e.target.value.replace(/\./g, '').replace(',', '.');
                const value = parseFloat(valStr);
                if (!isNaN(value)) {
                    e.target.value = value.toFixed(2).replace('.', ',');
                }
            }
        });
    });

    // Nova máscara de dinheiro para inputs com a classe .money-mask
    const moneyMaskInputs = document.querySelectorAll('.money-mask');
    moneyMaskInputs.forEach(input => {
        // Formata o valor inicial se já existir (vindo do banco, ex: 17.00 ou 16.96)
        if (input.value) {
            let val = parseFloat(input.value);
            if (!isNaN(val)) {
                input.value = val.toFixed(2).replace('.', ',');
            }
        }
        
        input.addEventListener('blur', function(e) {
            let rawValue = e.target.value.trim();
            if (!rawValue) return;
            
            // Se o usuário digitou apenas números sem vírgula/ponto, ex: "7"
            if (/^\d+$/.test(rawValue)) {
                e.target.value = rawValue + ',00';
            } 
            // Se digitou com ponto
            else if (rawValue.includes('.') && !rawValue.includes(',')) {
                let val = parseFloat(rawValue);
                if (!isNaN(val)) e.target.value = val.toFixed(2).replace('.', ',');
            }
            // Se já tem vírgula, garante que tem 2 casas decimais
            else if (rawValue.includes(',')) {
                let parts = rawValue.split(',');
                if (parts[1].length === 0) e.target.value = rawValue + '00';
                else if (parts[1].length === 1) e.target.value = rawValue + '0';
            }
        });

        // Impede letras
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9.,]/g, '');
        });
    });
});
