<?php
/**
 * Prospect Digital — Store Infrastructure & E-Commerce Module (MySQL Driven)
 * ---------------------------------------------------------------------------
 * Provides a scalable, production-ready e-commerce architecture backed by MySQL:
 *   • Unified Product Data Schema (ID, SKU, Slug, Specs, Images, Pricing, Stock)
 *   • MySQL Catalog, Categories & Filter Queries
 *   • Server-side Validated Shopping Cart & Inventory Control
 *   • Wishlist Management
 *   • Transactional Checkout & Order Processing with Foreign Keys
 *   • Admin Order Management & Stock Monitoring
 *   • Currency & Discount Calculation Helpers
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/**
 * Obtain the global PDO database connection instance.
 */
function store_db(): ?PDO
{
    global $pdo;

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        require_once __DIR__ . '/database.php';
    }

    return ($pdo instanceof PDO) ? $pdo : null;
}

/* =========================================================================
   1. STANDARD PRODUCT SCHEMA & CATEGORIES
   ========================================================================= */

/**
 * Retrieve all active store categories from MySQL.
 */
function store_get_categories(): array
{
    $db = store_db();
    if (!$db) {
        return [
            'all' => [
                'id' => 0,
                'slug' => 'all',
                'name' => 'All Products',
                'icon' => 'grid',
                'description' => 'Complete catalog of products & hardware modules',
                'count' => 0,
            ],
        ];
    }
    try {
        $stmt = $db->query("SELECT * FROM `store_categories` WHERE `is_active` = 1 ORDER BY `display_order` ASC, `name` ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categories = [
            'all' => [
                'id'          => 0,
                'name'        => 'All Categories',
                'slug'        => 'all',
                'description' => 'Browse all hardware, software licenses, and developer tools.',
                'icon'        => 'grid',
            ],
        ];

        foreach ($rows as $row) {
            $slug = (string) $row['slug'];
            $categories[$slug] = [
                'id'          => (int) $row['id'],
                'name'        => (string) $row['name'],
                'slug'        => $slug,
                'description' => (string) ($row['description'] ?? ''),
                'icon'        => (string) ($row['icon'] ?? 'grid'),
            ];
        }

        return $categories;
    } catch (PDOException $e) {
        error_log('store_get_categories error: ' . $e->getMessage());
        return [
            'all' => [
                'id'          => 0,
                'name'        => 'All Categories',
                'slug'        => 'all',
                'description' => 'Browse all hardware, software licenses, and developer tools.',
                'icon'        => 'grid',
            ],
        ];
    }
}

/**
 * Validates and normalizes a product array against the master product schema.
 */
function store_normalize_product(array $p): array
{
    return [
        'id'                => (string) ($p['id'] ?? 'prod_' . substr(md5(uniqid('', true)), 0, 8)),
        'sku'               => (string) ($p['sku'] ?? 'PD-SKU-000'),
        'name'              => (string) ($p['name'] ?? 'Untitled Product'),
        'slug'              => (string) ($p['slug'] ?? 'untitled-product'),
        'category'          => (string) ($p['category_name'] ?? ($p['category'] ?? 'General')),
        'category_slug'     => (string) ($p['category_slug'] ?? 'general'),
        'category_id'       => isset($p['category_id']) ? (int) $p['category_id'] : null,
        'short_description' => (string) ($p['short_description'] ?? ''),
        'full_description'  => (string) ($p['full_description'] ?? ''),
        'images'            => is_array($p['images'] ?? null) ? $p['images'] : [],
        'price'             => (float) ($p['price'] ?? 0.0),
        'discount_price'    => isset($p['discount_price']) && is_numeric($p['discount_price']) && (float) $p['discount_price'] > 0 ? (float) $p['discount_price'] : null,
        'stock_quantity'    => (int) ($p['stock_quantity'] ?? 0),
        'stock_status'      => in_array($p['stock_status'] ?? '', ['in_stock', 'out_of_stock', 'preorder', 'discontinued'], true)
                                ? $p['stock_status']
                                : (($p['stock_quantity'] ?? 0) > 0 ? 'in_stock' : 'out_of_stock'),
        'is_published'      => (int) ($p['is_published'] ?? 1),
        'is_featured'       => (int) ($p['is_featured'] ?? 0),
        'specifications'    => is_array($p['specifications'] ?? null) ? $p['specifications'] : [],
        'features'          => is_array($p['features'] ?? null) ? $p['features'] : [],
        'tags'              => is_array($p['tags'] ?? null) ? $p['tags'] : [],
        'seo_title'         => (string) ($p['seo_title'] ?? ($p['name'] ?? 'Store Product') . ' | ' . COMPANY_NAME),
        'seo_description'   => (string) ($p['seo_description'] ?? ($p['short_description'] ?? '')),
        'canonical_url'     => (string) ($p['canonical_url'] ?? absolute_url('store/' . ($p['slug'] ?? ''))),
        'created_at'        => (string) ($p['created_at'] ?? date('c')),
        'updated_at'        => (string) ($p['updated_at'] ?? date('c')),
    ];
}

/**
 * Generate a clean URL-friendly slug.
 */
function store_slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return $text !== '' ? $text : 'product-' . time();
}

/* =========================================================================
   2. PRODUCT CATALOG ACCESS & SEARCH (MySQL)
   ========================================================================= */

/**
 * Retrieve products from MySQL.
 * $public_only = true filters to only published products (`is_published = 1`).
 */
