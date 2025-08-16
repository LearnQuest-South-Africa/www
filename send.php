<?php
// send.php — PHP mailer for learnquest.co.za offers
$TO = 'billing@skunkworks.africa';
$FROM = 'no-reply@learnquest.co.za'; // set to a valid sender on your host
$SUBJECT_PREFIX = 'Domain Offer: ';

function clean($s) {
  $s = trim($s ?? '');
  $s = str_replace(["\r", "\n"], [' ', ' '], $s);
  return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'Method Not Allowed';
  exit;
}

if (!empty($_POST['company'])) {
  http_response_code(200);
  echo 'OK';
  exit;
}

$domain   = 'learnquest.co.za';
$name     = clean($_POST['name'] ?? '');
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone    = clean($_POST['phone'] ?? '');
$currency = clean($_POST['currency'] ?? '');
$amount   = clean($_POST['amount'] ?? '');
$message  = trim($_POST['message'] ?? '');

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

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/plain; charset=UTF-8';
$headers[] = 'From: Domain Offers <' . $FROM . '>';
if ($email) { $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>'; }
$headers_str = implode("\r\n", $headers);

$sent = @mail($TO, $subject, $body, $headers_str);
header('Content-Type: application/json');
if ($sent) {
  echo json_encode(['ok'=>true]);
} else {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'error'=>'Mailer failure (PHP mail() returned false). Consider SMTP/PHPMailer).']);
}
