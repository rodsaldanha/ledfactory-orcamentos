<?php
/**
 * LED Factory - Template do PDF de Orçamento
 * Gera o HTML que será convertido em PDF pelo Dompdf
 * 
 * Variáveis esperadas: $data (array com dados do orçamento)
 */

// Converter imagens para base64
$logoPath = ASSETS_PATH . '/img/logo.png';
$fachadaPath = ASSETS_PATH . '/img/fachada.jpg';
$piscinaPath = ASSETS_PATH . '/img/piscina.jpg';

$logoB64 = base64_encode(file_get_contents($logoPath));
$fachadaB64 = base64_encode(file_get_contents($fachadaPath));
$piscinaB64 = base64_encode(file_get_contents($piscinaPath));

// Formatar valor
$valorFormatado = !empty($data['valor_total']) && $data['valor_total'] > 0
    ? 'R$ ' . number_format($data['valor_total'], 2, ',', '.')
    : 'R$ _____________';

// Formatar datas
$dataEmissao = !empty($data['data_emissao']) 
    ? date('d/m/Y', strtotime($data['data_emissao'])) 
    : '____/____/________';
$dataValidade = !empty($data['data_validade']) 
    ? date('d/m/Y', strtotime($data['data_validade'])) 
    : '____/____/________';

// Helper para campos vazios
function fieldVal($val) {
    return !empty($val) ? htmlspecialchars($val) : '&nbsp;';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
@page {
  size: A4;
  margin: 0;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Helvetica', 'Arial', sans-serif;
  font-weight: bold;
  background: #0A0A0A;
  color: #F5F5F5;
  width: 210mm;
  height: 297mm;
  margin: 0;
  position: relative;
}

.page {
  width: 210mm;
  height: 297mm;
  position: relative;
  overflow: hidden;
}

.content {
  padding: 22mm 18mm 20mm 18mm;
  position: relative;
  z-index: 1;
}

/* HEADER */
.header {
  width: 100%;
  margin-bottom: 5mm;
}

.header-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.header-table td {
  border: none;
  vertical-align: middle;
  padding: 0;
}

.logo-img {
  height: 44px;
}

.header-title {
  font-family: 'Helvetica', sans-serif;
  font-weight: bold;
  font-size: 26px;
  letter-spacing: 5px;
  color: #FFFFFF;
  text-align: right;
  text-transform: uppercase;
}

.header-subtitle {
  font-size: 10px;
  color: #999999;
  letter-spacing: 3px;
  text-align: right;
  text-transform: uppercase;
  margin-top: 3px;
}

/* NEON DIVIDER */
.neon-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, #149BB9, transparent);
  margin: 4mm 0;
}

.neon-divider-thin {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(20,155,185,0.35), transparent);
  margin: 3mm 0;
}

/* DOC INFO */
.doc-info {
  width: 100%;
  border: none;
  border-collapse: collapse;
  margin-bottom: 3mm;
}

.doc-info td {
  border: none;
  padding: 2px 0;
}

.doc-label {
  font-size: 9px;
  color: #999999;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.doc-value {
  font-size: 12px;
  color: #F5F5F5;
  letter-spacing: 1px;
}

/* SECTION */
.section {
  margin-bottom: 4mm;
}

.section-title {
  font-size: 10px;
  font-weight: bold;
  letter-spacing: 4px;
  color: #149BB9;
  text-transform: uppercase;
  margin-bottom: 2mm;
}

/* CARD */
.card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(20,155,185,0.15);
  border-radius: 6px;
  padding: 10px 14px;
}

/* FIELD TABLE */
.field-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.field-table td {
  border: none;
  padding: 4px 8px 4px 0;
  vertical-align: top;
}

.field-label {
  font-size: 9px;
  font-weight: bold;
  color: #149BB9;
  text-transform: uppercase;
  letter-spacing: 1.2px;
  padding-bottom: 1px;
}

.field-value {
  font-size: 12px;
  font-weight: bold;
  color: #F5F5F5;
  border-bottom: 1px solid rgba(20,155,185,0.2);
  padding-bottom: 3px;
  min-height: 18px;
}

