<?php
// File diagnostik sementara. HAPUS dari server setelah selesai dipakai!
error_reporting(E_ALL); ini_set('display_errors', '1');
header('Content-Type: text/html; charset=utf-8');
function row($ok, $label, $info = '') { echo '<tr><td>' . ($ok ? '✅' : '❌') . '</td><td>' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($info) . '</td></tr>'; }
echo '<h2>Cek Instalasi</h2><table border="1" cellpadding="6" style="border-collapse:collapse;font-family:sans-serif;font-size:14px">';
row(PHP_VERSION_ID >= 80000, 'Versi PHP (butuh 8.0+)', PHP_VERSION);
row(extension_loaded('mysqli'), 'Ekstensi mysqli');
row(function_exists('mysqli_stmt_get_result'), 'mysqlnd (get_result)');
foreach (['db.php','auth.php','login.php','crud.php','dashboard.php','partials/header.php','partials/footer.php','assets/css/dashboard.css'] as $f)
    row(is_file(__DIR__ . '/' . $f), 'File ' . $f);
$src = is_file(__DIR__ . '/login.php') ? file_get_contents(__DIR__ . '/login.php') : '';
row(strpos($src, 'isset($user[\'id_pengguna\'])') !== false, 'login.php versi terbaru');
$h = is_file(__DIR__ . '/partials/header.php') ? file_get_contents(__DIR__ . '/partials/header.php') : '';
row(strpos($h, "/../auth.php") !== false, 'partials/header.php versi terbaru');
$dbsrc = is_file(__DIR__ . '/db.php') ? file_get_contents(__DIR__ . '/db.php') : '';
preg_match("/DB_HOST = '([^']*)'/", $dbsrc, $mh); preg_match("/DB_USER = '([^']*)'/", $dbsrc, $mu); preg_match("/DB_NAME = '([^']*)'/", $dbsrc, $mn); preg_match("/DB_PASS = '([^']*)'/", $dbsrc, $mp);
row(!in_array($mh[1] ?? '', ['', '127.0.0.1', 'localhost'], true), 'DB_HOST bukan localhost', $mh[1] ?? '?');
row(($mp[1] ?? '') !== '', 'DB_PASS terisi');
if (extension_loaded('mysqli')) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $c = @new mysqli($mh[1] ?? '', $mu[1] ?? '', $mp[1] ?? '', $mn[1] ?? '');
    if ($c->connect_errno) { row(false, 'Koneksi database', $c->connect_error); }
    else {
        row(true, 'Koneksi database', $mn[1]);
        foreach (['roles','pengguna','universitas','fakultas','program_studi','mahasiswa','audit_log'] as $t) {
            $r = $c->query("SELECT COUNT(*) FROM `$t`");
            row((bool)$r, 'Tabel ' . $t, $r ? $r->fetch_row()[0] . ' baris' : $c->error);
        }
        $r = $c->query("SELECT username, password_hash, aktif FROM pengguna WHERE username='admin'");
        $u = $r ? $r->fetch_assoc() : null;
        row((bool)$u, 'Akun admin ada');
        if ($u) { row(password_verify('password', $u['password_hash']), 'Password admin = "password" cocok'); row((int)$u['aktif'] === 1, 'Akun admin aktif'); }
    }
}
echo '</table><p>❌ pertama dari atas adalah penyebab utama. Hapus file <b>cek.php</b> setelah selesai.</p>';
