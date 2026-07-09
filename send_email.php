<?php
/*
 * Contact-form mailer for rickykennedy.github.io.
 *
 * Returns JSON: { "code": "200"|"4xx"|"5xx", "message": "..." }
 * The frontend (contact.html) treats code "200" as success and shows
 * `message` in green, anything else in red.
 *
 * Security notes:
 *  - All inputs are validated and stripped of CR/LF before being used
 *    anywhere near mail headers, to prevent header injection / open-relay
 *    abuse (a classic PHP mail() vulnerability).
 *  - The visitor's address is placed in Reply-To, NOT From. From is a fixed
 *    address on our own domain so the message is more likely to pass
 *    SPF/DMARC and not be dropped as spoofed.
 */

header('Content-Type: application/json');

// ---- Config ----------------------------------------------------------------
$admin_email = 'rickyicezz@gmail.com';            // recipient (inbox)
$from_email  = 'no-reply@rickykennedy.github.io'; // envelope/From on our domain
$MAX_NAME    = 100;
$MAX_EMAIL   = 254;   // RFC 5321 max
$MAX_MESSAGE = 5000;

// ---- Helpers ---------------------------------------------------------------
function fail($code, $message) {
    echo json_encode(array('code' => $code, 'message' => $message));
    exit;
}

// Reject anything containing CR or LF — the vector for header injection.
function has_crlf($value) {
    return preg_match('/[\r\n]/', $value) === 1;
}

// ---- Only accept POST ------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('405', 'Method not allowed.');
}

// ---- Presence check --------------------------------------------------------
$name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';
$email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$submit  = isset($_POST['submit'])  ? $_POST['submit']        : '';

if ($submit !== 'submit') {
    fail('400', 'Bad request.');
}
if ($name === '' || $email === '' || $message === '') {
    fail('400', 'Please fill in all the form.');
}

// ---- Length limits ---------------------------------------------------------
if (strlen($name) > $MAX_NAME || strlen($email) > $MAX_EMAIL || strlen($message) > $MAX_MESSAGE) {
    fail('400', 'One of the fields is too long.');
}

// ---- Header-injection guard (name + email must be single-line) -------------
if (has_crlf($name) || has_crlf($email)) {
    fail('400', 'Invalid characters detected.');
}

// ---- Email validation ------------------------------------------------------
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('400', 'Please enter a valid email address.');
}

// ---- Build the message safely ----------------------------------------------
// Subject: strip newlines and collapse the name to plain text.
$safe_name = str_replace(array("\r", "\n"), ' ', $name);
$subject   = date('d M Y') . ' - Request for Ricky Kennedy from ' . $safe_name;

// Headers: fixed From on our domain; visitor address only in Reply-To.
$headers  = 'From: Ricky Kennedy Website <' . $from_email . '>' . "\r\n";
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

// Body: include sender details as plain text. CR/LF in the body is harmless —
// it cannot escape into the header section.
$body  = 'Name: ' . $name . "\n";
$body .= 'Email: ' . $email . "\n\n";
$body .= $message . "\n";

// ---- Send ------------------------------------------------------------------
if (mail($admin_email, $subject, $body, $headers)) {
    fail('200', 'Your message has been successfully sent, I will get back to you soon.');
} else {
    fail('500', 'Sorry, the message could not be sent right now. Please email me directly.');
}
