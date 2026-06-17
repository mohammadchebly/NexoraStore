<?php
function mira_column_exists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :column_name");
    $stmt->execute([':column_name' => $column]);
    return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}

function mira_table_exists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SHOW TABLES LIKE :table_name");
    $stmt->execute([':table_name' => $table]);
    return (bool)$stmt->fetch(PDO::FETCH_NUM);
}

function ensure_store_schema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_name VARCHAR(120) NOT NULL,
        category_slug VARCHAR(140) NOT NULL UNIQUE,
        isDeleted TINYINT(1) NOT NULL DEFAULT 1,
        date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if (mira_table_exists($pdo, 'products') && !mira_column_exists($pdo, 'products', 'category_id')) {
        $pdo->exec("ALTER TABLE products ADD category_id INT NULL AFTER id");
        $pdo->exec("ALTER TABLE products ADD INDEX idx_products_category_id (category_id)");
    }
}

function category_slug(string $name): string {
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : 'category';
}

function unique_category_slug(PDO $pdo, string $name, int $ignoreId = 0): string {
    $base = category_slug($name);
    $slug = $base;
    $counter = 2;

    while (true) {
        $sql = "SELECT id FROM categories WHERE category_slug = :slug" . ($ignoreId > 0 ? " AND id != :id" : "") . " LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $params = [':slug' => $slug];
        if ($ignoreId > 0) {
            $params[':id'] = $ignoreId;
        }
        $stmt->execute($params);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            return $slug;
        }
        $slug = $base . '-' . $counter++;
    }
}
