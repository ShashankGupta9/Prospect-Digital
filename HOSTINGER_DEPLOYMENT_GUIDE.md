# Hostinger Deployment Guide — Prospect Digital

This guide explains how to upload, configure, and launch the **Prospect Digital** website on **Hostinger** (Shared Web Hosting, Cloud Hosting, or cPanel/hPanel).

---

## 📋 Pre-Deployment Checklist

Before you begin, ensure you have:
1. Active Hostinger Hosting Plan with your domain connected (e.g., `prospectdigital.in`).
2. Access to **Hostinger hPanel** (`https://hpanel.hostinger.com`).
3. The complete `prospect-digital` project folder.

---

## 🚀 Step 1: Upload Files to Hostinger

### Option A: Using Hostinger File Manager (Recommended & Fastest)
1. Log in to **Hostinger hPanel**.
2. Go to **Websites** → click **Manage** next to your domain.
3. Open **File Manager** (Files → File Manager).
4. Navigate into the **`public_html`** directory.
5. Create a ZIP archive of all files inside your local `prospect-digital` folder:
   - Select all files and folders inside `prospect-digital/` (including `.htaccess`, `index.php`, `includes/`, `data/`, `assets/`, etc.).
   - Compress them into `prospect-digital.zip`.
6. Click **Upload** in Hostinger File Manager, select `prospect-digital.zip`, and wait for the upload to complete.
7. Right-click the uploaded ZIP file and choose **Extract** directly into `public_html/`.
8. Delete the `.zip` file after extraction.

> [!IMPORTANT]
> Make sure the contents are extracted directly inside `public_html/` so that `index.php` and `.htaccess` are directly at `public_html/index.php` and `public_html/.htaccess`.

---

## 🗄️ Step 2: Create MySQL Database & Import `database.sql`

1. In **Hostinger hPanel**, navigate to **Databases** → **Management**.
2. Under **Create a New MySQL Database and Database User**:
   - **Database name**: e.g., `u123456789_prospect`
   - **Username**: e.g., `u123456789_dbuser`
   - **Password**: Enter a strong password (copy and save this password!).
   - Click **Create**.
3. Under **List of Current MySQL Databases And Users**, locate your newly created database and click **Enter phpMyAdmin**.
4. Inside phpMyAdmin:
   - Click the **Import** tab in the top navigation bar.
   - Click **Choose File** and select **`database.sql`** from your project root.
   - Click **Import** (or **Go**) at the bottom.
   - You will see a green success message: *Import has been successfully finished, 16 tables created*.

---

## ⚙️ Step 3: Configure Database Credentials

You can configure your Hostinger database in **either of two ways**:

### Method 1: Using `.env` (Recommended & Most Secure)
1. In Hostinger File Manager in `public_html/`, rename `.env.example` to **`.env`** (or create a new file named `.env`).
2. Fill in your Hostinger database details:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   SITE_URL=https://yourdomain.com

   DB_HOST=localhost
   DB_NAME=u123456789_prospect
   DB_USER=u123456789_dbuser
   DB_PASS=YourStrongDatabasePassword123!
   DB_PORT=3306
   ```

### Method 2: Editing `includes/config.php` Directly
If you prefer not to use `.env`, open `includes/config.php` in File Manager and update lines 37–41:
```php
define('DB_HOST',    'localhost');
define('DB_NAME',    'u123456789_prospect');
define('DB_USER',    'u123456789_dbuser');
define('DB_PASS',    'YourStrongDatabasePassword123!');
define('DB_PORT',    3306);
```

---

## 📧 Step 4: Configure Email & SMTP for Contact Form

Hostinger restricts unauthenticated `mail()`. To ensure all client enquiries and consultation requests are delivered reliably:

1. In **Hostinger hPanel**, go to **Emails** → **Email Accounts**.
2. Create an email account for your domain: e.g., `hello@prospectdigital.in`.
3. In your `.env` file (or `includes/config.php`), configure the SMTP settings:
   ```env
   MAIL_DRIVER=smtp
   MAIL_FROM=hello@prospectdigital.in
   MAIL_TO=hello@prospectdigital.in

   SMTP_HOST=smtp.hostinger.com
   SMTP_PORT=465
   SMTP_USER=hello@prospectdigital.in
   SMTP_PASS=YourEmailPassword123!
   SMTP_ENCRYPTION=ssl
   ```

*(Note: Even if SMTP is not configured, the site will safely store all incoming enquiries into MySQL and the backup JSON log files in `data/enquiries/` without losing any submissions).*

---

## 🔒 Step 5: Verify File Permissions

In Hostinger File Manager, ensure the following folders have write permissions (`0775` or `0755`):
- `data/`
- `data/enquiries/`
- `data/admin/`
- `assets/images/store/`

*(Hostinger sets standard folders to 0755 by default, which works automatically).*

---

## 🔍 Step 6: Run Verification Diagnostics

1. Open your browser and navigate to:
   ```
   https://yourdomain.com/setup-check.php
   ```
2. Check that all diagnostics are marked **[PASS]**:
   - ✅ PHP Version (PHP 8.0+)
   - ✅ PDO & MySQL driver loaded
   - ✅ Database Connected to your Hostinger database
   - ✅ All 16 database tables found
   - ✅ Folders writable
3. Test key pages:
   - Homepage: `https://yourdomain.com/`
   - Services: `https://yourdomain.com/services`
   - Store: `https://yourdomain.com/store`
   - Contact form: `https://yourdomain.com/contact`
   - Admin panel: `https://yourdomain.com/admin/login.php`
4. **Delete `setup-check.php`** from `public_html/` after verification for security.

---

## 🛡️ Admin Panel Access

- **Admin URL**: `https://yourdomain.com/admin/login.php`
- **Default Username**: `admin`
- **Default Password**: `Admin@2026!` *(or password defined in `data/admin/config.php`)*
