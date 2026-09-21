<?php
/**
 * Prospect Digital — Admin Store & Product Management
 * ---------------------------------------------------------------------------
 * Full CRUD management for store products, pricing, stock levels, specifications,
 * highlights, and product images.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

// Ensure store functions and core config are available
require_once dirname(__DIR__) . '/includes/store-functions.php';

$page_title = 'Store & Products';
$active_nav = 'store';
$csrf_token = admin_csrf_token();

$categories = store_get_categories();

// ---------------------------------------------------------------------------
// 1. POST ACTION HANDLERS (Create, Update, Delete)
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!admin_verify_csrf($_POST['csrf_token'] ?? '')) {
        admin_set_flash('danger', 'Security session expired. Please refresh the page and try again.');
        header('Location: ' . url('admin/store.php'));
        exit;
    }

    // A. Add New Product
    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0.0);
        $discount_price = trim((string) ($_POST['discount_price'] ?? '')) !== '' ? (float) $_POST['discount_price'] : null;
        $category = trim((string) ($_POST['category'] ?? 'Hardware & IoT Kits'));
        $category_slug = trim((string) ($_POST['category_slug'] ?? ''));
        $sku = trim((string) ($_POST['sku'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $stock_status = trim((string) ($_POST['stock_status'] ?? 'in_stock'));
        $stock_quantity = (int) ($_POST['stock_quantity'] ?? 0);
        $short_desc = trim((string) ($_POST['short_description'] ?? ''));
        $full_desc = trim((string) ($_POST['full_description'] ?? ''));

        // Process specifications key-values
        $spec_keys = $_POST['spec_key'] ?? [];
        $spec_vals = $_POST['spec_val'] ?? [];
        $specifications = [];
        if (is_array($spec_keys) && is_array($spec_vals)) {
            foreach ($spec_keys as $k_idx => $s_key) {
                $k = trim((string) $s_key);
                $v = trim((string) ($spec_vals[$k_idx] ?? ''));
                if ($k !== '' && $v !== '') {
                    $specifications[$k] = $v;
                }
            }
        }

        // Process highlights
        $highlights_raw = trim((string) ($_POST['features_text'] ?? ''));
        $features = [];
        if ($highlights_raw !== '') {
            $lines = explode("\n", $highlights_raw);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $features[] = $trimmed;
                }
            }
        }

        // Handle Image Upload or URL
        $images = [];
        if (isset($_FILES['product_image']) && ($_FILES['product_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploaded_url = store_upload_product_image($_FILES['product_image']);
            if ($uploaded_url) {
                $images[] = $uploaded_url;
            }
        }

        $image_url = trim((string) ($_POST['image_url'] ?? ''));
        if ($image_url !== '') {
            $images[] = $image_url;
        }

        if ($name === '' || $price <= 0) {
            admin_set_flash('danger', 'Product name and a valid price (> 0) are required.');
            header('Location: ' . url('admin/store.php?action=new'));
            exit;
        }

        try {
            $created = store_create_product([
                'name'              => $name,
                'slug'              => $slug,
                'sku'               => $sku,
                'category'          => $category,
                'category_slug'     => $category_slug,
                'price'             => $price,
                'discount_price'    => $discount_price,
                'stock_status'      => $stock_status,
                'stock_quantity'    => $stock_quantity,
                'short_description' => $short_desc,
                'full_description'  => $full_desc,
                'images'            => $images,
                'specifications'    => $specifications,
                'features'          => $features,
            ]);

            admin_set_flash('success', 'Product "' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" has been published to the store catalog.');
            header('Location: ' . url('admin/store.php'));
            exit;
        } catch (Throwable $e) {
            admin_set_flash('danger', 'Error creating product: ' . $e->getMessage());
            header('Location: ' . url('admin/store.php?action=new'));
            exit;
        }
    }

    // B. Update Existing Product
    if ($action === 'update') {
        $id = trim((string) ($_POST['id'] ?? ''));
        $existing = store_get_product_by_id($id);

        if (!$existing) {
            admin_set_flash('danger', 'Product not found.');
            header('Location: ' . url('admin/store.php'));
            exit;
        }

        $name = trim((string) ($_POST['name'] ?? $existing['name']));
        $price = (float) ($_POST['price'] ?? $existing['price']);
        $discount_price = trim((string) ($_POST['discount_price'] ?? '')) !== '' ? (float) $_POST['discount_price'] : null;
        $category = trim((string) ($_POST['category'] ?? $existing['category']));
        $category_slug = trim((string) ($_POST['category_slug'] ?? ''));
        $sku = trim((string) ($_POST['sku'] ?? $existing['sku']));
        $slug = trim((string) ($_POST['slug'] ?? $existing['slug']));
        $stock_status = trim((string) ($_POST['stock_status'] ?? $existing['stock_status']));
        $stock_quantity = (int) ($_POST['stock_quantity'] ?? $existing['stock_quantity']);
        $short_desc = trim((string) ($_POST['short_description'] ?? ''));
        $full_desc = trim((string) ($_POST['full_description'] ?? ''));

        // Specs
        $spec_keys = $_POST['spec_key'] ?? [];
        $spec_vals = $_POST['spec_val'] ?? [];
        $specifications = [];
        if (is_array($spec_keys) && is_array($spec_vals)) {
            foreach ($spec_keys as $k_idx => $s_key) {
                $k = trim((string) $s_key);
                $v = trim((string) ($spec_vals[$k_idx] ?? ''));
                if ($k !== '' && $v !== '') {
                    $specifications[$k] = $v;
                }
            }
        }

        // Features
        $highlights_raw = trim((string) ($_POST['features_text'] ?? ''));
        $features = [];
        if ($highlights_raw !== '') {
            $lines = explode("\n", $highlights_raw);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $features[] = $trimmed;
                }
            }
        }

        // Images: retain existing or append new
        $images = is_array($existing['images'] ?? null) ? $existing['images'] : [];
        if (isset($_FILES['product_image']) && ($_FILES['product_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $uploaded_url = store_upload_product_image($_FILES['product_image']);
            if ($uploaded_url) {
                // If requested replace primary
                if (!empty($_POST['replace_primary_image'])) {
                    array_unshift($images, $uploaded_url);
                } else {
                    $images[] = $uploaded_url;
                }
            }
        }

        $image_url = trim((string) ($_POST['image_url'] ?? ''));
        if ($image_url !== '') {
            $images[] = $image_url;
        }

        $images = array_values(array_unique($images));

        // Handle soft deleted images and garbage collection
        $removed_images = $_POST['remove_images'] ?? [];
        if (!empty($removed_images) && is_array($removed_images)) {
            $images = array_values(array_diff($images, $removed_images));

            $gc_file = ADMIN_DATA_DIR . DIRECTORY_SEPARATOR . 'deleted_images.json';
            $deleted_log = [];
            if (file_exists($gc_file)) {
                $deleted_log = json_decode(file_get_contents($gc_file), true) ?: [];
            }

            foreach ($removed_images as $r_img) {
                $deleted_log[] = $r_img;
            }

            while (count($deleted_log) > 10) {
                $oldest = array_shift($deleted_log);
                if (str_starts_with($oldest, 'assets/images/store/')) {
                    $abs_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldest);
                    if (file_exists($abs_path)) {
                        @unlink($abs_path);
                    }
                }
            }
            @file_put_contents($gc_file, json_encode($deleted_log, JSON_PRETTY_PRINT));
        }

        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $is_featured  = isset($_POST['is_featured']) ? 1 : 0;

        $ok = store_update_product($id, [
            'name'              => $name,
            'slug'              => $slug,
            'sku'               => $sku,
            'category'          => $category,
            'category_slug'     => $category_slug,
            'price'             => $price,
            'discount_price'    => $discount_price,
            'stock_status'      => $stock_status,
            'stock_quantity'    => $stock_quantity,
            'is_published'      => $is_published,
            'is_featured'       => $is_featured,
            'short_description' => $short_desc,
            'full_description'  => $full_desc,
            'images'            => $images,
            'specifications'    => $specifications,
            'features'          => $features,
        ]);

        if ($ok) {
            admin_set_flash('success', 'Product "' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" was updated successfully.');
        } else {
            admin_set_flash('danger', 'Failed to update product details.');
        }

        header('Location: ' . url('admin/store.php'));
        exit;
    }

    // C. Toggle Publish / Draft
    if ($action === 'toggle_publish') {
        $id = trim((string) ($_POST['id'] ?? ''));
        if ($id !== '' && store_toggle_publish($id)) {
            admin_set_flash('success', 'Product visibility toggled successfully.');
        } else {
            admin_set_flash('danger', 'Failed to update visibility state.');
        }
        header('Location: ' . url('admin/store.php'));
        exit;
    }

    // D. Delete Product
    if ($action === 'delete') {
        $id = trim((string) ($_POST['id'] ?? ''));
        $existing = store_get_product_by_id($id);

        if ($existing && store_delete_product($id)) {
            admin_set_flash('success', 'Product "' . htmlspecialchars($existing['name'], ENT_QUOTES, 'UTF-8') . '" has been removed from the catalog.');
        } else {
            admin_set_flash('danger', 'Could not delete product or product was not found.');
        }

        header('Location: ' . url('admin/store.php'));
        exit;
    }
}

// ---------------------------------------------------------------------------
// 2. QUERY CATALOG & STATS (From MySQL)
// ---------------------------------------------------------------------------
$all_products = store_get_products([], false); // false = include unpublished/drafts for admin

// Summary Stats
$total_products = count($all_products);
$in_stock_count = 0;
$out_stock_count = 0;
$published_count = 0;
$categories_map = [];

foreach ($all_products as $p) {
    if (($p['stock_status'] ?? '') === 'in_stock') {
        $in_stock_count++;
    } else {
        $out_stock_count++;
    }
    if (!empty($p['is_published'])) {
        $published_count++;
    }
    $cat = (string) ($p['category'] ?? 'General');
    $categories_map[$cat] = ($categories_map[$cat] ?? 0) + 1;
}

$categories_count = count($categories_map);

// Filter Parameters
$filter_q   = trim((string) ($_GET['q'] ?? ''));
$filter_cat = trim((string) ($_GET['category'] ?? ''));
$filter_st  = trim((string) ($_GET['status'] ?? ''));

$display_products = $all_products;
if ($filter_q !== '' || $filter_cat !== '' || $filter_st !== '') {
    $display_products = array_filter($display_products, static function (array $p) use ($filter_q, $filter_cat, $filter_st): bool {
        if ($filter_cat !== '' && $filter_cat !== 'all') {
            if (($p['category_slug'] ?? '') !== $filter_cat && ($p['category'] ?? '') !== $filter_cat) {
                return false;
            }
        }
        if ($filter_st !== '' && $filter_st !== 'all') {
            if (($p['stock_status'] ?? '') !== $filter_st) {
                return false;
            }
        }
        if ($filter_q !== '') {
            $haystack = mb_strtolower($p['name'] . ' ' . ($p['sku'] ?? '') . ' ' . ($p['short_description'] ?? ''));
            if (!str_contains($haystack, mb_strtolower($filter_q))) {
                return false;
            }
        }
        return true;
    });
}

// Check View Action
$view_mode = $_GET['action'] ?? 'list'; // 'list', 'new', 'edit'
$edit_product = null;
if ($view_mode === 'edit') {
    $edit_id = trim((string) ($_GET['id'] ?? ''));
    $edit_product = store_get_product_by_id($edit_id);
    if (!$edit_product) {
        admin_set_flash('danger', 'Product ID not found.');
        header('Location: ' . url('admin/store.php'));
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<?php if ($view_mode === 'new' || $view_mode === 'edit'): ?>
  <!-- =========================================================================
       FORM VIEW: ADD / EDIT PRODUCT
       ========================================================================= -->
  <div class="page-header">
    <div>
      <h1 class="page-title"><?= $view_mode === 'new' ? 'Add New Product' : 'Edit Product: ' . htmlspecialchars($edit_product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="page-subtitle">Fill in the product specifications, pricing, inventory status, and gallery media.</p>
    </div>
    <div style="display:flex; gap:0.75rem;">
      <a href="<?= url('admin/store.php') ?>" class="btn btn--outline">
        &larr; Return to Catalog
      </a>
    </div>
  </div>

  <form method="POST" action="<?= url('admin/store.php') ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="action" value="<?= $view_mode === 'new' ? 'create' : 'update' ?>">
    <?php if ($view_mode === 'edit'): ?>
      <input type="hidden" name="id" value="<?= htmlspecialchars($edit_product['id'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <div class="card" style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.75rem;">
        1. General Information
      </h2>
      <div class="product-form-grid">
        <div class="form-group full-span">
          <label for="prodName">Product Title <span class="required">*</span></label>
          <input type="text" id="prodName" name="name" class="form-control" required placeholder="e.g. Industrial Edge Gateway Controller" value="<?= htmlspecialchars($edit_product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Displayed prominently on the store catalog and product detail page.</span>
        </div>

        <div class="form-group">
          <label for="prodSlug">URL Slug</label>
          <input type="text" id="prodSlug" name="slug" class="form-control" placeholder="industrial-edge-gateway-controller" value="<?= htmlspecialchars($edit_product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Leave blank to auto-generate from the title.</span>
        </div>

        <div class="form-group">
          <label for="prodSku">SKU / Model Number</label>
          <input type="text" id="prodSku" name="sku" class="form-control" placeholder="PD-HW-001" value="<?= htmlspecialchars($edit_product['sku'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Unique identifier for stock tracking and invoices.</span>
        </div>

        <div class="form-group">
          <label for="prodCategory">Category <span class="required">*</span></label>
          <select id="prodCategory" name="category" class="form-control" required>
            <?php foreach ($categories as $slug => $cat): ?>
              <?php if ($slug === 'all') continue; ?>
              <option value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>" <?= ($edit_product['category'] ?? '') === $cat['name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="prodCatSlug">Category Slug</label>
          <input type="text" id="prodCatSlug" name="category_slug" class="form-control" placeholder="hardware-iot" value="<?= htmlspecialchars($edit_product['category_slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Used for live catalog chip filtering.</span>
        </div>

        <div class="form-group full-span">
          <label for="prodShortDesc">Short Summary (Catalog Cards)</label>
          <textarea id="prodShortDesc" name="short_description" class="form-control" rows="2" placeholder="Brief 1-2 sentence overview shown on store catalog cards."><?= htmlspecialchars($edit_product['short_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-group full-span">
          <label for="prodFullDesc">Detailed Description</label>
          <textarea id="prodFullDesc" name="full_description" class="form-control" rows="5" placeholder="Full technical explanation, use cases, and operating architecture."><?= htmlspecialchars($edit_product['full_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>
      </div>
    </div>

    <!-- Pricing & Stock -->
    <div class="card" style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.75rem;">
        2. Pricing &amp; Inventory Controls
      </h2>
      <div class="product-form-grid">
        <div class="form-group">
          <label for="prodPrice">Regular Price (INR ₹) <span class="required">*</span></label>
          <input type="number" step="0.01" min="0" id="prodPrice" name="price" class="form-control" required placeholder="4999.00" value="<?= htmlspecialchars((string) ($edit_product['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Base selling price.</span>
        </div>

        <div class="form-group">
          <label for="prodDiscountPrice">Discounted Sale Price (INR ₹)</label>
          <input type="number" step="0.01" min="0" id="prodDiscountPrice" name="discount_price" class="form-control" placeholder="3999.00" value="<?= htmlspecialchars((string) ($edit_product['discount_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">If set, customers see a strikethrough and savings badge.</span>
        </div>

        <div class="form-group">
          <label for="prodStockStatus">Stock Availability <span class="required">*</span></label>
          <select id="prodStockStatus" name="stock_status" class="form-control" required>
            <option value="in_stock" <?= ($edit_product['stock_status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>In Stock (Ready to ship)</option>
            <option value="out_of_stock" <?= ($edit_product['stock_status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
            <option value="preorder" <?= ($edit_product['stock_status'] ?? '') === 'preorder' ? 'selected' : '' ?>>Pre-Order</option>
            <option value="discontinued" <?= ($edit_product['stock_status'] ?? '') === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
          </select>
        </div>

        <div class="form-group">
          <label for="prodStockQty">Available Stock Quantity</label>
          <input type="number" min="0" id="prodStockQty" name="stock_quantity" class="form-control" placeholder="25" value="<?= htmlspecialchars((string) ($edit_product['stock_quantity'] ?? '10'), ENT_QUOTES, 'UTF-8') ?>">
          <span class="form-hint">Units currently in warehouse.</span>
        </div>

        <div class="form-group full-span" style="display: flex; gap: 2rem; align-items: center; background: var(--admin-surface-subtle); padding: 1rem 1.25rem; border-radius: var(--admin-radius-sm); border: 1px solid var(--admin-border);">
          <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 13.5px; margin: 0;">
            <input type="checkbox" name="is_published" value="1" <?= (!isset($edit_product) || !empty($edit_product['is_published'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--admin-brand);">
            <span><strong>Published in Store</strong> (Immediately visible to public customers)</span>
          </label>

          <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 13.5px; margin: 0;">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($edit_product['is_featured']) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--admin-brand);">
            <span><strong>Featured Product</strong> (Highlighted at the top of catalog)</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Media & Gallery -->
    <div class="card" style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.75rem;">
        3. Media &amp; Imagery
      </h2>
      <div class="product-form-grid">
        <div class="form-group full-span">
          <?php if (!empty($edit_product['images'])): ?>
            <label>Current Product Images:</label>
            <div class="prod-current-images-grid">
              <?php foreach ($edit_product['images'] as $img): ?>
                <div class="prod-preview-card">
                  <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Product Media">
                  <button type="button" class="prod-image-remove-btn" onclick="removeProductImage(this, '<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>')" aria-label="Remove image">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <label for="prodImgUpload">Upload New Product Image (JPG, PNG, WEBP)</label>
          <div class="prod-image-uploader-box">
            <input type="file" id="prodImgUpload" name="product_image" accept="image/jpeg,image/png,image/webp,image/gif" style="display:block; margin: 0 auto;">
            <p style="font-size: 12px; color: var(--admin-text-muted); margin-top: 0.5rem;">Max 5MB. Uploaded automatically to <code>assets/images/store/</code>.</p>
          </div>
        </div>

        <div class="form-group full-span">
          <label for="prodImgUrl">Or specify Image URL (External / Asset path)</label>
          <input type="text" id="prodImgUrl" name="image_url" class="form-control" placeholder="https://... or /prospect-digital/assets/images/store/gateway.jpg">
          <span class="form-hint">Optionally link directly to any hosted image asset.</span>
        </div>
      </div>
    </div>

    <!-- Specifications & Highlights -->
    <div class="card" style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem; color: var(--admin-text-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.75rem;">
        4. Technical Specifications &amp; Features
      </h2>
      <div class="product-form-grid">
        <div class="form-group full-span">
          <label for="prodFeatures">Feature Bullet Points (One per line)</label>
          <textarea id="prodFeatures" name="features_text" class="form-control" rows="4" placeholder="Quad-Core ARM Cortex-A53 processor&#10;Dual Gigabit Ethernet ports with PoE&#10;DIN Rail Mountable Enclosure"><?php
            if (!empty($edit_product['features']) && is_array($edit_product['features'])) {
                echo htmlspecialchars(implode("\n", $edit_product['features']), ENT_QUOTES, 'UTF-8');
            }
          ?></textarea>
          <span class="form-hint">Rendered as high-priority checkmark bullets on the product details page.</span>
        </div>

        <div class="form-group full-span">
          <label>Technical Specifications (Key &amp; Value Table)</label>
          <div class="specs-builder-container" id="specsContainer">
            <?php
              $existing_specs = !empty($edit_product['specifications']) && is_array($edit_product['specifications'])
                ? $edit_product['specifications']
                : ['Processor' => '', 'Memory' => '', 'Operating Voltage' => ''];
              foreach ($existing_specs as $s_k => $s_v):
            ?>
              <div class="spec-builder-row">
                <input type="text" name="spec_key[]" class="form-control" placeholder="Specification Label (e.g. Processor)" value="<?= htmlspecialchars((string) $s_k, ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" name="spec_val[]" class="form-control" placeholder="Value (e.g. Quad-Core 1.5GHz)" value="<?= htmlspecialchars((string) $s_v, ENT_QUOTES, 'UTF-8') ?>">
                <button type="button" class="btn-remove-row" title="Remove row" onclick="this.closest('.spec-builder-row').remove();">&times;</button>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn--secondary btn--sm btn-add-spec-row" id="btnAddSpecRow">
            + Add Specification Row
          </button>
        </div>
      </div>
    </div>

    <!-- Form Actions Bar -->
    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
      <a href="<?= url('admin/store.php') ?>" class="btn btn--outline">
        Cancel
      </a>
      <button type="submit" class="btn btn--primary">
        <?= $view_mode === 'new' ? 'Publish Product to Store' : 'Save Changes' ?>
      </button>
    </div>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Auto-generate slug from title if new
      var nameInput = document.getElementById('prodName');
      var slugInput = document.getElementById('prodSlug');
      if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function () {
          slugInput.value = nameInput.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        });
      }

      // Add Spec Row
      var addSpecBtn = document.getElementById('btnAddSpecRow');
      var specsContainer = document.getElementById('specsContainer');
      if (addSpecBtn && specsContainer) {
        addSpecBtn.addEventListener('click', function () {
          var row = document.createElement('div');
          row.className = 'spec-builder-row';
          row.innerHTML = '<input type="text" name="spec_key[]" class="form-control" placeholder="Specification Label">' +
                          '<input type="text" name="spec_val[]" class="form-control" placeholder="Value">' +
                          '<button type="button" class="btn-remove-row" title="Remove row">&times;</button>';
          row.querySelector('.btn-remove-row').addEventListener('click', function () {
            row.remove();
          });
          specsContainer.appendChild(row);
        });
      }
    });
  </script>

<?php else: ?>
  <!-- =========================================================================
       CATALOG LIST VIEW: STATS + FILTER + DATA TABLE
       ========================================================================= -->
  <div class="page-header">
    <div>
      <h1 class="page-title">Store &amp; Products</h1>
      <p class="page-subtitle">Manage products, hardware kits, pricing, stock status, and digital assets.</p>
    </div>
    <div style="display:flex; gap:0.75rem;">
      <a href="<?= url('store') ?>" target="_blank" class="btn btn--outline">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        View Public Store &#8599;
      </a>
      <a href="<?= url('admin/store.php?action=new') ?>" class="btn btn--primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add New Product
      </a>
    </div>
  </div>

  <!-- KPI Stats Row -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-card__icon" style="background: var(--admin-brand-tint); color: var(--admin-brand);">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($total_products) ?></div>
        <div class="stat-card__label">Total Products</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($in_stock_count) ?></div>
        <div class="stat-card__label">In Stock</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(217, 119, 6, 0.1); color: #d97706;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($out_stock_count) ?></div>
        <div class="stat-card__label">Out of Stock / Preorder</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-card__icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      </div>
      <div>
        <div class="stat-card__value"><?= number_format($categories_count) ?></div>
        <div class="stat-card__label">Active Categories</div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Card -->
  <div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="<?= url('admin/store.php') ?>" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
      <div style="flex: 2; min-width: 240px;">
        <input type="text" name="q" class="form-control" placeholder="Search by title, SKU, or summary..." value="<?= htmlspecialchars($filter_q, ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div style="flex: 1; min-width: 180px;">
        <select name="category" class="form-control">
          <option value="all">All Categories</option>
          <?php foreach ($categories as $c_slug => $cat): ?>
            <?php if ($c_slug === 'all') continue; ?>
            <option value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>" <?= $filter_cat === $cat['name'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="flex: 1; min-width: 150px;">
        <select name="status" class="form-control">
          <option value="all">All Stock Statuses</option>
          <option value="in_stock" <?= $filter_st === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
          <option value="out_of_stock" <?= $filter_st === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
          <option value="preorder" <?= $filter_st === 'preorder' ? 'selected' : '' ?>>Pre-Order</option>
        </select>
      </div>

      <div style="display:flex; gap:0.5rem;">
        <button type="submit" class="btn btn--secondary">Filter</button>
        <?php if ($filter_q !== '' || $filter_cat !== '' || $filter_st !== ''): ?>
          <a href="<?= url('admin/store.php') ?>" class="btn btn--outline" title="Clear Filters">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Products Catalog Table -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($display_products)): ?>
      <div style="text-align: center; padding: 4.5rem 2rem;">
        <div style="width: 64px; height: 64px; border-radius: 16px; background: var(--admin-brand-tint); color: var(--admin-brand); display: grid; place-items: center; margin: 0 auto 1.25rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--admin-text-primary); margin-bottom: 0.5rem;">
          <?= $total_products === 0 ? 'No Products in Catalog Yet' : 'No Products Match Your Filter' ?>
        </h3>
        <p style="color: var(--admin-text-secondary); max-width: 440px; margin: 0 auto 1.5rem; font-size: 13.5px;">
          <?= $total_products === 0
            ? 'Get started by creating your first product. It will immediately appear on the public store catalog and support direct checkout.'
            : 'Try modifying your search keywords or clearing the category and stock status filters.' ?>
        </p>
        <?php if ($total_products === 0): ?>
          <a href="<?= url('admin/store.php?action=new') ?>" class="btn btn--primary">
            + Add First Product
          </a>
        <?php else: ?>
          <a href="<?= url('admin/store.php') ?>" class="btn btn--secondary">
            Clear Filters
          </a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr>
              <th style="width: 60px;">Image</th>
              <th>Product Details</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock Status</th>
              <th>Inventory</th>
              <th>Visibility</th>
              <th style="text-align: right; width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($display_products as $prod): ?>
              <?php
                $p_id = (string) $prod['id'];
                $p_name = (string) $prod['name'];
                $p_slug = (string) $prod['slug'];
                $p_sku = (string) $prod['sku'];
                $p_cat = (string) $prod['category'];
                $p_price = (float) $prod['price'];
                $p_discount = isset($prod['discount_price']) && is_numeric($prod['discount_price']) ? (float) $prod['discount_price'] : null;
                $p_stock = (string) $prod['stock_status'];
                $p_qty = (int) ($prod['stock_quantity'] ?? 0);
                $p_img = !empty($prod['images'][0]) ? (string) $prod['images'][0] : null;
                $p_published = !empty($prod['is_published']);

                $badge_class = match ($p_stock) {
                    'in_stock'     => 'badge--in-stock',
                    'out_of_stock' => 'badge--out-of-stock',
                    'preorder'     => 'badge--preorder',
                    default        => 'badge--discontinued',
                };
                $badge_label = match ($p_stock) {
                    'in_stock'     => 'In Stock',
                    'out_of_stock' => 'Out of Stock',
                    'preorder'     => 'Pre-Order',
                    default        => ucfirst($p_stock),
                };
              ?>
              <tr>
                <td>
                  <?php if ($p_img): ?>
                    <img src="<?= htmlspecialchars($p_img, ENT_QUOTES, 'UTF-8') ?>" alt="" class="product-table-thumb">
                  <?php else: ?>
                    <div class="product-thumb-placeholder">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/></svg>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="product-meta-cell">
                    <a href="<?= url('admin/store.php?action=edit&id=' . urlencode($p_id)) ?>" class="product-meta-title">
                      <?= htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <span class="product-meta-sku">SKU: <?= htmlspecialchars($p_sku, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                </td>
                <td>
                  <span class="status-badge" style="background: var(--admin-surface-subtle); color: var(--admin-text-primary); border: 1px solid var(--admin-border);">
                    <?= htmlspecialchars($p_cat, ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
                <td>
                  <div style="display:flex; flex-direction:column; gap:2px;">
                    <?php if ($p_discount !== null): ?>
                      <span style="font-weight: 800; color: var(--admin-text-primary); font-size: 13.5px;">
                        <?= store_format_currency($p_discount) ?>
                      </span>
                      <span style="font-size: 11px; color: var(--admin-text-muted); text-decoration: line-through;">
                        <?= store_format_currency($p_price) ?>
                      </span>
                    <?php else: ?>
                      <span style="font-weight: 800; color: var(--admin-text-primary); font-size: 13.5px;">
                        <?= store_format_currency($p_price) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <span class="status-badge <?= $badge_class ?>">
                    <?= $badge_label ?>
                  </span>
                </td>
                <td>
                  <span style="font-weight: 700; color: var(--admin-text-primary); font-family: monospace;">
                    <?= number_format($p_qty) ?> units
                  </span>
                </td>
                <td>
                  <form method="POST" action="<?= url('admin/store.php') ?>" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="toggle_publish">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($p_id, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn--sm" style="font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px; cursor: pointer; transition: all 0.2s ease; <?= $p_published ? 'background: rgba(5, 150, 105, 0.1); color: #059669; border: 1px solid rgba(5, 150, 105, 0.3);' : 'background: rgba(100, 116, 139, 0.1); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.3);' ?>" title="Click to toggle publish status">
                      <?= $p_published ? '● Published' : '○ Draft' ?>
                    </button>
                  </form>
                </td>
                <td style="text-align: right;">
                  <div style="display: inline-flex; gap: 0.45rem; align-items: center;">
                    <a href="<?= url('store/' . urlencode($p_slug)) ?>" target="_blank" class="btn btn--outline btn--sm" title="View live page on store">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                      View
                    </a>
                    <a href="<?= url('admin/store.php?action=edit&id=' . urlencode($p_id)) ?>" class="btn btn--secondary btn--sm" title="Edit product specifications">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      Edit
                    </a>
                    <form method="POST" action="<?= url('admin/store.php') ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this product?');">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($p_id, ENT_QUOTES, 'UTF-8') ?>">
                      <button type="submit" class="btn btn--danger btn--sm" title="Delete product">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<script>
function removeProductImage(btn, imageUrl) {
    if (confirm('Are you sure you want to remove this image from the product?')) {
        const card = btn.closest('.prod-preview-card');
        const form = card.closest('form');
        
        // Add hidden input to post array
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remove_images[]';
        hiddenInput.value = imageUrl;
        form.appendChild(hiddenInput);
        
        // Hide visually
        card.style.display = 'none';
    }
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
