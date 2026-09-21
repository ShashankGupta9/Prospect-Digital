<?php
/**
 * Prospect Digital — Admin Footer Component
 */

declare(strict_types=1);

if (!defined('ADMIN_PANEL_ACTIVE')) {
    exit('Direct access not permitted.');
}
?>
  </main>
</div>

<script src="<?= url('admin/assets/js/admin.js') ?>?v=<?= filemtime(__DIR__ . '/../assets/js/admin.js') ?>"></script>
</body>
</html>
