<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
requireLogin();
$user = currentUser();
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> - Universitas Contoh</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <div>
                <strong>Universitas Contoh</strong>
                <small>Data & Web Semantik</small>
            </div>
        </div>

        <div class="role-badge">
            <i class="fa-solid fa-shield-halved"></i>
            <?= htmlspecialchars(ucfirst($user['nama_role'])) ?>
        </div>

        <nav class="side-nav">
            <div class="nav-label">MENU UTAMA</div>
            <?php if (hasPermission('dashboard.view')): ?>
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                </a>
            <?php endif; ?>

            <?php if (hasPermission('mahasiswa.view')): ?>
                <a href="mahasiswa.php" class="<?= basename($_SERVER["PHP_SELF"]) === "mahasiswa.php" ? "active" : "" ?>"><i class="fa-solid fa-user-graduate"></i><span>Mahasiswa</span></a>
            <?php endif; ?>

            <?php if (hasPermission('prodi.view')): ?>
                <a href="prodi.php" class="<?= basename($_SERVER["PHP_SELF"]) === "prodi.php" ? "active" : "" ?>"><i class="fa-solid fa-book-open"></i><span>Program Studi</span></a>
            <?php endif; ?>

            <?php if (hasPermission('fakultas.view')): ?>
                <a href="fakultas.php" class="<?= basename($_SERVER["PHP_SELF"]) === "fakultas.php" ? "active" : "" ?>"><i class="fa-solid fa-building-columns"></i><span>Fakultas</span></a>
            <?php endif; ?>

            <?php if (hasPermission('universitas.view')): ?>
                <a href="universitas.php" class="<?= basename($_SERVER["PHP_SELF"]) === "universitas.php" ? "active" : "" ?>"><i class="fa-solid fa-university"></i><span>Universitas</span></a>
            <?php endif; ?>

            <?php if (hasPermission('pengguna.view')): ?>
                <a href="pengguna.php" class="<?= basename($_SERVER["PHP_SELF"]) === "pengguna.php" ? "active" : "" ?>"><i class="fa-solid fa-users-gear"></i><span>Pengguna</span></a>
            <?php endif; ?>

            <?php if (hasPermission('audit.view')): ?>
                <a href="audit.php" class="<?= basename($_SERVER["PHP_SELF"]) === "audit.php" ? "active" : "" ?>"><i class="fa-solid fa-file-shield"></i><span>Audit Log</span></a>
            <?php endif; ?>

            <div class="nav-label">WEB SEMANTIK</div>
            <a href="rdf.php" target="_blank"><i class="fa-solid fa-share-nodes"></i><span>RDF (Turtle)</span></a>
            <a href="sparql.php" class="<?= basename($_SERVER["PHP_SELF"]) === "sparql.php" ? "active" : "" ?>"><i class="fa-solid fa-diagram-project"></i><span>SPARQL</span></a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <span>Universitas Contoh</span><i class="fa-solid fa-chevron-right"></i>
                <strong><?= htmlspecialchars($pageTitle) ?></strong>
            </div>
            <div class="topbar-user">
                <div class="avatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?></div>
                <div class="user-meta">
                    <strong><?= htmlspecialchars($user['nama_lengkap']) ?></strong>
                    <small><?= htmlspecialchars(ucfirst($user['nama_role'])) ?></small>
                </div>
            </div>
        </header>
        <section class="page-content">
