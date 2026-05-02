<?php
/**
 * Histórico de Orçamentos
 * Com busca e paginação
 */

$db = Database::getInstance();
$search = Security::sanitize($_GET['q'] ?? '');
$page = max(1, intval($_GET['pg'] ?? 1));
$result = $db->listQuotes($search, $page, 20);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - <?php echo APP_NAME; ?></title>
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
            <a href="?p=novo" class="nav-link">Novo Orçamento</a>
            <a href="?p=historico" class="nav-link active">Histórico</a>
            <a href="?p=logout" class="nav-link nav-link-logout">Sair</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="app-content">

        <h1 class="page-title">HISTÓRICO</h1>
        <p class="page-subtitle"><?php echo $result['total']; ?> orçamento(s) encontrado(s)</p>

        <!-- SEARCH -->
        <form method="GET" class="search-bar">
            <input type="hidden" name="p" value="historico">
            <input type="text" name="q" class="form-input" placeholder="Buscar por número, cliente ou cidade..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if ($search): ?>
            <a href="?p=historico" class="btn btn-danger">Limpar</a>
            <?php endif; ?>
        </form>

        <!-- TABLE -->
        <div class="panel">
            <?php if (empty($result['data'])): ?>
                <p style="color: var(--text-secondary); font-size: 14px;">Nenhum orçamento encontrado.</p>
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
                        <?php foreach ($result['data'] as $row): ?>
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
                                    <a href="?p=deletar&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja deletar o orçamento <?php echo htmlspecialchars($row['numero']); ?>?');">×</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($result['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $result['totalPages']; $i++): ?>
                        <?php 
                        $params = ['p' => 'historico', 'pg' => $i];
                        if ($search) $params['q'] = $search;
                        $url = '?' . http_build_query($params);
                        ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo $url; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>
