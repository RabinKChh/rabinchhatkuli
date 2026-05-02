# Portfolio Website – Production Deployment Guide

## Overview

This is a professional portfolio website with a working contact form. The contact form uses **native PHP mail()** — no external dependencies required.

## Deployment Instructions

### 1. Upload Files to Your Hosting

Upload these files to your hosting (rabinchhatkuli.rf.gd) via FTP/SFTP/Control Panel:

- `index.php` (main website + contact form handler)
- `portfolio.jpg` (your profile image)
- All CSS/JS are embedded in `index.php` — no external files needed

### 2. Verify Email Address

In `index.php`, around line 51, verify the recipient email:

```php
$to = "rabinchhatkuli@gmail.com";  // ← Make sure this is correct
```

### 3. Test the Contact Form

1. Visit your website: https://rabinchhatkuli.rf.gd
2. Navigate to the **Contact** section (#contact in URL)
3. Fill out the form and submit
4. You should see a success message or error feedback
5. Check your email inbox (including Spam/Promotions folder)

## How It Works

**Contact Form Flow:**

1. User submits form → JavaScript POSTs to `index.php`
2. Server-side validation: checks all fields, email format, length limits
3. Input sanitization: removes harmful characters, prevents injection attacks
4. Email sent via PHP `mail()` function to: `rabinchhatkuli@gmail.com`
5. Browser redirects with success/error message (prevents form resubmission)

## Troubleshooting

### "Failed to send message" Error

**Cause:** Hosting mail() function is not working or misconfigured

**Solutions:**

1. **Test mail() availability**
   - Create a test file: `test-mail.php`
   ```php
   <?php
   echo phpinfo();
   ?>
   ```
   - Visit: https://rabinchhatkuli.rf.gd/test-mail.php
   - Search for "mail support" — should show "Enabled"

2. **Check hosting error logs**
   - Log in to your hosting control panel
   - Find: Logs → PHP Error Log or Mail Log
   - Look for recent errors related to mail()

3. **Contact hosting support**
   - Ask if mail() is enabled on your account
   - Sometimes it's disabled and needs to be manually enabled

4. **Use an alternative hosting provider**
   - Some free hosts disable mail() for abuse prevention
   - Consider upgrading to a premium plan or switching hosts

### Email Goes to Spam

**Cause:** Your hosting server's mail reputation is low

**Solutions:**

1. Check Gmail **Spam** and **Promotions** folders
2. Mark email as "Not spam" to improve delivery
3. Create a Gmail filter to prevent spam classification
4. Contact hosting support to check mail server reputation

### Form Keeps Redirecting

**This is normal!** The form uses server-side redirects to prevent the browser's "Resend form data?" warning. This is the correct behavior.

## File Structure

```
portfolio/
├── index.php                 # Main page + contact form (MAIN FILE)
├── portfolio.jpg             # Your profile image
├── README_PRODUCTION.md      # This file
└── README.md                 # Original setup guide (can be deleted)
```

## Security Features

✓ Server-side input validation (not just client-side)
✓ Email header injection prevention
✓ Name/subject/message length limits
✓ Email format verification
✓ Invalid character filtering

## Future Enhancements

- Add CAPTCHA to prevent spam bots
- Implement rate limiting (max X emails per hour per IP)
- Store contact submissions in database
- Add "Contact received" auto-reply to user
- Integrate with marketing email service (Mailchimp, SendGrid)

## Support

If you encounter issues:

1. Check the **Troubleshooting** section above
2. Review your **hosting control panel** → Logs
3. Contact your **hosting provider's support**
4. Verify `rabinchhatkuli@gmail.com` is correct in the code

---

**Last Updated:** February 16, 2026  
**Version:** Production v1.0
