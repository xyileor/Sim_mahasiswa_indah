<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requirePermission('dashboard.view');
header('Content-Type: text/plain; charset=utf-8');

function lit($v): string { return '"' . addcslashes((string)$v, "\\\"\n\r") . '"'; }
function emit(string $uri, string $type, array $props): void {
    $parts = [];
    foreach ($props as $p => [$kind, $v]) {
        if ($v === null || $v === '') continue;
        $parts[] = "    ex:$p " . ($kind === 'u' ? "<$v>" : lit($v));
    }
    echo "<$uri> a ex:$type ;\n" . implode(" ;\n", $parts) . " .\n\n";
}

echo "@prefix ex: <http://example.org/vocab#> .\n\n";
foreach ($db->query('SELECT * FROM universitas') as $r)
    emit($r['uri'], 'Universitas', ['kode' => ['l', $r['kode_universitas']], 'nama' => ['l', $r['nama_universitas']], 'kota' => ['l', $r['kota']]]);
foreach ($db->query('SELECT f.*, u.uri AS u_uri FROM fakultas f JOIN universitas u ON u.id_universitas=f.id_universitas') as $r)
    emit($r['uri'], 'Fakultas', ['kode' => ['l', $r['kode_fakultas']], 'nama' => ['l', $r['nama_fakultas']], 'dekan' => ['l', $r['dekan']], 'bagianDari' => ['u', $r['u_uri']]]);
foreach ($db->query('SELECT p.*, f.uri AS f_uri FROM program_studi p JOIN fakultas f ON f.id_fakultas=p.id_fakultas') as $r)
    emit($r['uri'], 'ProgramStudi', ['kode' => ['l', $r['kode_prodi']], 'nama' => ['l', $r['nama_prodi']], 'jenjang' => ['l', $r['jenjang']], 'bagianDari' => ['u', $r['f_uri']]]);
if (hasPermission('mahasiswa.view'))
    foreach ($db->query('SELECT m.*, p.uri AS p_uri FROM mahasiswa m JOIN program_studi p ON p.id_prodi=m.id_prodi') as $r)
        emit($r['uri'], 'Mahasiswa', ['nim' => ['l', $r['nim']], 'nama' => ['l', $r['nama_lengkap']], 'angkatan' => ['l', $r['angkatan']], 'status' => ['l', $r['status_mahasiswa']], 'programStudi' => ['u', $r['p_uri']]]);
