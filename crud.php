<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrfToken(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function flash(string $t, string $m): void { $_SESSION['flash'] = [$t, $m]; }

/**
 * Mesin CRUD generik. Konfigurasi tiap modul ada di file modul (mahasiswa.php, dst).
 * fields: nama_kolom => [label, tipe, wajib, opsi]  tipe: text|email|number|date|textarea|select|password|check
 */
function crudRun(array $c): void {
    global $db;
    $view = $c['perm'] . '.view'; $manage = $c['perm'] . '.manage';
    requirePermission($view);
    $can = hasPermission($manage); $pk = $c['pk']; $self = basename($_SERVER['PHP_SELF']);

    // opsi select
    foreach ($c['fields'] as $k => &$f) {
        if (($f[1] ?? '') === 'select' && is_string($f[3] ?? null)) {
            $f[3] = array_column($db->query($f[3])->fetch_all(MYSQLI_NUM), 1, 0);
        }
    } unset($f);

    // ---------- POST: simpan / hapus ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        requirePermission($manage);
        if (!hash_equals(csrfToken(), $_POST['csrf'] ?? '')) { http_response_code(419); exit('Token CSRF tidak valid.'); }
        $id = (int)($_POST['id'] ?? 0);
        $old = null;
        if ($id) { $s = $db->prepare("SELECT * FROM {$c['table']} WHERE $pk=?"); $s->bind_param('i', $id); $s->execute(); $old = $s->get_result()->fetch_assoc(); }
        try {
            if (($_POST['a'] ?? '') === 'delete') {
                if (!empty($c['protect_self']) && $id === (int)currentUser()['id_pengguna']) throw new RuntimeException('Anda tidak dapat menghapus akun sendiri.');
                $s = $db->prepare("DELETE FROM {$c['table']} WHERE $pk=?"); $s->bind_param('i', $id); $s->execute();
                audit('DELETE', $c['table'], (string)$id, json_encode($old), null);
                flash('ok', 'Data berhasil dihapus.');
            } else {
                $vals = [];
                foreach ($c['fields'] as $k => $f) {
                    $v = trim((string)($_POST[$k] ?? ''));
                    if ($f[1] === 'check') $v = isset($_POST[$k]) ? '1' : '0';
                    if ($f[1] === 'password') { if ($v === '') { if (!$id) throw new RuntimeException('Password wajib diisi.'); continue; } $v = password_hash($v, PASSWORD_DEFAULT); }
                    elseif (!empty($f[2]) && $v === '') throw new RuntimeException($f[0] . ' wajib diisi.');
                    if ($f[1] === 'select' && $v !== '' && !isset($f[3][$v])) throw new RuntimeException($f[0] . ' tidak valid.');
                    $vals[$k] = $v === '' ? null : $v;
                }
                if (!empty($c['uri'])) { [$pre, $from] = $c['uri']; $vals['uri'] = $pre . strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', (string)$vals[$from]), '-')); }
                $cols = array_keys($vals); $types = str_repeat('s', count($vals)); $params = array_values($vals);
                if ($id) {
                    $s = $db->prepare("UPDATE {$c['table']} SET " . implode(',', array_map(fn($x) => "$x=?", $cols)) . " WHERE $pk=?");
                    $args = [...$params, $id]; $s->bind_param($types . 'i', ...$args);
                } else {
                    $s = $db->prepare("INSERT INTO {$c['table']} (" . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')');
                    $s->bind_param($types, ...$params);
                }
                $s->execute();
                $newId = $id ?: $db->insert_id;
                unset($vals['password_hash']);
                audit($id ? 'UPDATE' : 'CREATE', $c['table'], (string)$newId, $old ? json_encode($old) : null, json_encode($vals));
                flash('ok', $id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.');
            }
        } catch (mysqli_sql_exception $ex) {
            $m = $ex->getCode() === 1062 ? 'Data duplikat: nilai unik (mis. kode/NIM/username) sudah dipakai.'
               : ($ex->getCode() === 1451 ? 'Data tidak bisa dihapus karena masih dipakai data lain.' : 'Kesalahan database.');
            flash('err', $m);
        } catch (RuntimeException $ex) { flash('err', $ex->getMessage()); }
        header("Location: $self"); exit;
    }

    // ---------- GET ----------
    $a = $_GET['a'] ?? 'list'; $row = [];
    if ($a === 'edit') {
        $s = $db->prepare("SELECT * FROM {$c['table']} WHERE $pk=?"); $id = (int)($_GET['id'] ?? 0);
        $s->bind_param('i', $id); $s->execute(); $row = $s->get_result()->fetch_assoc() ?: [];
        if (!$row) { flash('err', 'Data tidak ditemukan.'); header("Location: $self"); exit; }
    }
    if (in_array($a, ['new', 'edit'], true)) requirePermission($manage);

    $pageTitle = $c['title'];
    require __DIR__ . '/partials/header.php';
    echo '<div class="page-heading"><div><h1>' . e($c['title']) . '</h1><p>' . e($c['desc'] ?? '') . '</p></div>';
    if ($can && $a === 'list') echo '<a class="btn" href="?a=new"><i class="fa-solid fa-plus"></i> Tambah</a>';
    echo '</div>';
    if (!empty($_SESSION['flash'])) { [$t, $m] = $_SESSION['flash']; unset($_SESSION['flash']); echo '<div class="alert ' . $t . '">' . e($m) . '</div>'; }

    if (in_array($a, ['new', 'edit'], true)) {            // ----- FORM -----
        echo '<div class="card"><form method="post" class="form-grid"><input type="hidden" name="csrf" value="' . csrfToken() . '">';
        if ($a === 'edit') echo '<input type="hidden" name="id" value="' . (int)$row[$pk] . '">';
        foreach ($c['fields'] as $k => $f) {
            [$lb, $tp] = $f; $req = !empty($f[2]) && !($tp === 'password' && $a === 'edit'); $val = $row[$k] ?? ($f[4] ?? '');
            echo '<div class="' . ($tp === 'textarea' ? 'full' : '') . '"><label>' . e($lb) . ($req ? ' <span class="req">*</span>' : '') . '</label>';
            if ($tp === 'select') {
                echo "<select name=\"$k\">" . (empty($f[2]) ? '<option value="">-</option>' : '');
                foreach ($f[3] as $ov => $ol) echo '<option value="' . e($ov) . '"' . ((string)$val === (string)$ov ? ' selected' : '') . '>' . e($ol) . '</option>';
                echo '</select>';
            } elseif ($tp === 'textarea') echo "<textarea name=\"$k\" rows=\"3\">" . e($val) . '</textarea>';
            elseif ($tp === 'check') echo "<input type=\"checkbox\" name=\"$k\" style=\"width:auto\"" . ($val === '' || $val ? ' checked' : '') . '>';
            else echo "<input type=\"$tp\" name=\"$k\" value=\"" . ($tp === 'password' ? '' : e($val)) . '"' . ($req ? ' required' : '') . ($tp === 'password' && $a === 'edit' ? ' placeholder="Kosongkan jika tidak diubah"' : '') . '>';
            echo '</div>';
        }
        echo '<div class="full"><button class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button> <a class="btn btn-light" href="' . $self . '">Batal</a></div></form></div>';
    } else {                                               // ----- DAFTAR -----
        $q = trim($_GET['q'] ?? ''); $page = max(1, (int)($_GET['p'] ?? 1)); $per = 15;
        $where = ''; $p = [];
        if ($q !== '') { $w = []; foreach ($c['search'] as $col) { $w[] = "$col LIKE ?"; $p[] = "%$q%"; } $where = ' WHERE ' . implode(' OR ', $w); }
        $cs = $db->prepare("SELECT COUNT(*) FROM ({$c['sql']}$where) x"); if ($p) $cs->bind_param(str_repeat('s', count($p)), ...$p);
        $cs->execute(); $total = (int)$cs->get_result()->fetch_row()[0]; $off = ($page - 1) * $per;
        $s = $db->prepare("{$c['sql']}$where ORDER BY {$c['order']} LIMIT $per OFFSET $off"); if ($p) $s->bind_param(str_repeat('s', count($p)), ...$p);
        $s->execute(); $rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);

        echo '<div class="card"><form class="toolbar" method="get"><input name="q" placeholder="Cari data..." value="' . e($q) . '"><button class="btn btn-light"><i class="fa-solid fa-magnifying-glass"></i> Cari</button></form>';
        echo '<div class="table-wrap"><table><thead><tr><th>#</th>';
        foreach ($c['cols'] as $lb) echo '<th>' . e($lb) . '</th>';
        echo $can ? '<th>Aksi</th>' : '', '</tr></thead><tbody>';
        if (!$rows) echo '<tr><td colspan="99" class="empty">Belum ada data.</td></tr>';
        foreach ($rows as $i => $r) {
            echo '<tr><td>' . ($off + $i + 1) . '</td>';
            foreach ($c['cols'] as $k => $lb) echo '<td>' . e($r[$k] ?? '-') . '</td>';
            if ($can) echo '<td class="act"><a class="btn btn-light btn-sm" href="?a=edit&id=' . (int)$r[$pk] . '"><i class="fa-solid fa-pen"></i></a> '
                . '<form class="actions-form" method="post" onsubmit="return confirm(\'Hapus data ini?\')"><input type="hidden" name="csrf" value="' . csrfToken() . '"><input type="hidden" name="a" value="delete"><input type="hidden" name="id" value="' . (int)$r[$pk] . '"><button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button></form></td>';
            echo '</tr>';
        }
        echo '</tbody></table></div><div class="pager">';
        for ($i = 1; $i <= max(1, (int)ceil($total / $per)); $i++) echo '<a class="btn ' . ($i === $page ? '' : 'btn-light') . ' btn-sm" href="?p=' . $i . '&q=' . urlencode($q) . '">' . $i . '</a>';
        echo '<span class="empty" style="padding:6px">Total: ' . $total . ' data</span></div></div>';
    }
    require __DIR__ . '/partials/footer.php';
}
