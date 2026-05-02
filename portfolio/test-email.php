<?php
/**
 * Email Configuration Diagnostic Script
 * Upload this file to your hosting and visit:
 * yoursite/test-email.php
 */

/* ================================
   LOAD .ENV FILE
================================ */

function loadEnv($path)
{
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (trim($line) === '' || strpos(trim($line), '#') === 0) continue;

        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . "=" . trim($value));
    }
}

loadEnv(__DIR__ . '/.env');

/* ================================
   READ SMTP CONFIG
================================ */

$SMTP_HOST   = getenv('SMTP_HOST');
$SMTP_PORT   = getenv('SMTP_PORT') ?: 587;
$SMTP_USER   = getenv('SMTP_USER');
$SMTP_PASS   = getenv('SMTP_PASS');
$SMTP_SECURE = getenv('SMTP_SECURE') ?: 'tls';

/* ================================
   RUN TESTS
================================ */

$tests = [];

/* PHP mail() test */
$tests[] = [
    'name' => 'PHP mail() function',
    'status' => function_exists('mail') ? '✓ Available' : '✗ Not available',
    'severity' => function_exists('mail') ? 'success' : 'error'
];

/* SMTP variables */
function smtpTest($name, $value)
{
    return [
        'name' => $name,
        'status' => $value ? "✓ Configured" : "✗ Missing",
        'severity' => $value ? 'success' : 'warning'
    ];
}

$tests[] = smtpTest('SMTP_HOST', $SMTP_HOST);
$tests[] = smtpTest('SMTP_PORT', $SMTP_PORT);
$tests[] = smtpTest('SMTP_USER', $SMTP_USER);
$tests[] = smtpTest('SMTP_PASS', $SMTP_PASS);
$tests[] = smtpTest('SMTP_SECURE', $SMTP_SECURE);

/* PHPMailer check */
$autoload = __DIR__ . '/vendor/autoload.php';

$tests[] = [
    'name' => 'PHPMailer Library',
    'status' => file_exists($autoload)
        ? '✓ Installed'
        : '✗ Not installed',
    'severity' => file_exists($autoload) ? 'success' : 'error'
];

/* SMTP connection test */
$phpmailerStatus = 'Not tested';
$phpmailerSeverity = 'info';

if (file_exists($autoload) && $SMTP_HOST && $SMTP_USER && $SMTP_PASS) {

    require $autoload;

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE;
        $mail->Port = (int)$SMTP_PORT;
        $mail->Timeout = 10;

        /* CONNECT ONLY — NO EMAIL SENT */
        if ($mail->smtpConnect()) {
            $phpmailerStatus = '✓ SMTP connection successful';
            $phpmailerSeverity = 'success';
            $mail->smtpClose();
        }

    } catch (Exception $e) {
        $phpmailerStatus = '✗ ' . $e->getMessage();
        $phpmailerSeverity = 'error';
    }
}

$tests[] = [
    'name' => 'SMTP Connection Test',
    'status' => $phpmailerStatus,
    'severity' => $phpmailerSeverity
];

?>

<!DOCTYPE html>
<html>
<head>
<title>Email Diagnostic</title>

<style>
body {font-family: Arial; background:#f4f4f4; padding:30px;}
.container {background:white; padding:25px; border-radius:8px;}
.test {padding:12px; margin:10px 0; border-left:5px solid;}
.success {border-color:green; background:#f0fff0;}
.error {border-color:red; background:#fff0f0;}
.warning {border-color:orange; background:#fffaf0;}
.info {border-color:blue; background:#f0f8ff;}
</style>

</head>

<body>

<div class="container">

<h2>Email Configuration Test</h2>

<p><b>PHP Version:</b> <?= phpversion(); ?></p>
<p><b>.env file:</b> <?= file_exists(__DIR__.'/.env') ? "✓ Found" : "✗ Missing"; ?></p>

<?php foreach ($tests as $t): ?>
<div class="test <?= $t['severity']; ?>">
<b><?= $t['name']; ?></b><br>
<?= $t['status']; ?>
</div>
<?php endforeach; ?>

<h3>Setup Reminder</h3>

<p>Create a <b>.env</b> file:</p>

<pre>
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your@gmail.com
SMTP_PASS=app_password
SMTP_SECURE=tls
</pre>

</div>

</body>
</html>
