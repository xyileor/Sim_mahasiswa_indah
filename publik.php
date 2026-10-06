<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';   // halaman publik: hanya data agregat, tanpa data pribadi mahasiswa
$q = trim($_GET['q'] ?? '');
$sql = "SELECT ps.kode_prodi, ps.nama_prodi, ps.jenjang, ps.akreditasi, f.nama_fakultas, COUNT(m.id_mahasiswa) AS jumlah
        FROM program_studi ps JOIN fakultas f ON f.id_fakultas = ps.id_fakultas
        LEFT JOIN mahasiswa m ON m.id_prodi = ps.id_prodi AND m.status_mahasiswa = 'Aktif'
        WHERE ps.aktif = 1 AND (ps.nama_prodi LIKE ? OR f.nama_fakultas LIKE ?)
        GROUP BY ps.id_prodi, ps.kode_prodi, ps.nama_prodi, ps.jenjang, ps.akreditasi, f.nama_fakultas
        ORDER BY jumlah DESC, ps.nama_prodi";
$s = $db->prepare($sql); $like = "%$q%"; $s->bind_param('ss', $like, $like); $s->execute();
$rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);
$total = array_sum(array_column($rows, 'jumlah'));
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?><!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Mahasiswa per Program Studi</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/dashboard.css"></head>
<body><div style="max-width:1100px;margin:auto;padding:28px">
<div class="toolbar"><a class="btn btn-light" href="index.html">← Beranda</a><a class="btn" href="index.html?login=1">Login</a></div>
<div class="page-heading"><div><h1>Data Mahasiswa per Program Studi</h1><p>Ringkasan jumlah mahasiswa aktif (data agregat, tanpa data pribadi).</p></div></div>
<div class="card">
<form class="toolbar" method="get"><input name="q" placeholder="Cari prodi atau fakultas..." value="<?= h($q) ?>"><button class="btn btn-light">Cari</button></form>
<div class="table-wrap"><table><thead><tr><th>Kode</th><th>Program Studi</th><th>Jenjang</th><th>Akreditasi</th><th>Fakultas</th><th>Mahasiswa Aktif</th></tr></thead><tbody>
<?php if (!$rows): ?><tr><td colspan="6" class="empty">Data tidak ditemukan.</td></tr><?php endif; ?>
<?php foreach ($rows as $r): ?><tr><td><?= h($r['kode_prodi']) ?></td><td><?= h($r['nama_prodi']) ?></td><td><?= h($r['jenjang']) ?></td><td><?= h($r['akreditasi'] ?? '-') ?></td><td><?= h($r['nama_fakultas']) ?></td><td><b><?= (int)$r['jumlah'] ?></b></td></tr><?php endforeach; ?>
</tbody></table></div><p class="empty" style="padding:10px 0 0">Total mahasiswa aktif pada hasil ini: <b><?= (int)$total ?></b></p></div></div></body></html>
