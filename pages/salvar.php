<?php
/**
 * Salvar Orçamento e Gerar PDF
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?p=novo');
    exit;
}

// Validar CSRF
if (!Security::validateCSRF($_POST['csrf_token'] ?? '')) {
    die('Token de segurança inválido.');
}

// Carregar dependências
require_once BASE_PATH . '/includes/pdf_generator.php';

$db = Database::getInstance();

// Gerar número sequencial
$numero = $db->getNextNumber();

// Data de emissão e validade
$dataEmissao = date('Y-m-d');
$days = QUOTE_VALIDITY_DAYS;
$dataValidade = date('Y-m-d', strtotime("+{$days} days"));

// Tratar valor (remover formatação BR)
$valorRaw = $_POST['valor_total'] ?? '0';
$valorRaw = str_replace('.', '', $valorRaw);  // remove separador de milhares
$valorRaw = str_replace(',', '.', $valorRaw); // troca vírgula por ponto
$valorTotal = floatval($valorRaw);

// Montar dados
$data = [
    ':numero'          => $numero,
    ':data_emissao'    => $dataEmissao,
    ':data_validade'   => $dataValidade,
    ':cliente_nome'    => Security::sanitize($_POST['cliente_nome'] ?? ''),
    ':cliente_telefone'=> Security::sanitize($_POST['cliente_telefone'] ?? ''),
    ':cliente_cidade'  => Security::sanitize($_POST['cliente_cidade'] ?? ''),
    ':tipo_painel'     => Security::sanitize($_POST['tipo_painel'] ?? ''),
    ':tamanho_painel'  => Security::sanitize($_POST['tamanho_painel'] ?? ''),
    ':pitch'           => Security::sanitize($_POST['pitch'] ?? ''),
    ':dupla_face'      => Security::sanitize($_POST['dupla_face'] ?? ''),
    ':sistema_som'     => Security::sanitize($_POST['sistema_som'] ?? ''),
    ':tipo_suporte'    => Security::sanitize($_POST['tipo_suporte'] ?? ''),
    ':frete'           => Security::sanitize($_POST['frete'] ?? ''),
    ':prazo_entrega'   => Security::sanitize($_POST['prazo_entrega'] ?? ''),
    ':forma_pagamento' => Security::sanitize($_POST['forma_pagamento'] ?? ''),
    ':observacoes'     => Security::sanitize($_POST['observacoes'] ?? ''),
    ':valor_total'     => $valorTotal,
    ':pdf_path'        => '',
    ':ip_criacao'      => Security::getClientIP(),
];

// Dados para o template PDF (sem os ":")
$pdfData = [];
foreach ($data as $key => $val) {
    $pdfData[ltrim($key, ':')] = $val;
}

try {
    // Gerar PDF e salvar em disco (ZIP)
    $zipPath = PDFGenerator::generate($pdfData);

    // Atualizar caminho no array
    $data[':pdf_path'] = $zipPath;

    // Salvar no banco
    $insertId = $db->saveQuote($data);

    // Log
    $logMsg = date('Y-m-d H:i:s') . " | ORCAMENTO CRIADO | {$numero} | Cliente: {$pdfData['cliente_nome']} | Valor: R$ " . number_format($valorTotal, 2, ',', '.') . " | IP: " . Security::getClientIP() . PHP_EOL;
    file_put_contents(LOGS_PATH . '/orcamentos.log', $logMsg, FILE_APPEND | LOCK_EX);

    // Redirecionar com sucesso
    header("Location: ?p=novo&success=1&id={$insertId}");
    exit;

} catch (Exception $e) {
    error_log('Erro ao gerar orçamento: ' . $e->getMessage());
    die('<div style="background:#0A0A0A;color:#E74C3C;font-family:sans-serif;padding:40px;text-align:center;"><h2>Erro ao gerar orçamento</h2><p>' . htmlspecialchars($e->getMessage()) . '</p><p><a href="?p=novo" style="color:#149BB9;">Voltar</a></p></div>');
}
