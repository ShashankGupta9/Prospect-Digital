<?php
/**
 * Prospect Digital — Website Media & Image Manager
 * ---------------------------------------------------------------------------
 * Allows administrator to inspect, replace, and restore any image on the site.
 * Creates automatic backups in data/admin/media_backups/ before replacing.
 * ---------------------------------------------------------------------------
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$page_title = 'Website Media Manager';
$active_nav = 'media';
$csrf_token = admin_csrf_token();

$assets_root = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets');
$backup_dir  = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'media_backups';

if (!is_dir($backup_dir)) {
    @mkdir($backup_dir, 0775, true);
}

// ---------------------------------------------------------------------------
// 1. Known image map & usage contexts
// ---------------------------------------------------------------------------
$known_contexts = [
    'images/products/bizora.jpg'                => 'Homepage Products Strip & Bizora CRM Product Page',
    'images/products/medvora.jpg'               => 'Homepage Products Strip & Medvora Clinic Page',
    'images/products/routeflow.jpg'             => 'Homepage Products Strip & RouteFlow Logistics Page',
    'images/products/schova.jpg'                => 'Homepage Products Strip & Schova School ERP Page',
    'images/products/workora.jpg'               => 'Homepage Products Strip & Workora HR Page',
    'images/services/ai-automation.jpg'         => 'Services Index & AI Automation Details',
    'images/services/branding-creative.jpg'     => 'Services Index & Branding Creative Details',
    'images/services/digital-marketing.jpg'     => 'Services Index & Digital Marketing Details',
    'images/services/growth-strategy.jpg'       => 'Services Index & Growth Strategy Details',
    'images/services/it-services-cloud.jpg'     => 'Services Index & IT Services Details',
    'images/services/performance-marketing.jpg' => 'Services Index & Performance Marketing Details',
    'images/services/software-development.jpg'  => 'Services Index & Software Development Details',
    'images/services/website-development.jpg'   => 'Services Index & Website Development Details',
    'images/og-default.jpg'                     => 'Global Open Graph / WhatsApp / Social Share Banner',
    'images/prospect-digital-logo-full.webp'    => 'Full Brand Banner & Artwork',
    'logo/prospect-digital-logo.png'            => 'Public Header Navbar Logo',
    'logo/prospect-digital-logo-white.png'      => 'Dark Mode & Footer Logo Alternative',
    'logo/favicon.png'                          => 'Browser Tab Icon & Bookmark Favicon',
    'logo/apple-touch-icon.png'                 => 'iOS & Mobile Home Screen Touch Icon',
    'hero/home-dashboard.webp'                  => 'Homepage Hero Banner Visual',
    'hero/about-team.webp'                      => 'About Us Page Hero Artwork',
    'hero/contact-office.webp'                  => 'Contact Page Hero Visual',
    'hero/software-development.webp'            => 'Software Development Service Hero',
    'hero/website-development.webp'             => 'Website Development Service Hero',
    'hero/ai-automation.webp'                   => 'AI Automation Service Hero',
    'hero/bizora.webp'                          => 'Bizora Product Showcase Hero',
    'hero/workora.webp'                         => 'Workora Product Showcase Hero',
    'hero/routeflow.webp'                       => 'RouteFlow Product Showcase Hero',
    'hero/medvora.webp'                         => 'Medvora Product Showcase Hero',
    'hero/schova.webp'                          => 'Schova Product Showcase Hero',
];

// ---------------------------------------------------------------------------
// 2. Handle Image Upload & Replacement
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $csrf   = $_POST['csrf_token'] ?? '';

    if (!admin_verify_csrf($csrf)) {
        admin_set_flash('error', 'Security token invalid or expired. Please try again.');
        header('Location: ' . url('admin/media.php'));
        exit;
    }

    // A. Replace Image Action
    if ($action === 'replace_image') {
        $target_rel = trim((string) ($_POST['target_rel'] ?? ''));
        $target_path = realpath($assets_root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $target_rel));

        // Security check: Must reside within assets folder
        if (!$target_path || !str_starts_with($target_path, $assets_root) || !is_file($target_path)) {
            admin_set_flash('error', 'Invalid target image specified.');
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        if (empty($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
            $err_code = $_FILES['image_file']['error'] ?? 'empty';
            admin_set_flash('error', "Upload failed (Error code: {$err_code}). Please select a valid file.");
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        $uploaded = $_FILES['image_file'];
        $max_size = 8 * 1024 * 1024; // 8MB

        if ($uploaded['size'] > $max_size) {
            admin_set_flash('error', 'Uploaded image exceeds the 8MB size limit.');
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        // Validate image MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $uploaded['tmp_name']);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
        if (!in_array($mime, $allowed_mimes, true)) {
            admin_set_flash('error', "Unsupported image format ({$mime}). Allowed: JPG, PNG, WebP, SVG.");
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        // 1. Create a timestamped backup before replacing
        $safe_key   = preg_replace('/[^a-zA-Z0-9_-]/', '_', $target_rel);
        $backup_file = $backup_dir . DIRECTORY_SEPARATOR . $safe_key . '__' . date('Ymd_His') . '.bak';
        @copy($target_path, $backup_file);

        // 2. Overwrite target file
        if (@move_uploaded_file($uploaded['tmp_name'], $target_path)) {
            @touch($target_path); // Refresh filemtime for instant browser cache-busting
            admin_set_flash('success', "Image '{$target_rel}' was updated successfully! A backup of the previous version was preserved.");
        } else {
            admin_set_flash('error', "Failed to write image to target location. Please check directory permissions.");
        }

        header('Location: ' . url('admin/media.php'));
        exit;
    }

    // B. Restore Previous Backup Action
    if ($action === 'restore_backup') {
        $target_rel = trim((string) ($_POST['target_rel'] ?? ''));
        $target_path = realpath($assets_root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $target_rel));

        if (!$target_path || !str_starts_with($target_path, $assets_root)) {
            admin_set_flash('error', 'Invalid target image for restore.');
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        $safe_key = preg_replace('/[^a-zA-Z0-9_-]/', '_', $target_rel);
        $backups = glob($backup_dir . DIRECTORY_SEPARATOR . $safe_key . '__*.bak');

        if (empty($backups)) {
            admin_set_flash('error', 'No previous backups found for this image.');
            header('Location: ' . url('admin/media.php'));
            exit;
        }

        // Sort to get newest backup
        rsort($backups);
        $latest_backup = $backups[0];

        if (@copy($latest_backup, $target_path)) {
            @touch($target_path);
            admin_set_flash('success', "Image '{$target_rel}' was successfully restored to its previous backup version!");
        } else {
            admin_set_flash('error', 'Failed to restore image from backup file.');
        }

        header('Location: ' . url('admin/media.php'));
        exit;
    }
}

// ---------------------------------------------------------------------------
// 3. Scan all images in assets
// ---------------------------------------------------------------------------
function get_all_website_images(string $assets_root, string $backup_dir, array $known_contexts): array
{
    $images = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($assets_root));

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            continue;
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
            continue;
        }

        $full_path = $file->getPathname();
        $rel_path  = str_replace('\\', '/', substr($full_path, strlen($assets_root) + 1));

        // Group into category
        if (str_starts_with($rel_path, 'images/products/')) {
            $cat = 'Products';
        } elseif (str_starts_with($rel_path, 'images/services/')) {
            $cat = 'Services';
        } elseif (str_starts_with($rel_path, 'hero/')) {
            $cat = 'Hero Art';
        } elseif (str_starts_with($rel_path, 'logo/')) {
            $cat = 'Logos & Branding';
        } else {
            $cat = 'General';
        }

        $safe_key = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rel_path);
        $backups = glob($backup_dir . DIRECTORY_SEPARATOR . $safe_key . '__*.bak');
        $has_backup = !empty($backups);

        $dimensions = '—';
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $size_info = @getimagesize($full_path);
            if ($size_info) {
                $dimensions = $size_info[0] . ' × ' . $size_info[1] . ' px';
            }
        }

        $images[] = [
            'rel_path'     => $rel_path,
            'filename'     => $file->getFilename(),
            'category'     => $cat,
            'url'          => url('assets/' . $rel_path) . '?v=' . filemtime($full_path),
            'size_bytes'   => $file->getSize(),
            'size_fmt'     => round($file->getSize() / 1024, 1) . ' KB',
            'dimensions'   => $dimensions,
            'modified_at'  => date('M d, Y h:i A', filemtime($full_path)),
            'context'      => $known_contexts[$rel_path] ?? ('assets/' . $rel_path),
            'has_backup'   => $has_backup,
            'backup_count' => count($backups),
        ];
    }

    // Sort: Products first, then Services, Hero, Logos
    $cat_order = ['Products' => 1, 'Services' => 2, 'Hero Art' => 3, 'Logos & Branding' => 4, 'General' => 5];
    usort($images, static function ($a, $b) use ($cat_order) {
        $c1 = $cat_order[$a['category']] ?? 9;
        $c2 = $cat_order[$b['category']] ?? 9;
        if ($c1 !== $c2) {
            return $c1 <=> $c2;
        }
        return strcmp($a['rel_path'], $b['rel_path']);
    });

    return $images;
}

$all_images = get_all_website_images($assets_root, $backup_dir, $known_contexts);
$filter_cat = trim((string) ($_GET['cat'] ?? ''));
$search_q   = trim((string) ($_GET['q'] ?? ''));

$filtered_images = array_values(array_filter($all_images, static function ($img) use ($filter_cat, $search_q) {
    if ($filter_cat !== '' && $img['category'] !== $filter_cat) {
        return false;
    }
    if ($search_q !== '') {
        $needle = mb_strtolower($search_q);
        $haystack = mb_strtolower($img['rel_path'] . ' ' . $img['context'] . ' ' . $img['category']);
        if (!str_contains($haystack, $needle)) {
            return false;
        }
    }
    return true;
}));

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Website Media &amp; Image Manager</h1>
    <p class="page-subtitle">Replace or update any image displayed on the website. Browser caches are cleared automatically.</p>
  </div>
  <div style="display:flex; gap:0.75rem; align-items:center;">
    <span class="status-badge badge--contacted" style="padding: 6px 12px; font-size:12px;">
      <?= count($all_images) ?> Total Images Active
    </span>
  </div>
</div>

<!-- Filters Bar -->
<div class="card">
  <div class="card-body" style="padding: 1.25rem;">
    <form method="GET" action="" class="filters-bar">
      <input type="text" name="q" class="form-input" placeholder="Search by image name or location..." value="<?= htmlspecialchars($search_q, ENT_QUOTES, 'UTF-8') ?>" style="min-width: 260px; flex: 1;">

      <select name="cat" class="form-select" onchange="this.form.submit()">
        <option value="">All Categories (<?= count($all_images) ?>)</option>
        <?php foreach (['Products', 'Services', 'Hero Art', 'Logos & Branding', 'General'] as $c): ?>
          <option value="<?= $c ?>" <?= $filter_cat === $c ? 'selected' : '' ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn btn--brand">Filter</button>
      <?php if ($search_q !== '' || $filter_cat !== ''): ?>
        <a href="<?= url('admin/media.php') ?>" class="btn btn--outline">Reset</a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Media Grid -->
<?php if (empty($filtered_images)): ?>
  <div class="card">
    <div class="card-body" style="text-align: center; padding: 4rem 2rem; color: var(--admin-text-muted);">
      <p style="font-size: 16px; font-weight: 600; margin-bottom: 0.5rem;">No media files match your criteria.</p>
      <a href="<?= url('admin/media.php') ?>" class="btn btn--secondary btn--sm">View All Images</a>
    </div>
  </div>
<?php else: ?>
  <div class="media-grid">
    <?php foreach ($filtered_images as $img): ?>
      <div class="media-card" data-category="<?= htmlspecialchars($img['category'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="media-card__preview">
          <span class="media-card__badge"><?= htmlspecialchars($img['category'], ENT_QUOTES, 'UTF-8') ?></span>
          <img src="<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>" class="media-card__img" loading="lazy">
        </div>

        <div class="media-card__body">
          <div class="media-card__name"><?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?></div>
          
          <div class="media-card__location" title="File path in project">
            assets/<?= htmlspecialchars($img['rel_path'], ENT_QUOTES, 'UTF-8') ?>
          </div>

          <div class="media-card__meta">
            <div><strong>Dimensions:</strong> <?= htmlspecialchars($img['dimensions'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>File Size:</strong> <?= htmlspecialchars($img['size_fmt'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Updated:</strong> <?= htmlspecialchars($img['modified_at'], ENT_QUOTES, 'UTF-8') ?></div>
            <div style="margin-top:0.35rem; color: var(--admin-text-primary); font-weight:600;">
              <strong>Used In:</strong> <?= htmlspecialchars($img['context'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>

          <div class="media-card__actions">
            <button type="button" class="btn btn--brand btn--sm" style="flex:1;" onclick="openReplaceModal('<?= htmlspecialchars($img['rel_path'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($img['filename'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($img['url'], ENT_QUOTES, 'UTF-8') ?>')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Replace Image
            </button>

            <?php if ($img['has_backup']): ?>
              <form method="POST" action="" onsubmit="return confirm('Restore this image to its previous version?')" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="restore_backup">
                <input type="hidden" name="target_rel" value="<?= htmlspecialchars($img['rel_path'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn--secondary btn--sm" title="Revert to previous version (<?= $img['backup_count'] ?> backup saved)">
                  Revert
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Replace Image Modal -->
<div id="replaceModal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center; padding:1.5rem;">
  <div class="card" style="width:100%; max-width:480px; margin-bottom:0; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
    <div class="card-header">
      <h2 class="card-title" id="modalTitle">Replace Image</h2>
      <button type="button" onclick="closeReplaceModal()" class="btn btn--outline btn--sm" style="padding:0.25rem 0.5rem;">✕</button>
    </div>
    <div class="card-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="action" value="replace_image">
        <input type="hidden" name="target_rel" id="modalTargetRel" value="">

        <div style="margin-bottom:1.25rem; text-align:center;">
          <div style="font-size:12px; color:var(--admin-text-muted); margin-bottom:0.5rem;">Current Image:</div>
          <img id="modalCurrentImg" src="" alt="Current Preview" style="max-height:120px; max-width:100%; border-radius:var(--admin-radius-sm); border:1px solid var(--admin-border);">
        </div>

        <div class="form-group">
          <label class="form-label" for="image_file">Select New Image File</label>
          <input type="file" name="image_file" id="image_file" class="form-input" accept=".jpg,.jpeg,.png,.webp,.svg" required onchange="previewUpload(this)">
          <div style="font-size:11px; color:var(--admin-text-muted); margin-top:0.4rem;">
            Supported formats: JPG, PNG, WebP, SVG. Max size: 8MB.<br>
            A backup of the current image will be created automatically.
          </div>
        </div>

        <div id="uploadPreviewBox" style="display:none; margin-bottom:1.25rem; text-align:center;">
          <div style="font-size:12px; color:var(--admin-brand); font-weight:700; margin-bottom:0.5rem;">New Selected Preview:</div>
          <img id="modalNewPreview" src="" alt="New Preview" style="max-height:120px; max-width:100%; border-radius:var(--admin-radius-sm); border:2px solid var(--admin-brand);">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem;">
          <button type="button" class="btn btn--outline" onclick="closeReplaceModal()">Cancel</button>
          <button type="submit" class="btn btn--brand">Upload &amp; Replace</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openReplaceModal(relPath, filename, currentUrl) {
  document.getElementById('modalTargetRel').value = relPath;
  document.getElementById('modalTitle').textContent = 'Replace ' + filename;
  document.getElementById('modalCurrentImg').src = currentUrl;
  document.getElementById('uploadPreviewBox').style.display = 'none';
  document.getElementById('image_file').value = '';
  document.getElementById('replaceModal').style.display = 'flex';
}

function closeReplaceModal() {
  document.getElementById('replaceModal').style.display = 'none';
}

function previewUpload(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('modalNewPreview').src = e.target.result;
      document.getElementById('uploadPreviewBox').style.display = 'block';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

// Close on backdrop click
document.getElementById('replaceModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeReplaceModal();
  }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
