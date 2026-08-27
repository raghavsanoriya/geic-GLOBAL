<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Method not allowed.']);
}

if (! empty($_POST['website'] ?? '')) {
    respond(200, ['success' => true, 'message' => 'Thank you. Your enquiry has been received.']);
}

$configFile = dirname(__DIR__, 2).'/.tg_form_config.php';
if (! is_file($configFile)) {
    error_log('Trans Globe form configuration is missing.');
    respond(500, ['success' => false, 'message' => 'The form is temporarily unavailable. Please try again shortly.']);
}

$config = require $configFile;
if (! is_array($config)) {
    error_log('Trans Globe form configuration is invalid.');
    respond(500, ['success' => false, 'message' => 'The form is temporarily unavailable. Please try again shortly.']);
}

function value(string $key, int $maxLength = 255): string
{
    $value = trim((string) ($_POST[$key] ?? ''));

    return mb_substr($value, 0, $maxLength);
}

$name = value('name', 120);
$phone = value('phone', 30);
$email = value('email', 190);
$qualification = value('qualification', 100);
$passingYear = value('passing_year', 4);
$score = value('score', 30);
$country = value('country', 100);

if ($name === '' || $phone === '' || $email === '' || $qualification === '' || $passingYear === '' || $score === '' || $country === '') {
    respond(422, ['success' => false, 'message' => 'Please complete all required fields.']);
}
if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(422, ['success' => false, 'message' => 'Please enter a valid email address.']);
}
if (! preg_match('/^\\d{4}$/', $passingYear) || (int) $passingYear < 1990 || (int) $passingYear > 2035) {
    respond(422, ['success' => false, 'message' => 'Please enter a valid passing year.']);
}

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['db_host'], $config['db_name']);
    $database = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $statement = $database->prepare(
        'INSERT INTO profile_enquiries (full_name, phone, email, qualification, passing_year, academic_score, preferred_country, submitted_at, source_ip, user_agent)'
        .'VALUES (:full_name, :phone, :email, :qualification, :passing_year, :academic_score, :preferred_country, NOW(), :source_ip, :user_agent)'
    );
    $statement->execute([
        ':full_name' => $name,
        ':phone' => $phone,
        ':email' => $email,
        ':qualification' => $qualification,
        ':passing_year' => (int) $passingYear,
        ':academic_score' => $score,
        ':preferred_country' => $country,
        ':source_ip' => mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ':user_agent' => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
    ]);
} catch (Throwable $error) {
    error_log('Trans Globe form submission failed: '.$error->getMessage());
    respond(500, ['success' => false, 'message' => 'We could not send your enquiry. Please try again shortly.']);
}

respond(201, ['success' => true, 'message' => 'Thank you. Your profile evaluation request has been received.']);
