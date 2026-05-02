<?php
/**
 * Deletar Orçamento
 */

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ?p=historico');
    exit;
}

$db = Database::getInstance();
$quote = $db->getQuoteById($id);

if ($quote) {
    $db->deleteQuote($id);

    // Log
    $logMsg = date('Y-m-d H:i:s') . " | ORCAMENTO DELETADO | {$quote['numero']} | IP: " . Security::getClientIP() . PHP_EOL;
    file_put_contents(LOGS_PATH . '/orcamentos.log', $logMsg, FILE_APPEND | LOCK_EX);
}

header('Location: ?p=historico');
exit;
