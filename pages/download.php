<?php
/**
 * Download do PDF
 * Extrai do ZIP e serve para download
 */

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ?p=dashboard');
    exit;
}

$db = Database::getInstance();
$quote = $db->getQuoteById($id);

if (!$quote || empty($quote['pdf_path'])) {
    die('<div style="background:#0A0A0A;color:#E74C3C;font-family:sans-serif;padding:40px;text-align:center;"><h2>Orçamento não encontrado</h2><a href="?p=historico" style="color:#149BB9;">Voltar</a></div>');
}

$zipPath = $quote['pdf_path'];

if (!file_exists($zipPath)) {
    die('<div style="background:#0A0A0A;color:#E74C3C;font-family:sans-serif;padding:40px;text-align:center;"><h2>Arquivo não encontrado no servidor</h2><a href="?p=historico" style="color:#149BB9;">Voltar</a></div>');
}

// Extrair PDF do ZIP
$zip = new ZipArchive();
if ($zip->open($zipPath) === true) {
    $pdfContent = $zip->getFromIndex(0);
    $pdfName = $zip->getNameIndex(0);
    $zip->close();

    if ($pdfContent === false) {
        die('Erro ao extrair PDF do arquivo.');
    }

    // Servir PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $pdfName . '"');
    header('Content-Length: ' . strlen($pdfContent));
    header('Cache-Control: private, max-age=0, must-revalidate');
    echo $pdfContent;
    exit;
} else {
    die('Erro ao abrir arquivo ZIP.');
}