/* VALOR BOX */
.valor-box {
  background: rgba(20,155,185,0.05);
  border: 1px solid rgba(20,155,185,0.25);
  border-radius: 8px;
  padding: 12px 18px;
}

.valor-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.valor-table td {
  border: none;
  vertical-align: middle;
  padding: 0;
}

.valor-label {
  font-size: 10px;
  font-weight: bold;
  color: #999999;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.valor-sub {
  font-size: 9px;
  color: #666666;
  margin-top: 2px;
}

.valor-amount {
  font-size: 20px;
  font-weight: bold;
  color: #F5F5F5;
  text-align: right;
  letter-spacing: 1px;
}

/* IMAGES */
.images-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
  margin-top: 3mm;
}

.images-table td {
  border: none;
  padding: 0;
  width: 50%;
  vertical-align: top;
}

.img-card {
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid rgba(20,155,185,0.12);
  position: relative;
  margin: 0 4px;
}

.img-card img {
  width: 100%;
  height: 110px;
  object-fit: cover;
  display: block;
}

.img-caption {
  font-size: 7px;
  font-weight: bold;
  color: #8ED7E7;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  padding: 4px 8px;
  background: rgba(10,10,10,0.7);
}

/* FOOTER */
.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 8px 18mm 10px 18mm;
}

.footer-line {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(20,155,185,0.3), transparent);
  margin-bottom: 8px;
}

.footer-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.footer-table td {
  border: none;
  font-size: 8px;
  font-weight: bold;
  color: #999999;
  vertical-align: middle;
  padding: 0 4px;
}

.footer-icon {
  color: #149BB9;
  font-size: 10px;
  margin-right: 3px;
}

.footer-site {
  font-size: 7px;
  color: #0F6F83;
  letter-spacing: 2px;
  text-align: right;
}

/* INLINE SECTIONS */
.inline-table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.inline-table > tbody > tr > td {
  border: none;
  width: 50%;
  vertical-align: top;
  padding: 0;
}

.inline-table > tbody > tr > td:first-child {
  padding-right: 6px;
}