function store_get_products(array $filters = [], bool $public_only = true): array
{
    $db = store_db();
    if (!$db) {
        return [];
    }

    $where = [];
    $params = [];

    if ($public_only) {
        $where[] = "p.`is_published` = 1";
    }

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "p.`stock_status` = :stock_status";
        $params[':stock_status'] = $filters['status'];
    }

    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        $where[] = "(c.`slug` = :cat_slug OR p.`category_name` = :cat_name)";
        $params[':cat_slug'] = $filters['category'];
        $params[':cat_name'] = $filters['category'];
    }

    if (!empty($filters['q'])) {
        $where[] = "(p.`name` LIKE :search_q OR p.`sku` LIKE :search_sku OR p.`short_description` LIKE :search_desc)";
        $search_wild = '%' . $filters['q'] . '%';
        $params[':search_q'] = $search_wild;
        $params[':search_sku'] = $search_wild;
        $params[':search_desc'] = $search_wild;
    }

    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Order By
    $sort = $filters['sort'] ?? 'featured';
    $order_sql = match ($sort) {
        'price-asc'  => 'ORDER BY (CASE WHEN p.`discount_price` IS NOT NULL AND p.`discount_price` > 0 THEN p.`discount_price` ELSE p.`price` END) ASC',
        'price-desc' => 'ORDER BY (CASE WHEN p.`discount_price` IS NOT NULL AND p.`discount_price` > 0 THEN p.`discount_price` ELSE p.`price` END) DESC',
        'name-asc'   => 'ORDER BY p.`name` ASC',
        'name-desc'  => 'ORDER BY p.`name` DESC',
        'newest'     => 'ORDER BY p.`created_at` DESC',
        default      => 'ORDER BY p.`is_featured` DESC, p.`created_at` DESC',
    };

    $sql = "
        SELECT p.*, c.`slug` AS `category_slug`, c.`name` AS `cat_name_rel`
        FROM `store_products` p
        LEFT JOIN `store_categories` c ON p.`category_id` = c.`id`
        {$where_sql}
        {$order_sql}
    ";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        $product_ids = array_column($rows, 'id');
        $images_by_product = store_get_images_for_products($product_ids);

        $products = [];
        foreach ($rows as $row) {
            $p_id = (string) $row['id'];
            $row['images'] = $images_by_product[$p_id] ?? [];
            if (!empty($row['cat_name_rel']) && empty($row['category_name'])) {
                $row['category_name'] = $row['cat_name_rel'];
            }
            $products[] = store_normalize_product($row);
        }

        return $products;
    } catch (PDOException $e) {
        error_log('store_get_products error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Batch-load images for an array of product IDs.
 */
function store_get_images_for_products(array $product_ids): array
{
    if (empty($product_ids)) {
        return [];
    }

    $db = store_db();
    if (!$db) {
        return [];
    }
    $in_placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    $sql = "
        SELECT `product_id`, `image_path`
        FROM `store_product_images`
        WHERE `product_id` IN ({$in_placeholders})
        ORDER BY `is_primary` DESC, `display_order` ASC, `id` ASC
    ";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array_values($product_ids));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $pid = (string) $row['product_id'];
            if (!isset($result[$pid])) {
                $result[$pid] = [];
            }
            $result[$pid][] = (string) $row['image_path'];
        }

        return $result;
    } catch (PDOException $e) {
        error_log('store_get_images_for_products error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Find a single product by its unique URL slug.
 */
function store_get_product_by_slug(string $slug, bool $public_only = true): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }

    $db = store_db();
    if (!$db) {
        return null;
    }
    $where_public = $public_only ? 'AND p.`is_published` = 1' : '';

    $sql = "
        SELECT p.*, c.`slug` AS `category_slug`, c.`name` AS `cat_name_rel`
        FROM `store_products` p
        LEFT JOIN `store_categories` c ON p.`category_id` = c.`id`
        WHERE p.`slug` = :slug {$where_public}
        LIMIT 1
    ";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $p_id = (string) $row['id'];
        $row['images'] = store_get_product_images($p_id);
        $row['specifications'] = store_get_product_specs($p_id);
        $row['features'] = store_get_product_features($p_id);

        return store_normalize_product($row);
    } catch (PDOException $e) {
        error_log('store_get_product_by_slug error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Find a single product by its unique ID.
 */
function store_get_product_by_id(string $id, bool $public_only = false): ?array
{
    $id = trim($id);
    if ($id === '') {
        return null;
    }

    $db = store_db();
    $where_public = $public_only ? 'AND p.`is_published` = 1' : '';

    $sql = "
        SELECT p.*, c.`slug` AS `category_slug`, c.`name` AS `cat_name_rel`
        FROM `store_products` p
        LEFT JOIN `store_categories` c ON p.`category_id` = c.`id`
        WHERE p.`id` = :id {$where_public}
        LIMIT 1
    ";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $p_id = (string) $row['id'];
        $row['images'] = store_get_product_images($p_id);
        $row['specifications'] = store_get_product_specs($p_id);
        $row['features'] = store_get_product_features($p_id);

        return store_normalize_product($row);
    } catch (PDOException $e) {
        error_log('store_get_product_by_id error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Retrieve image paths for a single product.
 */
function store_get_product_images(string $product_id): array
{
    $db = store_db();
    try {
        $stmt = $db->prepare("
            SELECT `image_path`
            FROM `store_product_images`
            WHERE `product_id` = :product_id
            ORDER BY `is_primary` DESC, `display_order` ASC, `id` ASC
        ");
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log('store_get_product_images error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Retrieve key-value specs for a single product.
 */
function store_get_product_specs(string $product_id): array
{
    $db = store_db();
    try {
        $stmt = $db->prepare("
            SELECT `spec_key`, `spec_value`
            FROM `store_product_specs`
            WHERE `product_id` = :product_id
            ORDER BY `display_order` ASC, `id` ASC
        ");
        $stmt->execute([':product_id' => $product_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $specs = [];
        foreach ($rows as $r) {
            $specs[$r['spec_key']] = $r['spec_value'];
        }
        return $specs;
    } catch (PDOException $e) {
        error_log('store_get_product_specs error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Retrieve bullet point features for a single product.
 */
function store_get_product_features(string $product_id): array
{
    $db = store_db();
    try {
        $stmt = $db->prepare("
            SELECT `feature_text`
            FROM `store_product_features`
            WHERE `product_id` = :product_id
            ORDER BY `display_order` ASC, `id` ASC
        ");
        $stmt->execute([':product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log('store_get_product_features error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Filter and sort an array of products by search query, category, and sorting choice.
 */
function store_filter_and_sort(array $products, string $search = '', string $category = '', string $sort = 'featured'): array
{
    return store_get_products([
        'q'        => $search,
        'category' => $category,
        'sort'     => $sort,
    ], true);
}

/* =========================================================================
   3. ADMIN PRODUCT CRUD (MySQL Transactions)
   ========================================================================= */

/**
 * Add a new product to the MySQL catalog.
 */
function store_create_product(array $data): array
{
    $db = store_db();

    $name = trim((string) ($data['name'] ?? ''));
    if ($name === '') {
        throw new InvalidArgumentException('Product name is required.');
    }

    $price = (float) ($data['price'] ?? 0.0);
    if ($price <= 0.0) {
        throw new InvalidArgumentException('Product price must be greater than zero.');
    }

    // Slug calculation & duplicate check
    $slug = trim((string) ($data['slug'] ?? ''));
    $slug = $slug !== '' ? store_slugify($slug) : store_slugify($name);

    $checkSlug = $db->prepare("SELECT COUNT(*) FROM `store_products` WHERE `slug` = :slug");
    $checkSlug->execute([':slug' => $slug]);
    if ($checkSlug->fetchColumn() > 0) {
        $slug .= '-' . rand(100, 999);
    }

    $id = 'prod_' . substr(bin2hex(random_bytes(6)), 0, 10);
    $sku = trim((string) ($data['sku'] ?? ''));
    if ($sku === '') {
        $sku = 'PD-' . strtoupper(substr($slug, 0, 4)) . '-' . rand(100, 999);
    } else {
        $checkSku = $db->prepare("SELECT COUNT(*) FROM `store_products` WHERE `sku` = :sku");
        $checkSku->execute([':sku' => $sku]);
        if ($checkSku->fetchColumn() > 0) {
            $sku .= '-' . rand(10, 99);
        }
    }

    // Category ID lookup
    $category_name = trim((string) ($data['category'] ?? 'Hardware & IoT Kits'));
    $category_id = null;
    $findCat = $db->prepare("SELECT `id`, `name` FROM `store_categories` WHERE `name` = :cname OR `slug` = :cslug LIMIT 1");
    $findCat->execute([':cname' => $category_name, ':cslug' => store_slugify($category_name)]);
    $catRow = $findCat->fetch(PDO::FETCH_ASSOC);
    if ($catRow) {
        $category_id = (int) $catRow['id'];
        $category_name = (string) $catRow['name'];
    }

    $discount_price = isset($data['discount_price']) && is_numeric($data['discount_price']) && (float) $data['discount_price'] > 0
        ? (float) $data['discount_price']
        : null;

    $stock_quantity = max(0, (int) ($data['stock_quantity'] ?? 0));
    $stock_status = (string) ($data['stock_status'] ?? ($stock_quantity > 0 ? 'in_stock' : 'out_of_stock'));
    $is_published = isset($data['is_published']) ? (int) $data['is_published'] : 1;
    $is_featured = isset($data['is_featured']) ? (int) $data['is_featured'] : 0;
    $short_desc = trim((string) ($data['short_description'] ?? ''));
    $full_desc = trim((string) ($data['full_description'] ?? ''));
    $seo_title = trim((string) ($data['seo_title'] ?? ($name . ' | Prospect Digital Store')));
    $seo_description = trim((string) ($data['seo_description'] ?? $short_desc));

    $db->beginTransaction();

    try {
        $insert = $db->prepare("
            INSERT INTO `store_products` (
                `id`, `sku`, `name`, `slug`, `category_id`, `category_name`,
                `short_description`, `full_description`, `price`, `discount_price`,
                `stock_quantity`, `stock_status`, `is_published`, `is_featured`,
                `seo_title`, `seo_description`, `created_at`, `updated_at`
            ) VALUES (
                :id, :sku, :name, :slug, :cat_id, :cat_name,
                :short_desc, :full_desc, :price, :discount_price,
                :stock_qty, :stock_status, :is_published, :is_featured,
                :seo_title, :seo_desc, NOW(), NOW()
            )
        ");

        $insert->execute([
            ':id'             => $id,
            ':sku'            => $sku,
            ':name'           => $name,
            ':slug'           => $slug,
            ':cat_id'         => $category_id,
            ':cat_name'       => $category_name,
            ':short_desc'     => $short_desc,
            ':full_desc'      => $full_desc,
            ':price'          => number_format($price, 2, '.', ''),
            ':discount_price' => $discount_price !== null ? number_format($discount_price, 2, '.', '') : null,
            ':stock_qty'      => $stock_quantity,
            ':stock_status'   => $stock_status,
            ':is_published'   => $is_published,
            ':is_featured'    => $is_featured,
            ':seo_title'      => $seo_title,
            ':seo_desc'       => $seo_description,
        ]);

        // Insert Gallery Images
        if (!empty($data['images']) && is_array($data['images'])) {
            $imgStmt = $db->prepare("
                INSERT INTO `store_product_images` (`product_id`, `image_path`, `is_primary`, `display_order`)
                VALUES (:product_id, :image_path, :is_primary, :display_order)
            ");
            foreach (array_values($data['images']) as $order => $path) {
                $imgStmt->execute([
                    ':product_id'    => $id,
                    ':image_path'    => (string) $path,
                    ':is_primary'    => $order === 0 ? 1 : 0,
                    ':display_order' => $order,
                ]);
            }
        }

        // Insert Specifications
        if (!empty($data['specifications']) && is_array($data['specifications'])) {
            $specStmt = $db->prepare("
                INSERT INTO `store_product_specs` (`product_id`, `spec_key`, `spec_value`, `display_order`)
                VALUES (:product_id, :spec_key, :spec_value, :display_order)
            ");
            $order = 0;
            foreach ($data['specifications'] as $k => $v) {
                if (trim((string) $k) !== '' && trim((string) $v) !== '') {
                    $specStmt->execute([
                        ':product_id'    => $id,
                        ':spec_key'      => trim((string) $k),
                        ':spec_value'    => trim((string) $v),
                        ':display_order' => $order++,
                    ]);
                }
            }
        }

        // Insert Features
        if (!empty($data['features']) && is_array($data['features'])) {
            $featStmt = $db->prepare("
                INSERT INTO `store_product_features` (`product_id`, `feature_text`, `display_order`)
                VALUES (:product_id, :feature_text, :display_order)
            ");
            $order = 0;
            foreach ($data['features'] as $f) {
                if (trim((string) $f) !== '') {
                    $featStmt->execute([
                        ':product_id'    => $id,
                        ':feature_text'  => trim((string) $f),
                        ':display_order' => $order++,
                    ]);
                }
            }
        }

        $db->commit();

        return store_get_product_by_id($id, false) ?? [];
    } catch (Exception $e) {
        $db->rollBack();
        error_log('store_create_product failed: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Update an existing product in MySQL.
 */
function store_update_product(string $id, array $data): bool
{
    $id = trim($id);
    if ($id === '') {
        return false;
    }

    $db = store_db();
    $existing = store_get_product_by_id($id, false);
    if (!$existing) {
        return false;
    }

    $name = trim((string) ($data['name'] ?? $existing['name']));
    $price = isset($data['price']) ? (float) $data['price'] : $existing['price'];
    $discount_price = isset($data['discount_price']) && is_numeric($data['discount_price']) && (float) $data['discount_price'] > 0
        ? (float) $data['discount_price']
        : null;

    $slug = trim((string) ($data['slug'] ?? $existing['slug']));
    $slug = $slug !== '' ? store_slugify($slug) : $existing['slug'];

    // Avoid duplicate slug
    $checkSlug = $db->prepare("SELECT COUNT(*) FROM `store_products` WHERE `slug` = :slug AND `id` != :id");
    $checkSlug->execute([':slug' => $slug, ':id' => $id]);
    if ($checkSlug->fetchColumn() > 0) {
        $slug .= '-' . rand(10, 99);
    }

    $sku = trim((string) ($data['sku'] ?? $existing['sku']));
    $category_name = trim((string) ($data['category'] ?? $existing['category']));
    $category_id = $existing['category_id'] ?? null;

    $findCat = $db->prepare("SELECT `id`, `name` FROM `store_categories` WHERE `name` = :cname OR `slug` = :cslug LIMIT 1");
    $findCat->execute([':cname' => $category_name, ':cslug' => store_slugify($category_name)]);
    $catRow = $findCat->fetch(PDO::FETCH_ASSOC);
    if ($catRow) {
        $category_id = (int) $catRow['id'];
        $category_name = (string) $catRow['name'];
    }

    $stock_quantity = isset($data['stock_quantity']) ? max(0, (int) $data['stock_quantity']) : $existing['stock_quantity'];
    $stock_status = (string) ($data['stock_status'] ?? $existing['stock_status']);
    $is_published = isset($data['is_published']) ? (int) $data['is_published'] : (int) $existing['is_published'];
    $is_featured = isset($data['is_featured']) ? (int) $data['is_featured'] : (int) $existing['is_featured'];
    $short_desc = (string) ($data['short_description'] ?? $existing['short_description']);
    $full_desc = (string) ($data['full_description'] ?? $existing['full_description']);
    $seo_title = (string) ($data['seo_title'] ?? $existing['seo_title']);
    $seo_description = (string) ($data['seo_description'] ?? $existing['seo_description']);

    $db->beginTransaction();

    try {
        $stmt = $db->prepare("
            UPDATE `store_products` SET
                `name`              = :name,
                `slug`              = :slug,
                `sku`               = :sku,
                `category_id`       = :cat_id,
                `category_name`     = :cat_name,
                `price`             = :price,
                `discount_price`    = :discount_price,
                `stock_quantity`    = :stock_qty,
                `stock_status`      = :stock_status,
                `is_published`      = :is_published,
                `is_featured`       = :is_featured,
                `short_description` = :short_desc,
                `full_description`  = :full_desc,
                `seo_title`         = :seo_title,
                `seo_description`   = :seo_desc,
                `updated_at`        = NOW()
            WHERE `id` = :id
        ");

        $stmt->execute([
            ':id'             => $id,
            ':name'           => $name,
            ':slug'           => $slug,
            ':sku'            => $sku,
            ':cat_id'         => $category_id,
            ':cat_name'       => $category_name,
            ':price'          => number_format($price, 2, '.', ''),
            ':discount_price' => $discount_price !== null ? number_format($discount_price, 2, '.', '') : null,
            ':stock_qty'      => $stock_quantity,
            ':stock_status'   => $stock_status,
            ':is_published'   => $is_published,
            ':is_featured'    => $is_featured,
            ':short_desc'     => $short_desc,
            ':full_desc'      => $full_desc,
            ':seo_title'      => $seo_title,
            ':seo_desc'       => $seo_description,
        ]);

        // Sync Images if provided
        if (isset($data['images']) && is_array($data['images'])) {
            $db->prepare("DELETE FROM `store_product_images` WHERE `product_id` = :id")->execute([':id' => $id]);
            $imgStmt = $db->prepare("
                INSERT INTO `store_product_images` (`product_id`, `image_path`, `is_primary`, `display_order`)
                VALUES (:product_id, :image_path, :is_primary, :display_order)
            ");
            foreach (array_values($data['images']) as $order => $path) {
                $imgStmt->execute([
                    ':product_id'    => $id,
                    ':image_path'    => (string) $path,
                    ':is_primary'    => $order === 0 ? 1 : 0,
                    ':display_order' => $order,
                ]);
            }
        }

        // Sync Specifications if provided
        if (isset($data['specifications']) && is_array($data['specifications'])) {
            $db->prepare("DELETE FROM `store_product_specs` WHERE `product_id` = :id")->execute([':id' => $id]);
            $specStmt = $db->prepare("
                INSERT INTO `store_product_specs` (`product_id`, `spec_key`, `spec_value`, `display_order`)
                VALUES (:product_id, :spec_key, :spec_value, :display_order)
            ");
            $order = 0;
            foreach ($data['specifications'] as $k => $v) {
                if (trim((string) $k) !== '' && trim((string) $v) !== '') {
                    $specStmt->execute([
                        ':product_id'    => $id,
                        ':spec_key'      => trim((string) $k),
                        ':spec_value'    => trim((string) $v),
                        ':display_order' => $order++,
                    ]);
                }
            }
        }

        // Sync Features if provided
        if (isset($data['features']) && is_array($data['features'])) {
            $db->prepare("DELETE FROM `store_product_features` WHERE `product_id` = :id")->execute([':id' => $id]);
            $featStmt = $db->prepare("
                INSERT INTO `store_product_features` (`product_id`, `feature_text`, `display_order`)
                VALUES (:product_id, :feature_text, :display_order)
            ");
            $order = 0;
            foreach ($data['features'] as $f) {
                if (trim((string) $f) !== '') {
                    $featStmt->execute([
                        ':product_id'    => $id,
                        ':feature_text'  => trim((string) $f),
                        ':display_order' => $order++,
                    ]);
                }
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log('store_update_product error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete a product by ID from MySQL.
 */
function store_delete_product(string $id): bool
{
    $id = trim($id);
    if ($id === '') {
        return false;
    }

    $db = store_db();
    try {
        $stmt = $db->prepare("DELETE FROM `store_products` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log('store_delete_product error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Toggle the publish state of a product.
 */
function store_toggle_publish(string $id): bool
{
    $db = store_db();
    try {
        $stmt = $db->prepare("UPDATE `store_products` SET `is_published` = IF(`is_published` = 1, 0, 1), `updated_at` = NOW() WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log('store_toggle_publish error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Upload a product image to assets/images/store/.
 */
function store_upload_product_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $max_size = 5 * 1024 * 1024; // 5 MB
    if (($file['size'] ?? 0) > $max_size) {
        return null;
    }

    $allowed_mimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $tmp = (string) ($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp)) {
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);

    if (!isset($allowed_mimes[$mime])) {
        return null;
    }

    $ext = $allowed_mimes[$mime];
    $store_img_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'store';
    if (!is_dir($store_img_dir)) {
        @mkdir($store_img_dir, 0775, true);
    }

    $orig_name = pathinfo((string) ($file['name'] ?? 'product'), PATHINFO_FILENAME);
    $safe_base = store_slugify($orig_name);
    $filename = $safe_base . '-' . time() . '-' . rand(100, 999) . '.' . $ext;
    $destination = $store_img_dir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($tmp, $destination)) {
        return null;
    }

    return url('assets/images/store/' . $filename);
}

/* =========================================================================
   4. SHOPPING CART MANAGEMENT (Server-Side Price & Stock Validated)
   ========================================================================= */

/**
 * Get total quantity of items in the cart.
 */
function store_cart_count(): int
{
    $cart = $_SESSION['pd_cart'] ?? [];
    if (!is_array($cart)) {
        return 0;
    }

    $count = 0;
    foreach ($cart as $item) {
        $count += (int) ($item['quantity'] ?? 0);
    }

    return $count;
}

/**
 * Get complete cart calculations with current prices and stock checked against MySQL.
 */
function store_cart_get(): array
{
    $raw_cart = $_SESSION['pd_cart'] ?? [];
    if (!is_array($raw_cart)) {
        $raw_cart = [];
    }

    $items       = [];
    $subtotal    = 0.00;
    $total_items = 0;

    foreach ($raw_cart as $product_id => $cart_row) {
        $qty = max(1, (int) ($cart_row['quantity'] ?? 1));
        $prod = store_get_product_by_id((string) $product_id, true);

        // If product no longer exists or is unpublished, remove from cart
        if (!$prod) {
            unset($_SESSION['pd_cart'][$product_id]);
            continue;
        }

        // Cap quantity to available stock if in_stock
        $stock = (int) ($prod['stock_quantity'] ?? 0);
        if ($prod['stock_status'] === 'out_of_stock') {
            // Out of stock
            $qty = 0;
        } elseif ($stock > 0 && $qty > $stock) {
            $qty = $stock;
            $_SESSION['pd_cart'][$product_id]['quantity'] = $qty;
        }

        if ($qty <= 0) {
            continue;
        }

        // Exact server-side price validation
        $unit_price = $prod['discount_price'] !== null && (float) $prod['discount_price'] > 0
            ? (float) $prod['discount_price']
            : (float) $prod['price'];

        $line_total = round($unit_price * $qty, 2);
        $subtotal  += $line_total;
        $total_items += $qty;

        $items[] = [
            'product'    => $prod,
            'quantity'   => $qty,
            'unit_price' => $unit_price,
            'line_total' => $line_total,
        ];
    }

    // Taxes & Shipping (18% GST for IT hardware/software in India)
    $tax_rate    = 0.18;
    $tax_amount  = round($subtotal * $tax_rate, 2);
    $shipping    = ($subtotal > 0 && $subtotal < 2500.0) ? 150.00 : 0.00; // Free shipping over ₹2,500
    $discount    = (float) ($_SESSION['pd_cart_discount'] ?? 0.00);
    $grand_total = max(0.00, round(($subtotal + $tax_amount + $shipping) - $discount, 2));

    return [
        'items'        => $items,
        'item_count'   => $total_items,
        'subtotal'     => $subtotal,
        'tax_rate'     => $tax_rate,
        'tax_amount'   => $tax_amount,
        'shipping'     => $shipping,
        'discount'     => $discount,
        'promo_code'   => (string) ($_SESSION['pd_cart_promo'] ?? ''),
        'grand_total'  => $grand_total,
        'is_empty'     => empty($items),
    ];
}

/**
 * Add product to cart.
 */
function store_cart_add(string $product_id, int $quantity = 1): bool
{
    $quantity = max(1, $quantity);
    $prod = store_get_product_by_id($product_id, true);
    if (!$prod || $prod['stock_status'] === 'out_of_stock') {
        return false;
    }

    if (!isset($_SESSION['pd_cart']) || !is_array($_SESSION['pd_cart'])) {
        $_SESSION['pd_cart'] = [];
    }

    $existing_qty = (int) ($_SESSION['pd_cart'][$product_id]['quantity'] ?? 0);
    $new_qty = $existing_qty + $quantity;

    // Check stock limit
    $max_stock = (int) ($prod['stock_quantity'] ?? 0);
    if ($max_stock > 0 && $new_qty > $max_stock) {
        $new_qty = $max_stock;
    }

    $_SESSION['pd_cart'][$product_id] = [
        'id'       => $product_id,
        'quantity' => $new_qty,
        'added_at' => time(),
    ];

    return true;
}

/**
 * Update quantity of an item in the cart.
 */
function store_cart_update(string $product_id, int $quantity): bool
{
    if (!isset($_SESSION['pd_cart'][$product_id])) {
        return false;
    }

    if ($quantity <= 0) {
        unset($_SESSION['pd_cart'][$product_id]);
        return true;
    }

    $prod = store_get_product_by_id($product_id, true);
    if (!$prod) {
        unset($_SESSION['pd_cart'][$product_id]);
        return false;
    }

    $max_stock = (int) ($prod['stock_quantity'] ?? 0);
    if ($max_stock > 0 && $quantity > $max_stock) {
        $quantity = $max_stock;
    }

    $_SESSION['pd_cart'][$product_id]['quantity'] = $quantity;
    return true;
}

/**
 * Remove an item from the cart.
 */
function store_cart_remove(string $product_id): bool
{
    if (isset($_SESSION['pd_cart'][$product_id])) {
        unset($_SESSION['pd_cart'][$product_id]);
        return true;
    }

    return false;
}

/**
 * Empty the shopping cart.
 */
function store_cart_clear(): void
{
    $_SESSION['pd_cart'] = [];
    unset($_SESSION['pd_cart_discount'], $_SESSION['pd_cart_promo']);
}

/* =========================================================================
   5. WISHLIST MANAGEMENT
   ========================================================================= */

function store_wishlist_get(): array
{
    $wishlist = $_SESSION['pd_wishlist'] ?? [];
    return is_array($wishlist) ? array_values(array_unique($wishlist)) : [];
}

function store_wishlist_has(string $product_id): bool
{
    return in_array($product_id, store_wishlist_get(), true);
}

function store_wishlist_toggle(string $product_id): bool
{
    if (!isset($_SESSION['pd_wishlist']) || !is_array($_SESSION['pd_wishlist'])) {
        $_SESSION['pd_wishlist'] = [];
    }

    $idx = array_search($product_id, $_SESSION['pd_wishlist'], true);
    if ($idx !== false) {
        unset($_SESSION['pd_wishlist'][$idx]);
        $_SESSION['pd_wishlist'] = array_values($_SESSION['pd_wishlist']);
        return false;
    }

    $_SESSION['pd_wishlist'][] = $product_id;
    return true;
}

/* =========================================================================
   6. CHECKOUT & ORDER PROCESSING (Transactional MySQL)
   ========================================================================= */

/**
 * Process checkout form, lock inventory, decrement stock, and insert order in MySQL.
 */
function store_process_checkout(array $post): array
{
    $cart = store_cart_get();
    if ($cart['is_empty']) {
        return [
            'ok'     => false,
            'errors' => ['Your shopping cart is currently empty. Please add items to proceed.'],
        ];
    }

    // Sanitize and validate inputs
    $customer_name  = clean_text($post['customer_name'] ?? '', 120);
    $customer_email = strtolower(clean_text($post['customer_email'] ?? '', 190));
    $customer_phone = clean_text($post['customer_phone'] ?? '', 40);

    $address_line1  = clean_text($post['address_line1'] ?? '', 255);
    $address_line2  = clean_text($post['address_line2'] ?? '', 255);
    $city           = clean_text($post['city'] ?? '', 100);
    $state          = clean_text($post['state'] ?? '', 100);
    $pincode        = clean_text($post['pincode'] ?? '', 20);
    $country        = clean_text($post['country'] ?? 'India', 80);

    $payment_method = clean_text($post['payment_method'] ?? 'upi', 50);
    $notes          = clean_text($post['order_notes'] ?? '', 1000);

    $errors = [];

    if ($customer_name === '') {
        $errors['customer_name'] = 'Please enter your full name.';
    }
    if ($customer_email === '' || !valid_email($customer_email)) {
        $errors['customer_email'] = 'Please enter a valid work or personal e-mail address.';
    }
    if ($customer_phone === '' || !valid_phone($customer_phone)) {
        $errors['customer_phone'] = 'Please enter a valid phone number (7–16 digits).';
    }
    if ($address_line1 === '') {
        $errors['address_line1'] = 'Please enter your delivery street address.';
    }
    if ($city === '') {
        $errors['city'] = 'Please enter your city.';
    }
    if ($state === '') {
        $errors['state'] = 'Please select or enter your state.';
    }
    if ($pincode === '') {
        $errors['pincode'] = 'Please enter a valid postal/PIN code.';
    }

    if (!empty($errors)) {
        return [
            'ok'           => false,
            'errors'       => array_values($errors),
            'field_errors' => $errors,
        ];
    }

    $db = store_db();
    $db->beginTransaction();

    try {
        // 1. Verify and Lock Product Inventory
        $lockStmt = $db->prepare("
            SELECT `id`, `name`, `sku`, `price`, `discount_price`, `stock_quantity`, `stock_status`
            FROM `store_products`
            WHERE `id` = :id
            FOR UPDATE
        ");

        $stock_errors = [];
        $verified_items = [];
        $calculated_subtotal = 0.00;

        foreach ($cart['items'] as $item) {
            $pid = (string) $item['product']['id'];
            $req_qty = (int) $item['quantity'];

            $lockStmt->execute([':id' => $pid]);
            $current_prod = $lockStmt->fetch(PDO::FETCH_ASSOC);

            if (!$current_prod) {
                $stock_errors[] = "Product \"{$item['product']['name']}\" is no longer available.";
                continue;
            }

            $avail_stock = (int) $current_prod['stock_quantity'];
            if ($current_prod['stock_status'] === 'out_of_stock' || $avail_stock < $req_qty) {
                $stock_errors[] = "Insufficient stock for \"{$current_prod['name']}\". Available: {$avail_stock}, requested: {$req_qty}.";
                continue;
            }

            // Locked purchase unit price
            $unit_price = ($current_prod['discount_price'] !== null && (float) $current_prod['discount_price'] > 0)
                ? (float) $current_prod['discount_price']
                : (float) $current_prod['price'];

            $line_total = round($unit_price * $req_qty, 2);
            $calculated_subtotal += $line_total;

            $verified_items[] = [
                'product_id'   => $pid,
                'product_name' => (string) $current_prod['name'],
                'product_sku'  => (string) $current_prod['sku'],
                'unit_price'   => $unit_price,
                'quantity'     => $req_qty,
                'total_price'  => $line_total,
                'new_stock'    => max(0, $avail_stock - $req_qty),
            ];
        }

        if (!empty($stock_errors)) {
            $db->rollBack();
            return [
                'ok'     => false,
                'errors' => $stock_errors,
            ];
        }

        // 2. Financial calculation
        $tax_rate     = 0.18;
        $tax_amount   = round($calculated_subtotal * $tax_rate, 2);
        $shipping_fee = ($calculated_subtotal > 0 && $calculated_subtotal < 2500.0) ? 150.00 : 0.00;
        $discount_amt = round((float) ($cart['discount'] ?? 0.00), 2);
        $grand_total  = max(0.00, round(($calculated_subtotal + $tax_amount + $shipping_fee) - $discount_amt, 2));

        // 3. Upsert Customer Record
        $findCust = $db->prepare("SELECT `id` FROM `store_customers` WHERE `email` = :email LIMIT 1");
        $findCust->execute([':email' => $customer_email]);
        $customer_id = $findCust->fetchColumn();

        if ($customer_id) {
            $db->prepare("UPDATE `store_customers` SET `name` = :name, `phone` = :phone, `updated_at` = NOW() WHERE `id` = :id")
               ->execute([':name' => $customer_name, ':phone' => $customer_phone, ':id' => $customer_id]);
        } else {
            $cur_auth_user = current_user();
            $user_id = $cur_auth_user['id'] ?? ($_SESSION['user']['id'] ?? ($_SESSION['user_id'] ?? null));
            $insCust = $db->prepare("INSERT INTO `store_customers` (`user_id`, `name`, `email`, `phone`, `created_at`, `updated_at`) VALUES (:uid, :name, :email, :phone, NOW(), NOW())");
            $insCust->execute([
                ':uid'   => $user_id,
                ':name'  => $customer_name,
                ':email' => $customer_email,
                ':phone' => $customer_phone,
            ]);
            $customer_id = (int) $db->lastInsertId();
        }

        // 4. Generate Order Reference
        $order_number = 'PD-ORD-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $payment_status = in_array($payment_method, ['cod', 'bank_transfer'], true) ? 'pending' : 'authorized';
        $order_status = 'confirmed';

        // 5. Insert Order
        $orderStmt = $db->prepare("
            INSERT INTO `store_orders` (
                `order_number`, `customer_id`, `customer_name`, `customer_email`, `customer_phone`,
                `shipping_address_line1`, `shipping_address_line2`, `shipping_city`, `shipping_state`, `shipping_pincode`, `shipping_country`,
                `payment_method`, `payment_status`, `order_status`,
                `subtotal`, `tax_amount`, `shipping_fee`, `discount_amount`, `total_amount`, `notes`,
                `created_at`, `updated_at`
            ) VALUES (
                :order_num, :cust_id, :c_name, :c_email, :c_phone,
                :addr1, :addr2, :city, :state, :pincode, :country,
                :pay_method, :pay_status, :order_status,
                :subtotal, :tax, :shipping, :discount, :total, :notes,
                NOW(), NOW()
            )
        ");

        $orderStmt->execute([
            ':order_num'     => $order_number,
            ':cust_id'       => $customer_id,
            ':c_name'        => $customer_name,
            ':c_email'       => $customer_email,
            ':c_phone'       => $customer_phone,
            ':addr1'         => $address_line1,
            ':addr2'         => $address_line2,
            ':city'          => $city,
            ':state'         => $state,
            ':pincode'       => $pincode,
            ':country'       => $country,
            ':pay_method'    => $payment_method,
            ':pay_status'    => $payment_status,
            ':order_status'  => $order_status,
            ':subtotal'      => number_format($calculated_subtotal, 2, '.', ''),
            ':tax'           => number_format($tax_amount, 2, '.', ''),
            ':shipping'      => number_format($shipping_fee, 2, '.', ''),
            ':discount'      => number_format($discount_amt, 2, '.', ''),
            ':total'         => number_format($grand_total, 2, '.', ''),
            ':notes'         => $notes,
        ]);

        $order_db_id = (int) $db->lastInsertId();

        // 6. Insert Order Items & Decrement Inventory
        $itemStmt = $db->prepare("
            INSERT INTO `store_order_items` (
                `order_id`, `product_id`, `product_name`, `product_sku`, `unit_price`, `quantity`, `total_price`, `created_at`
            ) VALUES (
                :order_id, :product_id, :product_name, :product_sku, :unit_price, :quantity, :total_price, NOW()
            )
        ");

        $decrementStmt = $db->prepare("
            UPDATE `store_products`
            SET `stock_quantity` = :new_stock,
                `stock_status` = CASE WHEN :new_stock_check <= 0 THEN 'out_of_stock' ELSE `stock_status` END,
                `updated_at` = NOW()
            WHERE `id` = :id
        ");

        foreach ($verified_items as $vItem) {
            $itemStmt->execute([
                ':order_id'     => $order_db_id,
                ':product_id'   => $vItem['product_id'],
                ':product_name' => $vItem['product_name'],
                ':product_sku'  => $vItem['product_sku'],
                ':unit_price'   => number_format($vItem['unit_price'], 2, '.', ''),
                ':quantity'     => $vItem['quantity'],
                ':total_price'  => number_format($vItem['total_price'], 2, '.', ''),
            ]);

            $decrementStmt->execute([
                ':new_stock'       => $vItem['new_stock'],
                ':new_stock_check' => $vItem['new_stock'],
                ':id'              => $vItem['product_id'],
            ]);
        }

        $db->commit();

        // Retrieve full order record
        $completed_order = store_get_order($order_number);
        $_SESSION['pd_last_order'] = $completed_order;

        // Clear cart
        store_cart_clear();

        return [
            'ok'       => true,
            'order_id' => $order_number,
            'order'    => $completed_order,
        ];
    } catch (Exception $e) {
        $db->rollBack();
        error_log('store_process_checkout transaction failed: ' . $e->getMessage());
        return [
            'ok'     => false,
            'errors' => ['An error occurred while finalizing your order: ' . $e->getMessage()],
        ];
    }
}

/**
 * Retrieve an order by reference code from MySQL.
 */
function store_get_order(string $order_number): ?array
{
    $order_number = trim($order_number);
    if ($order_number === '') {
        return null;
    }

    $db = store_db();
    try {
        $stmt = $db->prepare("SELECT * FROM `store_orders` WHERE `order_number` = :num LIMIT 1");
        $stmt->execute([':num' => $order_number]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order_id = (int) $order['id'];
        $itemStmt = $db->prepare("SELECT * FROM `store_order_items` WHERE `order_id` = :oid ORDER BY `id` ASC");
        $itemStmt->execute([':oid' => $order_id]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize format matching template expectations
        return [
            'id'             => (int) $order['id'],
            'order_id'       => (string) $order['order_number'],
            'order_number'   => (string) $order['order_number'],
            'created_at'     => (string) $order['created_at'],
            'status'         => (string) $order['order_status'],
            'order_status'   => (string) $order['order_status'],
            'payment_status' => (string) $order['payment_status'],
            'payment_method' => (string) $order['payment_method'],
            'customer' => [
                'name'  => (string) $order['customer_name'],
                'email' => (string) $order['customer_email'],
                'phone' => (string) $order['customer_phone'],
            ],
            'shipping_address' => [
                'line1'   => (string) $order['shipping_address_line1'],
                'line2'   => (string) ($order['shipping_address_line2'] ?? ''),
                'city'    => (string) $order['shipping_city'],
                'state'   => (string) $order['shipping_state'],
                'pincode' => (string) $order['shipping_pincode'],
                'country' => (string) $order['shipping_country'],
            ],
            'items' => array_map(static function (array $it): array {
                return [
                    'product_id' => (string) ($it['product_id'] ?? ''),
                    'quantity'   => (int) $it['quantity'],
                    'unit_price' => (float) $it['unit_price'],
                    'line_total' => (float) $it['total_price'],
                    'product'    => [
                        'name' => (string) $it['product_name'],
                        'sku'  => (string) $it['product_sku'],
                    ],
                ];
            }, $items),
            'subtotal'    => (float) $order['subtotal'],
            'tax_amount'  => (float) $order['tax_amount'],
            'shipping'    => (float) $order['shipping_fee'],
            'discount'    => (float) $order['discount_amount'],
            'grand_total' => (float) $order['total_amount'],
            'notes'       => (string) ($order['notes'] ?? ''),
        ];
    } catch (PDOException $e) {
        error_log('store_get_order error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Retrieve all orders for the Admin Panel with filtering and pagination.
 */
function store_get_all_orders(array $filters = [], int $limit = 20, int $offset = 0): array
{
    $db = store_db();
    $where = [];
    $params = [];

    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $where[] = "`order_status` = :status";
        $params[':status'] = $filters['status'];
    }

    if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
        $where[] = "`payment_status` = :payment_status";
        $params[':payment_status'] = $filters['payment_status'];
    }

    if (!empty($filters['q'])) {
        $where[] = "(`order_number` LIKE :q OR `customer_name` LIKE :q OR `customer_email` LIKE :q OR `customer_phone` LIKE :q)";
        $params[':q'] = '%' . $filters['q'] . '%';
    }

    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    try {
        // Total Count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM `store_orders` {$where_sql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Paginated Rows
        $sql = "
            SELECT * FROM `store_orders`
            {$where_sql}
            ORDER BY `created_at` DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total'  => $total,
            'orders' => $rows,
        ];
    } catch (PDOException $e) {
        error_log('store_get_all_orders error: ' . $e->getMessage());
        return ['total' => 0, 'orders' => []];
    }
}

/**
 * Update order status or payment status in MySQL.
 */
function store_update_order_status(int $order_id, string $order_status, ?string $payment_status = null): bool
{
    $db = store_db();
    try {
        if ($payment_status !== null) {
            $stmt = $db->prepare("
                UPDATE `store_orders`
                SET `order_status` = :ostatus, `payment_status` = :pstatus, `updated_at` = NOW()
                WHERE `id` = :id
            ");
            return $stmt->execute([
                ':ostatus' => $order_status,
                ':pstatus' => $payment_status,
                ':id'      => $order_id,
            ]);
        }

        $stmt = $db->prepare("
            UPDATE `store_orders`
            SET `order_status` = :ostatus, `updated_at` = NOW()
            WHERE `id` = :id
        ");
        return $stmt->execute([
            ':ostatus' => $order_status,
            ':id'      => $order_id,
        ]);
    } catch (PDOException $e) {
        error_log('store_update_order_status error: ' . $e->getMessage());
        return false;
    }
}

/* =========================================================================
   7. CURRENCY & FORMATTING HELPERS
   ========================================================================= */

function store_format_currency(float $amount, bool $show_decimals = false): string
{
    if ($show_decimals) {
        return '₹' . number_format($amount, 2, '.', ',');
    }

    return '₹' . number_format($amount, 0, '.', ',');
}

function store_calc_discount_percent(float $regular, float $discount): int
{
    if ($regular <= 0.0 || $discount >= $regular) {
        return 0;
    }

    return (int) round((($regular - $discount) / $regular) * 100);
}
