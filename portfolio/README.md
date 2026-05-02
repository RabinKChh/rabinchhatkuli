# Portfolio Contact Form – Email Setup Guide

The contact form on this portfolio website can send emails in two ways:

## 1. Quick Start (PHPMailer + SMTP)

**Step 1: Install PHPMailer with Composer**  
Open PowerShell in your project directory and run:
```powershell
composer install
```
This automatically installs PHPMailer and creates a `vendor/` folder.

**Step 2: Configure SMTP Credentials**  
Set environment variables on Windows. Choose one method:

### Method A: System Environment Variables (Recommended for Permanent Setup)
1. Press `Win + X` → **System** → **Advanced system settings**
2. Click **Environment Variables** button
3. Under **User variables**, click **New** and add:
   - `Variable name`: `SMTP_HOST` → `Value`: `smtp.gmail.com`
   - `Variable name`: `SMTP_PORT` → `Value`: `587`
   - `Variable name`: `SMTP_USER` → `Value`: `your@email.com`
   - `Variable name`: `SMTP_PASS` → `Value`: `your_app_password`
   - `Variable name`: `SMTP_SECURE` → `Value`: `tls`
4. Click **OK** twice to save
5. **Restart Apache** (in XAMPP Control Panel)
6. Restart PowerShell and browsers

### Method B: XAMPP httpd.conf (Local Development Only)
1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find your VirtualHost block or add one:
   ```apache
   <VirtualHost *:80>
     ServerName portfolio.local
     DocumentRoot "C:\xampp\htdocs\portfolio"
     SetEnv SMTP_HOST smtp.gmail.com
     SetEnv SMTP_PORT 587
     SetEnv SMTP_USER your@email.com
     SetEnv SMTP_PASS your_app_password
     SetEnv SMTP_SECURE tls
   </VirtualHost>
   ```
3. Restart Apache

### Method C: .env file (Using vlucas/phpdotenv)
1. Create `.env` file in project root:
   ```
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_USER=your@email.com
   SMTP_PASS=your_app_password
   SMTP_SECURE=tls
   ```
2. In `index.php` (after `<?php session_start();`), add:
   ```php
   if (file_exists(__DIR__ . '/.env')) {
       $envFile = file(__DIR__ . '/.env');
       foreach ($envFile as $line) {
           if (trim($line) && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
               list($key, $val) = explode('=', $line, 2);
               putenv(trim($key) . '=' . trim($val));
           }
       }
   }
   ```
3. Restart Apache

---

## Gmail SMTP Setup (Recommended)

If using Gmail, follow these steps:

1. Go to **Google Account** → [Security](https://myaccount.google.com/security)
2. Enable **2-Step Verification** (if not already enabled)
3. Go to **App passwords** (appears after 2FA is enabled)
4. Select **Mail** and **Windows Computer** → Generate password
5. Copy the 16-character password and use it in SMTP_PASS

**SMTP Settings for Gmail:**
```
SMTP_HOST: smtp.gmail.com
SMTP_PORT: 587
SMTP_USER: your.email@gmail.com
SMTP_PASS: xxxx xxxx xxxx xxxx  (app password, 16 chars)
SMTP_SECURE: tls
```

---

## Testing the Setup

### Method 1: Test Form
1. Open `https://yoursite.local/index.php#contact`
2. Fill out the contact form and submit
3. Check:
   - **Success message** appears on the page
   - Email arrives in your inbox (check Spam folder too)

### Method 2: Check PHP Error Log
Apache/PHP error log location:
```
C:\xampp\apache\logs\error.log
C:\xampp\php\logs\php_error.log
```

Search for "Contact form" errors to diagnose issues.

### Method 3: Debug Script (Optional)
Create `test-email.php` in the project root:
```php
<?php
putenv('SMTP_HOST=smtp.gmail.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=your@email.com');
putenv('SMTP_PASS=your_app_password');
putenv('SMTP_SECURE=tls');

require __DIR__ . '/vendor/autoload.php';

try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your@email.com';
    $mail->Password = 'your_app_password';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('your@email.com', 'Test');
    $mail->addAddress('rabinchhatkuli@gmail.com');
    $mail->Subject = 'SMTP Test';
    $mail->Body = 'If you see this, SMTP is working!';
    $mail->send();
    echo '✓ Email sent successfully!';
} catch (Exception $e) {
    echo '✗ Error: ' . $mail->ErrorInfo;
}
```

Then visit: `https://yoursite.local/test-email.php`

---

## Fallback: PHP mail() Function (Windows XAMPP)

If you prefer NOT to use external SMTP, you can configure XAMPP's built-in `mail()`:

1. Install **Fake Sendmail** or **msmtp**:
   - Download [**msmtp portable**](https://sourceforge.net/projects/msmtp/files/)
   - Extract to `C:\xampp\sendmail\`

2. Create `C:\xampp\sendmail\msmtprc` (no extension):
   ```
   defaults
   auth           on
   tls            on
   tls_trust_file C:/xampp/apache/bin/curl-ca-bundle.crt

   account        gmail
   host           smtp.gmail.com
   port           587
   from           your@gmail.com
   user           your@gmail.com
   password       your_app_password

   account default : gmail
   ```

3. Edit `C:\xampp\php\php.ini`:
   ```ini
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = your@gmail.com
   sendmail_path = "C:\xampp\sendmail\msmtp.exe -C C:\xampp\sendmail\msmtprc -t"
   ```

4. Restart Apache

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Message failed" error | 1. Check error log (above) 2. Verify SMTP env vars are set 3. Run test-email.php |
| Email goes to Spam | Add SPF/DKIM records to domain (Gmail will guide you) |
| "SMTP auth failed" | Wrong password. Use **App Password**, not Google password |
| "Connection timeout" | Firewall blocking port 587. Try port 465 with `SMTP_SECURE=ssl` |
| "vendor/autoload.php not found" | Run `composer install` in project directory |

---

## File Structure
```
portfolio/
├── index.php              # Main website + contact handler
├── composer.json          # PHP dependencies
├── composer.lock          # Lock file (auto-generated)
├── vendor/                # Installed packages (auto-generated)
├── portfolio.jpg          # Your profile image
└── README.md              # This file
```

---

**If issues persist**, check `C:\xampp\apache\logs\error.log` or enable display_errors in index.php:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

**Last updated:** February 16, 2026
