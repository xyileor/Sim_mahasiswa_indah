<?php
declare(strict_types=1);
require_once __DIR__ . '/crud.php';   // memuat auth + fungsi e()
requirePermission('dashboard.view');

// Kueri contoh: teks SPARQL ditampilkan, dijalankan setara via SQL pada basis data.
$P = 'PREFIX ex: <http://example.org/vocab#>' . "\n";
$Q = [
 'prodi' => ['Semua program studi', false, $P . "SELECT ?kode ?nama ?jenjang\nWHERE {\n  ?p a ex:ProgramStudi ;\n     ex:kode ?kode ; ex:nama ?nama ; ex:jenjang ?jenjang .\n}\nORDER BY ?nama",
    "SELECT kode_prodi AS kode, nama_prodi AS nama, jenjang FROM program_studi ORDER BY nama_prodi"],
 'fakultas' => ['Fakultas dan dekannya', false, $P . "SELECT ?fakultas ?dekan\nWHERE {\n  ?f a ex:Fakultas ;\n     ex:nama ?fakultas ; ex:dekan ?dekan .\n}",
    "SELECT nama_fakultas AS fakultas, dekan FROM fakultas WHERE dekan IS NOT NULL ORDER BY nama_fakultas"],
 'jumlah' => ['Jumlah prodi per fakultas', false, $P . "SELECT ?fakultas (COUNT(?p) AS ?jumlah)\nWHERE {\n  ?p a ex:ProgramStudi ; ex:bagianDari ?f .\n  ?f ex:nama ?fakultas .\n}\nGROUP BY ?fakultas",
    "SELECT f.nama_fakultas AS fakultas, COUNT(p.id_prodi) AS jumlah FROM fakultas f LEFT JOIN program_studi p ON p.id_fakultas=f.id_fakultas GROUP BY f.id_fakultas, f.nama_fakultas ORDER BY jumlah DESC"],
 'mhs' => ['Mahasiswa aktif per prodi', 'mahasiswa.view', $P . "SELECT ?prodi (COUNT(?m) AS ?jumlah)\nWHERE {\n  ?m a ex:Mahasiswa ; ex:status \"Aktif\" ; ex:programStudi ?p .\n  ?p ex:nama ?prodi .\n}\nGROUP BY ?prodi",
    "SELECT p.nama_prodi AS prodi, COUNT(m.id_mahasiswa) AS jumlah FROM program_studi p JOIN mahasiswa m ON m.id_prodi=p.id_prodi AND m.status_mahasiswa='Aktif' GROUP BY p.id_prodi, p.nama_prodi ORDER BY jumlah DESC"],
 'daftarmhs' => ['Daftar mahasiswa', 'mahasiswa.view', $P . "SELECT ?nim ?nama ?angkatan\nWHERE {\n  ?m a ex:Mahasiswa ;\n     ex:nim ?nim ; ex:nama ?nama ; ex:angkatan ?angkatan .\n}\nORDER BY ?nim",
    "SELECT nim, nama_lengkap AS nama, angkatan FROM mahasiswa ORDER BY nim LIMIT 100"],
];
$avail = array_filter($Q, fn($x) => !$x[1] || hasPermission($x[1]));
$k = $_GET['q'] ?? array_key_first($avail);
if (!isset($avail[$k])) $k = array_key_first($avail);
[$label, , $text, $sql] = $avail[$k];
$res = $db->query($sql); $rows = $res->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'SPARQL';
require __DIR__ . '/partials/header.php';
?>
<div class="page-heading"><div><h1>SPARQL Explorer</h1><p>Pilih kueri contoh, lihat teks SPARQL-nya, dan hasilnya dari data universitas. <a href="rdf.php" target="_blank"><b>Lihat data RDF (Turtle)</b></a></p></div></div>
<div class="card">
<form method="get" class="toolbar"><select name="q" onchange="this.form.submit()" style="border:1px solid var(--border);border-radius:10px;padding:9px 12px;min-width:260px">
<?php foreach ($avail as $key => $x): ?><option value="<?= e($key) ?>"<?= $key === $k ? ' selected' : '' ?>><?= e($x[0]) ?></option><?php endforeach; ?>
</select><button class="btn"><i class="fa-solid fa-play"></i> Jalankan</button></form>
<pre style="background:rgba(131,24,67,.9);color:#fce7f3;padding:16px;border-radius:12px;overflow-x:auto;font-size:12px"><?= e($text) ?></pre>
<div class="table-wrap"><table><thead><tr><?php foreach ($rows ? array_keys($rows[0]) : [] as $c): ?><th><?= e($c) ?></th><?php endforeach; ?></tr></thead><tbody>
<?php if (!$rows): ?><tr><td class="empty">Tidak ada hasil.</td></tr><?php endif; ?>
<?php foreach ($rows as $r): ?><tr><?php foreach ($r as $v): ?><td><?= e($v ?? '-') ?></td><?php endforeach; ?></tr><?php endforeach; ?>
</tbody></table></div>
<p class="empty" style="padding:10px 0 0"><?= count($rows) ?> hasil. Catatan: ini kueri contoh yang dipetakan ke basis data relasional, bukan triple store SPARQL penuh.</p></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
