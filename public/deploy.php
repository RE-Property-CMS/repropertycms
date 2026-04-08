<?php
/**
 * RePropertyCMS — Git-provider-agnostic Webhook Receiver
 *
 * Works with: GitHub, GitLab, Bitbucket, Gitea, or any provider
 * that supports webhook POST requests.
 *
 * Setup:
 *   1. Add DEPLOY_TOKEN=your-secret-here to your .env
 *   2. In your git provider, set webhook URL to:
 *      https://yoursite.com/deploy.php?token=your-secret-here
 *   3. Set Content-Type: application/json (any event, usually "push")
 *
 * Security: token-only auth — no provider-specific signature parsing
 * so this works universally across all git providers.
 */

// ── Load .env token ───────────────────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
$deployToken = null;
$deployBranch = 'main';

if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $key = trim($key);
        $val = trim($val, " \t\"'");
        if ($key === 'DEPLOY_TOKEN')  $deployToken  = $val;
        if ($key === 'DEPLOY_BRANCH') $deployBranch = $val;
    }
}

// ── Authenticate ──────────────────────────────────────────────────────────────
$providedToken = $_GET['token'] ?? $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? null;

if (empty($deployToken)) {
    http_response_code(500);
    die(json_encode(['error' => 'DEPLOY_TOKEN not configured in .env']));
}

if (!$providedToken || !hash_equals($deployToken, $providedToken)) {
    http_response_code(403);
    die(json_encode(['error' => 'Forbidden']));
}

// ── Only process POST requests ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'POST required']));
}

// ── Optionally verify branch from payload ─────────────────────────────────────
$payload = json_decode(file_get_contents('php://input'), true);

// GitHub / Gitea push: payload.ref = "refs/heads/main"
// GitLab push:         payload.ref = "refs/heads/main"
// Bitbucket push:      payload.push.changes[0].new.name = "main"
if ($payload) {
    $pushedRef = $payload['ref'] ?? null;
    if ($pushedRef) {
        $pushedBranch = str_replace('refs/heads/', '', $pushedRef);
        if ($pushedBranch !== $deployBranch) {
            http_response_code(200);
            die(json_encode(['skipped' => "Push to '$pushedBranch', watching '$deployBranch'"]));
        }
    }
}

// ── Fire deploy.sh in background ─────────────────────────────────────────────
$deployScript = dirname(__DIR__) . '/deploy.sh';
$logFile      = dirname(__DIR__) . '/storage/logs/deploy.log';

if (!file_exists($deployScript)) {
    http_response_code(500);
    die(json_encode(['error' => 'deploy.sh not found']));
}

// Run as background process — respond immediately, don't wait for completion
$cmd = sprintf(
    'bash %s >> %s 2>&1 &',
    escapeshellarg($deployScript),
    escapeshellarg($logFile)
);
exec($cmd);

http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'status'  => 'deployment triggered',
    'branch'  => $deployBranch,
    'time'    => date('Y-m-d H:i:s'),
    'log'     => 'storage/logs/deploy.log',
]);
