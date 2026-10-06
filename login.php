<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: index.html?error=empty');
    exit;
}

$stmt = $db->prepare(
    'SELECT p.id_pengguna, p.id_role, p.username, p.nama_lengkap, p.email,
            p.password_hash, p.id_prodi, p.aktif, r.nama_role, r.keterangan
     FROM pengguna p
     INNER JOIN roles r ON r.id_role = p.id_role
     WHERE p.username = ?
     LIMIT 1'
);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || (int)$user['aktif'] !== 1 || !password_verify($password, $user['password_hash'])) {
    // Jangan membedakan "user tidak ada", "password salah", atau "user nonaktif".
    audit('LOGIN_GAGAL', 'pengguna', isset($user['id_pengguna']) ? (string)$user['id_pengguna'] : null);
    header('Location: index.html?error=login');
    exit;
}

session_regenerate_id(true);

$_SESSION['user'] = [
    'id_pengguna' => (int)$user['id_pengguna'],
    'id_role' => (int)$user['id_role'],
    'username' => $user['username'],
    'nama_lengkap' => $user['nama_lengkap'],
    'email' => $user['email'],
    'id_prodi' => $user['id_prodi'] !== null ? (int)$user['id_prodi'] : null,
    'nama_role' => $user['nama_role'],
    'role_keterangan' => $user['keterangan'],
];

$update = $db->prepare('UPDATE pengguna SET last_login = NOW() WHERE id_pengguna = ?');
$update->bind_param('i', $_SESSION['user']['id_pengguna']);
$update->execute();
$update->close();

audit('LOGIN_BERHASIL', 'pengguna', (string)$user['id_pengguna']);

header('Location: dashboard.php');
exit;
