<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/*
 * RBAC sederhana berbasis tabel roles + pengguna.
 * Permission didefinisikan di aplikasi, sedangkan role user diambil dari DB.
 */
const RBAC_RULES = [
    'admin' => [
        'dashboard.view',
        'universitas.view', 'universitas.manage',
        'fakultas.view', 'fakultas.manage',
        'prodi.view', 'prodi.manage',
        'mahasiswa.view', 'mahasiswa.manage',
        'pengguna.view', 'pengguna.manage',
        'audit.view'
    ],
    'operator' => [
        'dashboard.view',
        'universitas.view',
        'fakultas.view',
        'prodi.view',
        'mahasiswa.view', 'mahasiswa.manage',
        'audit.view'
    ],
    'dosen' => [
        'dashboard.view',
        'prodi.view',
        'mahasiswa.view'
    ],
    'mahasiswa' => [
        'dashboard.view',
        'prodi.view',
        'mahasiswa.view'
    ],
    'publik' => [
        'dashboard.view',
        'prodi.view'
    ],
];

function isLoggedIn(): bool {
    return isset($_SESSION['user']['id_pengguna']);
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function hasPermission(string $permission): bool {
    $role = $_SESSION['user']['nama_role'] ?? '';
    return in_array($permission, RBAC_RULES[$role] ?? [], true);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: index.html?login=1');
        exit;
    }
}

function requirePermission(string $permission): void {
    requireLogin();

    if (!hasPermission($permission)) {
        http_response_code(403);
        exit('403 Forbidden — Anda tidak memiliki hak akses ke halaman ini.');
    }
}

function audit(string $activity, ?string $table = null, ?string $targetId = null, ?string $old = null, ?string $new = null): void {
    global $db;

    $idUser = $_SESSION['user']['id_pengguna'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $db->prepare(
        'INSERT INTO audit_log
        (id_pengguna, aktivitas, tabel_target, id_target, data_lama, data_baru, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('isssssss', $idUser, $activity, $table, $targetId, $old, $new, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}
