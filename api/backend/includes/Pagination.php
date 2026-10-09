<?php
class Pagination {
    public static function params(array $query = []): array {
        $limit  = max(1, min((int)($query['limit']  ?? 20), 100));
        $page   = max(1, (int)($query['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        return compact('limit', 'page', 'offset');
    }

    public static function meta(int $total, int $limit, int $page): array {
        return [
            'total'        => $total,
            'per_page'     => $limit,
            'current_page' => $page,
            'last_page'    => (int)ceil($total / max(1, $limit)),
            'has_more'     => ($page * $limit) < $total,
        ];
    }
}
