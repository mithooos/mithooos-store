<?php
class Response {
    private static function getBaseUrl(): string {
        // Prefer APP_URL when it's a real production domain (not localhost)
        $appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
        if ($appUrl && !str_contains($appUrl, 'localhost') && !str_contains($appUrl, '127.0.0.1')) {
            return $appUrl;
        }
        // In development: build URL from the current request so port is always correct
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost'; // includes port e.g. localhost:8000
        return $scheme . '://' . $host;
    }

    public static function fixImageUrls(mixed &$data): void {
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_string($value) && str_starts_with($value, '/uploads/')) {
                    $value = self::getBaseUrl() . $value;
                } elseif (is_array($value) || is_object($value)) {
                    self::fixImageUrls($value);
                }
            }
        } elseif (is_object($data)) {
            foreach (get_object_vars($data) as $key => $value) {
                if (is_string($value) && str_starts_with($value, '/uploads/')) {
                    $data->$key = self::getBaseUrl() . $value;
                } elseif (is_array($value) || is_object($value)) {
                    self::fixImageUrls($data->$key);
                }
            }
        }
    }

    public static function json(array $data, int $code = 200): void {
        self::fixImageUrls($data);
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): void {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }
    public static function error(string $message = 'Error', int $code = 400, mixed $errors = null): void {
        self::json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }
    public static function paginated(array $data, int $total, int $limit, int $offset): void {
        self::json(['success' => true, 'data' => $data, 'pagination' => [
            'total'   => $total,
            'limit'   => $limit,
            'offset'  => $offset,
            'page'    => (int) floor($offset / $limit) + 1,
            'pages'   => (int) ceil($total / $limit),
        ]]);
    }
}
