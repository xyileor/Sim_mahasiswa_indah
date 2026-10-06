<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requirePermission('dashboard.view');

$pageTitle = 'Dashboard';
require __DIR__ . '/partials/header.php';

$stats = $db->query("SELECT
    (SELECT COUNT(*) FROM mahasiswa WHERE status_mahasiswa='Aktif') AS total_mahasiswa,
    (SELECT COUNT(*) FROM program_studi WHERE aktif=1) AS total_program_studi,
    (SELECT COUNT(*) FROM fakultas) AS total_fakultas,
    (SELECT COUNT(*) FROM mahasiswa WHERE status_mahasiswa='Aktif' AND YEAR(created_at)=YEAR(CURDATE())) AS mahasiswa_data_tahun_ini")->fetch_assoc();

$prodi = $db->query(
    "SELECT ps.kode_prodi, ps.nama_prodi, ps.jenjang, COUNT(m.id_mahasiswa) AS jumlah_mahasiswa
     FROM program_studi ps
     LEFT JOIN mahasiswa m ON m.id_prodi = ps.id_prodi AND m.status_mahasiswa = 'Aktif'
     GROUP BY ps.id_prodi, ps.kode_prodi, ps.nama_prodi, ps.jenjang
     ORDER BY jumlah_mahasiswa DESC, ps.nama_prodi
     LIMIT 8"
);
?>
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Selamat datang, <?= htmlspecialchars($user['nama_lengkap']) ?>. Hak akses aktif: <strong><?= htmlspecialchars(ucfirst($user['nama_role'])) ?></strong>.</p>
    </div>
</div>

<div class="cards">
    <div class="card stat-card"><div><div class="stat-label">Mahasiswa Aktif</div><div class="stat-value"><?= number_format((int)$stats['total_mahasiswa']) ?></div></div><div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div></div>
    <div class="card stat-card"><div><div class="stat-label">Program Studi</div><div class="stat-value"><?= number_format((int)$stats['total_program_studi']) ?></div></div><div class="stat-icon"><i class="fa-solid fa-book-open"></i></div></div>
    <div class="card stat-card"><div><div class="stat-label">Fakultas</div><div class="stat-value"><?= number_format((int)$stats['total_fakultas']) ?></div></div><div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div></div>
    <div class="card stat-card"><div><div class="stat-label">Data Mahasiswa Tahun Ini</div><div class="stat-value"><?= number_format((int)$stats['mahasiswa_data_tahun_ini']) ?></div></div><div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div></div>
</div>

<div class="dashboard-grid">
    <div class="card">
        <h3>Mahasiswa Aktif per Program Studi</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kode</th><th>Program Studi</th><th>Jenjang</th><th>Jumlah</th></tr></thead>
                <tbody>
                <?php while ($row = $prodi->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['kode_prodi']) ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_prodi']) ?></td>
                        <td><?= htmlspecialchars($row['jenjang']) ?></td>
                        <td><strong><?= number_format((int)$row['jumlah_mahasiswa']) ?></strong></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>RBAC — Hak Akses Anda</h3>
        <p style="font-size:12px;color:#64748b;line-height:1.7">
            Sistem menentukan menu dan hak akses berdasarkan <strong>role</strong> yang
            terhubung ke tabel <strong>pengguna</strong>. Pemeriksaan permission dilakukan
            kembali di server pada setiap halaman.
        </p>
        <div class="badge"><i class="fa-solid fa-shield-halved"></i> <?= htmlspecialchars(ucfirst($user['nama_role'])) ?> aktif</div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
