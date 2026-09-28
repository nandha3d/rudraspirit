<?php
/**
 * GitHub Webhook Deployment Receiver
 * 
 * This script receives webhook calls from GitHub Actions
 * and triggers the deployment script on the server.
 */

// ─── Configuration ───────────────────────────────────────
$webhookSecret = trim(file_get_contents(__DIR__ . '/../.deploy_secret') ?: '');
$deployScript  = realpath(__DIR__ . '/../deploy.sh');
$logFile       = realpath(__DIR__ . '/../storage/logs') . '/deploy.log';
$lockFile      = realpath(__DIR__ . '/../storage') . '/deploy.lock';

// ─── Security Checks ────────────────────────────────────

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Verify deploy secret
$providedKey = $_SERVER['HTTP_X_DEPLOY_KEY'] ?? '';
if (empty($webhookSecret) || !hash_equals($webhookSecret, $providedKey)) {
    http_response_code(403);
    error_log("Deploy webhook: unauthorized attempt from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    die(json_encode(['error' => 'Unauthorized']));
}

// ─── Prevent Concurrent Deployments ─────────────────────
if (file_exists($lockFile)) {
    $lockTime = (int) file_get_contents($lockFile);
    // If lock is less than 10 minutes old, reject
    if (time() - $lockTime < 600) {
        http_response_code(429);
        die(json_encode(['error' => 'Deployment already in progress']));
    }
}

// Create lock file
file_put_contents($lockFile, time());

// ─── Trigger Deployment ─────────────────────────────────
$timestamp = date('Y-m-d H:i:s');
$projectDir = realpath(__DIR__ . '/..');

// Log start
$logEntry = "\n" . str_repeat('=', 60) . "\n";
$logEntry .= "[$timestamp] Deployment triggered via webhook\n";
$logEntry .= str_repeat('=', 60) . "\n";
file_put_contents($logFile, $logEntry, FILE_APPEND);

// Build the command to run in background
// The deploy script runs, output goes to log, and lock file is removed when done
$command = sprintf(
    'cd %s && bash deploy.sh >> %s 2>&1; rm -f %s &',
    escapeshellarg($projectDir),
    escapeshellarg($logFile),
    escapeshellarg($lockFile)
);

// Execute in background
exec($command);

// Return success immediately
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'status'  => 'success',
    'message' => 'Deployment started',
    'time'    => $timestamp,
    'log'     => 'storage/logs/deploy.log'
]);
