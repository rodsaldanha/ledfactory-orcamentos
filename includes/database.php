<?php
/**
 * LED Factory - Classe de Banco de Dados
 * Conexão PDO, schema e operações CRUD
 */

class Database {

    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB Connection Error: ' . $e->getMessage());
            die('Erro de conexão com o banco de dados.');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Cria as tabelas necessárias
     */
    public function setupSchema() {
        $sql = "
        CREATE TABLE IF NOT EXISTS orcamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero VARCHAR(20) NOT NULL UNIQUE,
            data_emissao DATE NOT NULL,
            data_validade DATE NOT NULL,
            
            -- Dados do Cliente
            cliente_nome VARCHAR(255) DEFAULT '',
            cliente_telefone VARCHAR(50) DEFAULT '',
            cliente_cidade VARCHAR(150) DEFAULT '',
            
            -- Especificações do Painel
            tipo_painel VARCHAR(100) DEFAULT '',
            tamanho_painel VARCHAR(100) DEFAULT '',
            pitch VARCHAR(50) DEFAULT '',
            dupla_face VARCHAR(20) DEFAULT '',
            sistema_som VARCHAR(100) DEFAULT '',
            tipo_suporte VARCHAR(100) DEFAULT '',
            
            -- Entrega
            frete VARCHAR(100) DEFAULT '',
            prazo_entrega VARCHAR(100) DEFAULT '',
            
            -- Pagamento
            forma_pagamento VARCHAR(255) DEFAULT '',
            observacoes TEXT,
            
            -- Valor
            valor_total DECIMAL(12,2) DEFAULT 0.00,
            
            -- Metadados
            pdf_path VARCHAR(500) DEFAULT '',
            ip_criacao VARCHAR(45) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_numero (numero),
            INDEX idx_data (data_emissao),
            INDEX idx_cliente (cliente_nome)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS configuracoes (
            chave VARCHAR(100) PRIMARY KEY,
            valor VARCHAR(500) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->pdo->exec($sql);

        // Inserir contador inicial se não existir
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO configuracoes (chave, valor) VALUES ('ultimo_numero', '0')");
        $stmt->execute();
    }

    /**
     * Gera próximo número sequencial
     */
    public function getNextNumber() {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("SELECT valor FROM configuracoes WHERE chave = 'ultimo_numero' FOR UPDATE");
            $stmt->execute();
            $row = $stmt->fetch();
            $next = intval($row['valor']) + 1;

            $stmt = $this->pdo->prepare("UPDATE configuracoes SET valor = ? WHERE chave = 'ultimo_numero'");
            $stmt->execute([$next]);

            $this->pdo->commit();
            return 'LEDF-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Salva orçamento
     */
    public function saveQuote($data) {
        $sql = "INSERT INTO orcamentos (
            numero, data_emissao, data_validade,
            cliente_nome, cliente_telefone, cliente_cidade,
            tipo_painel, tamanho_painel, pitch, dupla_face, sistema_som, tipo_suporte,
            frete, prazo_entrega, forma_pagamento, observacoes,
            valor_total, pdf_path, ip_criacao
        ) VALUES (
            :numero, :data_emissao, :data_validade,
            :cliente_nome, :cliente_telefone, :cliente_cidade,
            :tipo_painel, :tamanho_painel, :pitch, :dupla_face, :sistema_som, :tipo_suporte,
            :frete, :prazo_entrega, :forma_pagamento, :observacoes,
            :valor_total, :pdf_path, :ip_criacao
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    /**
     * Busca orçamento por ID
     */
    public function getQuoteById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orcamentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca orçamento por número
     */
    public function getQuoteByNumber($numero) {
        $stmt = $this->pdo->prepare("SELECT * FROM orcamentos WHERE numero = ?");
        $stmt->execute([$numero]);
        return $stmt->fetch();
    }

    /**
     * Lista orçamentos com paginação e busca
     */
    public function listQuotes($search = '', $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if (!empty($search)) {
            $where = "WHERE numero LIKE :search OR cliente_nome LIKE :search2 OR cliente_cidade LIKE :search3";
            $params = [
                ':search'  => "%{$search}%",
                ':search2' => "%{$search}%",
                ':search3' => "%{$search}%",
            ];
        }

        // Total
        $countSql = "SELECT COUNT(*) as total FROM orcamentos {$where}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch()['total'];

        // Registros
        $sql = "SELECT id, numero, data_emissao, cliente_nome, cliente_cidade, valor_total, pdf_path, created_at 
                FROM orcamentos {$where} 
                ORDER BY id DESC 
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];
    }

    /**
     * Deleta orçamento
     */
    public function deleteQuote($id) {
        $quote = $this->getQuoteById($id);
        if ($quote && !empty($quote['pdf_path']) && file_exists($quote['pdf_path'])) {
            unlink($quote['pdf_path']);
        }
        $stmt = $this->pdo->prepare("DELETE FROM orcamentos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
