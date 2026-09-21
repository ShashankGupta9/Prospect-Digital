Prospect Digital — stored enquiries
===================================
One JSON object per line, one file per month: enquiries-YYYY-MM.jsonl

Written by store_enquiry() in includes/functions.php. Each record holds the
submitted fields, a reference number (e.g. PD-250916-A1B2), the timestamp,
a hashed IP signature used only for abuse checks, and a mail_sent flag.

Back this folder up with the rest of your site, and review the retention
period stated in your privacy policy. The .htaccess in this folder blocks
web access — keep it in place on Apache hosting.
