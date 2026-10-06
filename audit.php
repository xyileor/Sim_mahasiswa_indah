<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
requirePermission('audit.view');
$pageTitle = 'Audit Log';
$rows = $db->query("SELECT a.*, u.username FROM audit_log a LEFT JOIN pengguna u ON u.id_pengguna=a.id_pengguna ORDER BY a.id_audit DESC LIMIT 200")->fetch_all(MYSQLI_ASSOC);
require __DIR__ . '/partials/header.php';
?>
<div class="page-heading"><div><h1>Audit Log</h1><p>200 aktivitas terakhir.</p></div></div>
<div class="card table-wrap"><table><thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th><th>Tabel</th><th>ID</th><th>IP</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= htmlspecialchars($r['created_at']) ?></td><td><?= htmlspecialchars($r['username'] ?? '-') ?></td><td><span class="badge"><?= htmlspecialchars($r['aktivitas']) ?></span></td><td><?= htmlspecialchars($r['tabel_target'] ?? '-') ?></td><td><?= htmlspecialchars($r['id_target'] ?? '-') ?></td><td><?= htmlspecialchars($r['ip_address'] ?? '-') ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
