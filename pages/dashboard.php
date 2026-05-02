<?php
/**
 * Dashboard Principal
 */
$db = Database::getInstance();
$recent = $db->listQuotes('', 1, 10);

// Stats
$pdo = $db->getPDO();
$totalQuotes = $pdo->query("SELECT COUNT(*) as c FROM orcamentos")->fetch()['c'] ?? 0;
$totalValue = $pdo->query("SELECT COALESCE(SUM(valor_total), 0) as s FROM orcamentos")->fetch()['s'] ?? 0;
$monthQuotes = $pdo->query("SELECT COUNT(*) as c FROM orcamentos WHERE MONTH(data_emissao) = MONTH(CURRENT_DATE()) AND YEAR(data_emissao) = YEAR(CURRENT_DATE())")->fetch()['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
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
            <a href="?p=dashboard" class="nav-link active">Dashboard</a>
            <a href="?p=novo" class="nav-link">Novo Orçamento</a>
            <a href="?p=historico" class="nav-link">Histórico</a>
            <a href="?p=logout" class="nav-link nav-link-logout">Sair</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="app-content">

        <h1 class="page-title">DASHBOARD</h1>
        <p class="page-subtitle">Visão geral do sistema de orçamentos</p>

        <!-- STATS -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalQuotes; ?></div>
                <div class="stat-label">Total de Orçamentos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $monthQuotes; ?></div>
                <div class="stat-label">Este Mês</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">R$ <?php echo number_format($totalValue, 0, ',', '.'); ?></div>
                <div class="stat-label">Valor Total</div>
            </div>
        </div>

        <!-- QUICK ACTION -->
        <div style="margin-bottom: 28px;">
            <a href="?p=novo" class="btn btn-primary">+ Novo Orçamento</a>
        </div>

        <!-- RECENT QUOTES -->
        <div class="panel">
            <div class="panel-title">Últimos Orçamentos</div>

            <?php if (empty($recent['data'])): ?>
                <p style="color: var(--text-secondary); font-size: 14px;">Nenhum orçamento criado ainda.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Cidade</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recent['data'] as $row): ?>
                            <tr>
                                <td style="color: var(--accent); font-weight: 700;"><?php echo htmlspecialchars($row['numero']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['data_emissao'])); ?></td>
                                <td><?php echo htmlspecialchars($row['cliente_nome'] ?: '—'); ?></td>
                                <td><?php echo htmlspecialchars($row['cliente_cidade'] ?: '—'); ?></td>
                                <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?></td>
                                <td class="actions">
                                    <?php if (!empty($row['pdf_path']) && file_exists($row['pdf_path'])): ?>
                                    <a href="?p=download&id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">PDF</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>
