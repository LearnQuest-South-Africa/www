<?php
// send.php — receives POSTed form and relays to email via PHP's mail()
// Configure
$TO = 'billing@skunkworks.africa';
$FROM = 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'yourdomain.tld'); // set to an existing sender on your host if needed
$SUBJECT_PREFIX = 'Domain Offer: ';

// Helper to sanitize and guard against header injection
function clean($s) {
  $s = trim($s ?? '');
  $s = str_replace(["\r", "\n"], [' ', ' '], $s); // prevent header injection
  return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
}

// Basic check: POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'Method Not Allowed';
  exit;
}

// Honeypot
if (!empty($_POST['company'])) {
  // likely a bot
  http_response_code(200);
  echo 'OK';
  exit;
}

// Collect inputs
$domain   = clean($_POST['domain'] ?? ($_SERVER['HTTP_HOST'] ?? ''));
$name     = clean($_POST['name'] ?? '');
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone    = clean($_POST['phone'] ?? '');
$currency = clean($_POST['currency'] ?? '');
$amount   = clean($_POST['amount'] ?? '');
$message  = trim($_POST['message'] ?? '');

// Validate
$errors = [];
if ($name === '') $errors[] = 'Name is required';
if (!$email) $errors[] = 'Valid email is required';
if ($amount === '' || !is_numeric($amount) || floatval($amount) <= 0) $errors[] = 'Valid amount required';

if (count($errors) > 0) {
  http_response_code(422);
  header('Content-Type: application/json');
  echo json_encode(['ok'=>false, 'errors'=>$errors]);
  exit;
}

// Compose email
$subject = $SUBJECT_PREFIX . $domain;
$lines = [
  "Domain: $domain",
  "Name: $name",
  "Email: " . ($email ?: ''),
  "Phone: " . ($phone ?: '-'),
  "Offer: $currency $amount",
  "Message: " . ($message ?: '-'),
  "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-'),
  "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? '-'),
  "Submitted: " . gmdate('c'),
];
$body = implode("\n", $lines);

// Headers
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/plain; charset=UTF-8';
$headers[] = 'From: Domain Offers <' . $FROM . '>';
if ($email) { $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>'; }
$headers_str = implode("\r\n", $headers);

// Send
$sent = @mail($TO, $subject, $body, $headers_str);

// Response
header('Content-Type: application/json');
if ($sent) {
  echo json_encode(['ok'=>true]);
} else {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'Mailer failure (PHP mail() returned false). Ensure mail is enabled or switch to SMTP with PHPMailer).']);
}