.inline-table > tbody > tr > td:last-child {
  padding-left: 6px;
}
</style>
</head>
<body>
<div class="page">
  <div class="content">

    <!-- HEADER -->
    <table class="header-table">
      <tr>
        <td style="width:40%;">
          <img class="logo-img" src="data:image/png;base64,<?php echo $logoB64; ?>" alt="LED Factory">
        </td>
        <td style="width:60%;">
          <div class="header-title">ORÇAMENTO</div>
          <div class="header-subtitle">Proposta Comercial</div>
        </td>
      </tr>
    </table>

    <div class="neon-divider"></div>

    <!-- DOC INFO -->
    <table class="doc-info">
      <tr>
        <td style="width:33%;">
          <span class="doc-label">Nº</span>&nbsp;&nbsp;
          <span class="doc-value"><?php echo htmlspecialchars($data['numero']); ?></span>
        </td>
        <td style="width:33%;text-align:center;">
          <span class="doc-label">Data</span>&nbsp;&nbsp;
          <span class="doc-value"><?php echo $dataEmissao; ?></span>
        </td>
        <td style="width:33%;text-align:right;">
          <span class="doc-label">Validade</span>&nbsp;&nbsp;
          <span class="doc-value"><?php echo $dataValidade; ?></span>
        </td>
      </tr>
    </table>

    <div class="neon-divider-thin"></div>

    <!-- DADOS DO CLIENTE -->
    <div class="section">
      <div class="section-title">Dados do Cliente</div>
      <div class="card">
        <table class="field-table">
          <tr>
            <td colspan="2">
              <div class="field-label">Nome / Razão Social</div>
              <div class="field-value"><?php echo fieldVal($data['cliente_nome']); ?></div>
            </td>
          </tr>
          <tr>
            <td style="width:50%;">
              <div class="field-label">Telefone / WhatsApp</div>
              <div class="field-value"><?php echo fieldVal($data['cliente_telefone']); ?></div>
            </td>
            <td style="width:50%;">
              <div class="field-label">Cidade / Estado</div>
              <div class="field-value"><?php echo fieldVal($data['cliente_cidade']); ?></div>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!-- ESPECIFICAÇÕES -->
    <div class="section">
      <div class="section-title">Especificações do Painel</div>
      <div class="card">
        <table class="field-table">
          <tr>
            <td style="width:33%;">
              <div class="field-label">Tipo de Painel</div>
              <div class="field-value"><?php echo fieldVal($data['tipo_painel']); ?></div>
            </td>
            <td style="width:33%;">
              <div class="field-label">Tamanho do Painel</div>
              <div class="field-value"><?php echo fieldVal($data['tamanho_painel']); ?></div>
            </td>
            <td style="width:33%;">
              <div class="field-label">Pitch (mm)</div>
              <div class="field-value"><?php echo fieldVal($data['pitch']); ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="field-label">Dupla Face</div>
              <div class="field-value"><?php echo fieldVal($data['dupla_face']); ?></div>
            </td>
            <td>
              <div class="field-label">Sistema de Som</div>
              <div class="field-value"><?php echo fieldVal($data['sistema_som']); ?></div>
            </td>
            <td>
              <div class="field-label">Tipo de Suporte</div>
              <div class="field-value"><?php echo fieldVal($data['tipo_suporte']); ?></div>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!-- ENTREGA + PAGAMENTO -->
    <div class="section">
      <table class="inline-table">
        <tr>
          <td>
            <div class="section-title">Entrega</div>
            <div class="card">
              <table class="field-table">
                <tr>
                  <td style="width:50%;">
                    <div class="field-label">Frete</div>
                    <div class="field-value"><?php echo fieldVal($data['frete']); ?></div>
                  </td>
                  <td style="width:50%;">
                    <div class="field-label">Prazo de Entrega</div>
                    <div class="field-value"><?php echo fieldVal($data['prazo_entrega']); ?></div>
                  </td>
                </tr>
              </table>
            </div>
          </td>
          <td>
            <div class="section-title">Condições de Pagamento</div>
            <div class="card">
              <table class="field-table">
                <tr>
                  <td>
                    <div class="field-label">Forma de Pagamento</div>
                    <div class="field-value"><?php echo fieldVal($data['forma_pagamento']); ?></div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="field-label">Observações</div>
                    <div class="field-value"><?php echo fieldVal($data['observacoes']); ?></div>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <!-- VALOR -->
    <div class="section">
      <div class="valor-box">
        <table class="valor-table">
          <tr>
            <td style="width:55%;">
              <div class="valor-label">Valor Total do Investimento</div>
              <div class="valor-sub">Valor sem impostos inclusos</div>
            </td>
            <td style="width:45%;">
              <div class="valor-amount"><?php echo $valorFormatado; ?></div>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!-- IMAGES -->
    <table class="images-table">
      <tr>
        <td>
          <div class="img-card">
            <img src="data:image/jpeg;base64,<?php echo $fachadaB64; ?>" alt="Fachada">
            <div class="img-caption">Fachada Comercial</div>
          </div>
        </td>
        <td>
          <div class="img-card">
            <img src="data:image/jpeg;base64,<?php echo $piscinaB64; ?>" alt="Piscina">
            <div class="img-caption">LED para Piscinas</div>
          </div>
        </td>
      </tr>
    </table>

  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div class="footer-line"></div>
    <table class="footer-table">
      <tr>
        <td><span class="footer-icon">✉</span> <?php echo CONTACT_EMAIL; ?></td>
        <td><span class="footer-icon">✆</span> <?php echo CONTACT_PHONE; ?></td>
        <td><span class="footer-icon">@</span> <?php echo CONTACT_INSTAGRAM; ?></td>
        <td class="footer-site"><?php echo CONTACT_SITE; ?></td>
      </tr>
    </table>
  </div>

</div>
</body>
</html>
