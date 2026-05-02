<?php
/**
 * LED Factory - Gerador de PDF
 * Usa Dompdf para gerar PDF idêntico ao layout aprovado
 */

require_once BASE_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFGenerator {

    /**
     * Gera o PDF do orçamento e salva em disco
     * 
     * @param array $data Dados do orçamento
     * @return string Caminho do arquivo salvo
     */
    public static function generate($data) {
        // Renderiza o template HTML
        ob_start();
        include TEMPLATES_PATH . '/pdf_template.php';
        $html = ob_get_clean();

        // Configurar Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('chroot', BASE_PATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Criar pasta de logs se não existir
        if (!file_exists(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0750, true);
        }

        // Salvar PDF
        $filename = $data['numero'] . '_' . date('Ymd_His') . '.pdf';
        $filepath = LOGS_PATH . '/' . $filename;
        file_put_contents($filepath, $dompdf->output());

        // Comprimir em ZIP
        $zipPath = $filepath . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $zip->addFile($filepath, $filename);
            $zip->close();
            // Remover PDF original, manter só o ZIP
            unlink($filepath);
        }

        return $zipPath;
    }

    /**
     * Gera PDF e retorna como stream para download
     * 
     * @param array $data Dados do orçamento
     * @return string PDF content
     */
    public static function generateStream($data) {
        ob_start();
        include TEMPLATES_PATH . '/pdf_template.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('chroot', BASE_PATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
