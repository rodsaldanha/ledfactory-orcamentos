<?php
/**
 * Formulário de Novo Orçamento
 */
$csrf = Security::generateCSRF();
$success = isset($_GET['success']) ? true : false;
$newId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Orçamento - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
<div class="app-wrapper">

    <!-- HEADER -->
    <div class="app-header">
        <div class="app-header-logo">
            <img src="assets/img/logo.png" alt="LED Factory">
        </div>
        <div class="app-header-nav">
            <a href="?p=dashboard" class="nav-link">Dashboard</a>
            <a href="?p=novo" class="nav-link active">Novo Orçamento</a>
            <a href="?p=historico" class="nav-link">Histórico</a>
            <a href="?p=logout" class="nav-link nav-link-logout">Sair</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="app-content">

        <h1 class="page-title">NOVO ORÇAMENTO</h1>
        <p class="page-subtitle">Preencha os dados abaixo para gerar o PDF</p>

        <?php if ($success && $newId): ?>
        <div class="modal-overlay" id="successModal">
            <div class="modal-box">
                <div class="modal-icon">✓</div>
                <div class="modal-title">Orçamento Gerado!</div>
                <p style="color: var(--text-secondary); margin-bottom: 8px;">PDF criado e salvo com sucesso.</p>
                <div class="modal-actions">
                    <a href="?p=download&id=<?php echo $newId; ?>" class="btn btn-primary">Baixar PDF</a>
                    <a href="?p=novo" class="btn btn-success">Novo Orçamento</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="?p=salvar">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <!-- DADOS DO CLIENTE -->
            <div class="panel">
                <div class="panel-title">Dados do Cliente</div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nome / Razão Social</label>
                        <input type="text" name="cliente_nome" class="form-input" placeholder="Nome completo ou razão social">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="cliente_telefone" class="form-input" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">Cidade / Estado</label>
                        <input type="text" name="cliente_cidade" class="form-input" placeholder="São Paulo / SP">
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- ESPECIFICAÇÕES DO PAINEL -->
            <div class="panel">
                <div class="panel-title">Especificações do Painel</div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label class="form-label">Tipo de Painel</label>
                        <select name="tipo_painel" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="Indoor">Indoor</option>
                            <option value="Outdoor">Outdoor</option>
                            <option value="Semi-Outdoor">Semi-Outdoor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tamanho do Painel</label>
                        <input type="text" name="tamanho_painel" class="form-input" placeholder="Ex: 3m x 2m">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pitch (mm)</label>
                        <select name="pitch" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="P1.5">P1.5</option>
                            <option value="P2">P2</option>
                            <option value="P2.5">P2.5</option>
                            <option value="P3">P3</option>
                            <option value="P4">P4</option>
                            <option value="P5">P5</option>
                            <option value="P6">P6</option>
                            <option value="P8">P8</option>
                            <option value="P10">P10</option>
                        </select>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label class="form-label">Dupla Face</label>
                        <select name="dupla_face" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="Sim">Sim</option>
                            <option value="Não">Não</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sistema de Som</label>
                        <select name="sistema_som" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="Sim - Integrado">Sim - Integrado</option>
                            <option value="Sim - Externo">Sim - Externo</option>
                            <option value="Não">Não</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipo de Suporte</label>
                        <select name="tipo_suporte" class="form-select">
                            <option value="">Selecione...</option>
                            <option value="Fixação em parede">Fixação em parede</option>
                            <option value="Estrutura metálica">Estrutura metálica</option>
                            <option value="Piso / Base">Piso / Base</option>
                            <option value="Suspenso">Suspenso</option>
                            <option value="Customizado">Customizado</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ENTREGA + PAGAMENTO -->
            <div class="form-row form-row-2">
                <div class="panel">
                    <div class="panel-title">Entrega</div>
                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label class="form-label">Frete</label>
                            <select name="frete" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="Incluso">Incluso</option>
                                <option value="Por conta do cliente">Por conta do cliente</option>
                                <option value="A combinar">A combinar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prazo de Entrega</label>
                            <input type="text" name="prazo_entrega" class="form-input" placeholder="Ex: 30 dias úteis">
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-title">Condições de Pagamento</div>
                    <div class="form-group">
                        <label class="form-label">Forma de Pagamento</label>
                        <input type="text" name="forma_pagamento" class="form-input" placeholder="Ex: 50% entrada + 50% na entrega">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-input" placeholder="Notas adicionais..."></textarea>
                    </div>
                </div>
            </div>

            <!-- VALOR -->
            <div class="panel">
                <div class="panel-title">Valor do Investimento</div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label class="form-label">Valor Total (R$)</label>
                        <input type="text" name="valor_total" class="form-input" placeholder="0,00" id="valorInput">
                    </div>
                    <div></div>
                </div>
            </div>

            <!-- SUBMIT -->
            <div style="display: flex; gap: 16px; justify-content: flex-end;">
                <a href="?p=dashboard" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">Gerar Orçamento PDF</button>
            </div>

        </form>

    </div>
</div>

<script>
// Máscara de valor
document.getElementById('valorInput').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');
    if (v.length === 0) { e.target.value = ''; return; }
    v = (parseInt(v) / 100).toFixed(2);
    v = v.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    e.target.value = v;
});

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
    }
});
</script>
</body>
</html>
