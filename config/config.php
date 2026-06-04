<?php
// ─── Application ───────────────────────────────────────────
define('APP_NAME',    'PathFinder AI');
define('APP_URL',     'http://localhost/pathfinder ai');
define('APP_VERSION', '1.0.0');

// ─── Database ──────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'pathfinder_ai');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ─── Internationalisation ──────────────────────────────────
define('DEFAULT_LANG',     'fr');
define('SUPPORTED_LANGS',  ['fr', 'en']);

// ─── Uploads ───────────────────────────────────────────────
define('UPLOAD_PATH', dirname(__DIR__) . '/public/uploads/');
define('UPLOAD_URL',  APP_URL . '/public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB

// ─── Pagination ────────────────────────────────────────────
define('ITEMS_PER_PAGE', 10);

// ─── Session sécurisée ─────────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode',  1);
ini_set('session.cookie_samesite',  'Lax');
ini_set('session.gc_maxlifetime',   7200);

// ─── Timezone ──────────────────────────────────────────────
date_default_timezone_set('Africa/Casablanca');

// ─── Erreurs (désactiver en production) ────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);  // mettre 0 en prod
ini_set('log_errors', 1);

// ─── Autoloader simple ─────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        dirname(__DIR__) . '/models/',
        dirname(__DIR__) . '/controllers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ─── Helper i18n ───────────────────────────────────────────
function __(string $key, array $replace = []): string
{
    global $translations;
    $text = $translations[$key] ?? $key;
    foreach ($replace as $k => $v) {
        $text = str_replace(':' . $k, htmlspecialchars((string)$v, ENT_QUOTES), $text);
    }
    return $text;
}

// ─── Helper sécurité sortie ────────────────────────────────
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ─── CSRF ──────────────────────────────────────────────────
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ─── Redirect ──────────────────────────────────────────────
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// ─── Flash messages ────────────────────────────────────────
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// ─── Auth helpers ──────────────────────────────────────────
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    if (class_exists('Database')) {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user']['id']]);
            if ($stmt->fetchColumn() == 0) {
                unset($_SESSION['user']);
                return false;
            }
        } catch (Exception $e) {
            // Keep session if DB is momentarily unreachable
        }
    }
    return true;
}

function hasRole(string ...$roles): bool
{
    $user = auth();
    return $user && in_array($user['role'], $roles);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('error', __('auth.login_required'));
        redirect(APP_URL . '/?ctrl=auth&action=login');
    }
}

function requireRole(string ...$roles): void
{
    requireLogin();
    if (!hasRole(...$roles)) {
        flash('error', __('auth.access_denied'));
        redirect(APP_URL . '/');
    }
}

// ─── requireAuth (any logged-in user) ──────────────────────
function requireAuth(): void
{
    if (!isLoggedIn()) {
        flash('error', __('auth.login_required'));
        redirect(APP_URL . '/?ctrl=auth&action=login');
    }
}

// ─── CSRF verify (throw + redirect on failure) ──────────────
function verify_csrf(): void
{
    if (!csrf_verify()) {
        flash('error', 'CSRF token invalide.');
        redirect(APP_URL . '/');
    }
}

// ─── Sanitize input ────────────────────────────────────────
function sanitize(mixed $value): string
{
    return trim(strip_tags((string)$value));
}
