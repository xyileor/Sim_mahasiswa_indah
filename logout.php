<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if (isLoggedIn()) {
    audit('LOGOUT', 'pengguna', (string)$_SESSION['user']['id_pengguna']);
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: index.html?logout=1');
exit;
