<?php
/**
 * MITHOOOS — Database Class (Turso HTTP API Wrapper)
 * Replaces the PostgreSQL PDO wrapper.
 */

class TursoStatement {
    public $data;
    public $cols;
    public $affectedRows;
    public $lastInsertId;
    private $rowIndex = 0;

    public function __construct($data, $cols, $affectedRows, $lastInsertId) {
        $this->data = $data;
        $this->cols = $cols;
        $this->affectedRows = $affectedRows;
        $this->lastInsertId = $lastInsertId;
    }

    public function fetchAll() {
        return $this->data;
    }

    public function fetch() {
        if ($this->rowIndex < count($this->data)) {
            return $this->data[$this->rowIndex++];
        }
        return false;
    }

    public function fetchColumn() {
        $row = $this->fetch();
        if ($row) {
            return array_values($row)[0];
        }
        return false;
    }

    public function columnCount() {
        return count($this->cols);
    }

    public function rowCount() {
        return $this->affectedRows;
    }
}

class Database
{
    private static ?Database $instance = null;
    private int $transactionCounter = 0;
    private ?string $baton = null;
    private ?string $baseUrl = null;
    private string $tursoUrl;
    private string $authToken;

    private function __construct()
    {
        $url = getenv('TURSO_DATABASE_URL');
        $token = getenv('TURSO_AUTH_TOKEN');

        if (!$url || !$token) {
            throw new Exception('Turso credentials missing. Check your .env (TURSO_DATABASE_URL and TURSO_AUTH_TOKEN).');
        }

        // Convert libsql:// or wss:// to https://
        $url = preg_replace('/^(libsql|wss|ws):\/\//', 'https://', $url);
        $this->tursoUrl = rtrim($url, '/');
        $this->authToken = $token;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __clone() {}

    private function formatArg($value) {
        if ($value === null) return ['type' => 'null'];
        if (is_int($value) || is_bool($value)) return ['type' => 'integer', 'value' => (string)(int)$value];
        if (is_float($value)) return ['type' => 'float', 'value' => (float)$value];
        // Handle BLOBs (basic check if string is valid UTF-8. If not, it's a blob)
        if (!mb_check_encoding($value, 'UTF-8')) {
            return ['type' => 'blob', 'base64' => base64_encode($value)];
        }
        return ['type' => 'text', 'value' => (string)$value];
    }

    private function parseValue($val) {
        if (!isset($val['type']) || $val['type'] === 'null') return null;
        if ($val['type'] === 'integer') return (int)$val['value'];
        if ($val['type'] === 'float') return (float)$val['value'];
        if ($val['type'] === 'blob') return base64_decode($val['base64']);
        return $val['value'];
    }

    private function sendRequest($requests) {
        $body = ['requests' => $requests];
        if ($this->baton) {
            $body['baton'] = $this->baton;
        }

        $url = ($this->baseUrl ?? $this->tursoUrl) . '/v2/pipeline';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->authToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 400 || !$response) {
            throw new PDOException("Turso HTTP Error ($httpCode): $response. cURL Error: $curlError");
        }

        $res = json_decode($response, true);
        
        if (isset($res['baton'])) {
            $this->baton = $res['baton'];
        }
        if (isset($res['base_url'])) {
            $this->baseUrl = $res['base_url'];
        }

        return $res['results'] ?? [];
    }

    public function query(string $sql, array $params = [])
    {
        // Simple positional (?) parameters conversion
        $args = array_map([$this, 'formatArg'], $params);
        
        // SQLite doesn't strictly require explicit boolean or JSON casts that Postgres uses
        // but we'll send it as is.
        $request = [
            'type' => 'execute',
            'stmt' => [
                'sql' => $sql,
                'args' => $args
            ]
        ];

        $results = $this->sendRequest([$request]);
        $firstResult = $results[0] ?? null;

        if (!$firstResult || $firstResult['type'] === 'error') {
            $errMsg = $firstResult['error']['message'] ?? 'Unknown database error';
            // Mock Postgres duplicate key violation for idempotency logic
            if (stripos($errMsg, 'UNIQUE constraint failed') !== false) {
                throw new PDOException($errMsg, 23505);
            }
            throw new PDOException($errMsg);
        }

        $resultData = $firstResult['response']['result'] ?? [];
        $cols = $resultData['cols'] ?? [];
        $rawRows = $resultData['rows'] ?? [];
        
        $data = [];
        foreach ($rawRows as $row) {
            $assoc = [];
            foreach ($row as $i => $val) {
                $colName = $cols[$i]['name'] ?? $i;
                $assoc[$colName] = $this->parseValue($val);
            }
            $data[] = $assoc;
        }

        return new TursoStatement(
            $data, 
            $cols, 
            $resultData['affected_row_count'] ?? 0, 
            $resultData['last_insert_rowid'] ?? '0'
        );
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function insert(string $sql, array $params = []): int
    {
        $statement = $this->query($sql, $params);
        if ($statement->columnCount() === 0) {
            return (int) $statement->lastInsertId;
        }
        $id = $statement->fetchColumn();
        return $id === false ? (int) $statement->lastInsertId : (int) $id;
    }

    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId(): string|false
    {
        // Needs a dummy query to fetch last_insert_rowid() if not tracked properly.
        // We'll rely on insert() returning it.
        return $this->fetchColumn("SELECT last_insert_rowid()");
    }

    public function beginTransaction(): void {
        if (!$this->transactionCounter++) {
            $this->baton = null; // Reset baton for new transaction
            $this->baseUrl = null;
            $this->query("BEGIN");
        }
    }
    
    public function commit(): void {
        if (!--$this->transactionCounter) {
            $this->query("COMMIT");
            $this->sendRequest([['type' => 'close']]);
            $this->baton = null;
            $this->baseUrl = null;
        }
    }
    
    public function rollBack(): void {
        if ($this->transactionCounter > 0) {
            $this->transactionCounter = 0;
            try {
                $this->query("ROLLBACK");
                $this->sendRequest([['type' => 'close']]);
            } catch (Exception $e) {}
            $this->baton = null;
            $this->baseUrl = null;
        }
    }

    // Aliases
    public function run(string $sql, array $params = []): array { return $this->fetchAll($sql, $params); }
    public function runOne(string $sql, array $params = []): array|false { return $this->fetchOne($sql, $params); }
    public function runExec(string $sql, array $params = []): int { return $this->execute($sql, $params); }

    public function paginate(string $sql, array $params, int $page, int $perPage): array
    {
        $total   = (int) $this->fetchColumn("SELECT COUNT(*) FROM ({$sql}) AS _c", $params);
        $offset  = ($page - 1) * $perPage;
        $data    = $this->fetchAll("{$sql} LIMIT ? OFFSET ?", [...$params, $perPage, $offset]);

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'total_pages'  => (int) ceil($total / $perPage),
            'has_next'     => $page < ceil($total / $perPage),
            'has_prev'     => $page > 1,
        ];
    }
}
