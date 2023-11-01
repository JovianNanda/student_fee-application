<?php
include_once __DIR__ . "/../bootstrap.php";
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=homeUrl()?>/assets/css/reset.css">
    <link rel="stylesheet" href="<?=homeUrl()?>/assets/css/app.css">
    <title><?= $title ?></title>
</head>

<body>
    <noscript>
        <div class="modal show">
            <div class="modal-content">
                <div class="modal-header">
                    <li class="ico ico-exclamation-triangle c-danger"></li>
                    <h1 class="c-danger">Javascript!</h1>
                    <span class="c-black">Tolong Aktifkan Javascript di browser anda!</span>
                </div>
            </div>
        </div>
    </noscript>
    <div class="container">
        <nav class="sidebar">
            <div class="sidebar-brand">
                <a href="<?=homeUrl()?>/uk">
                    <li class="ico ico-logo c-green"></li>
                    <h1 class="c-green">SPP</h1>
                </a>
            </div>
            <div class="sidebar-content">
                <?php if (isAdmin()): ?>
                <a href="<?=homeUrl()?>/dashboard/" class="sidebar-item <?php active("dashboard")?>">
                    <li class="ico ico-house"></li>
                    <span class="c-secondary">Dashboard</span>
                </a>
                <a href="<?=homeUrl()?>/pembayaran/" class="sidebar-item <?php active("pembayaran")?>">
                    <li class="ico ico-sack-dollar c-green"></li>
                    <span class="c-secondary">Pembayaran</span>
                </a>
                <a href="<?=homeUrl()?>/siswa/" class="sidebar-item <?php active("siswa")?>">
                    <li class="ico ico-users c-info"></li>
                    <span class="c-secondary">Siswa
                    </span>
                </a>
                <a href="<?=homeUrl()?>/kelas/" class="sidebar-item <?php active("kelas")?>">
                    <li class="ico ico-classroom c-success"></li>
                    <span class="c-secondary">Kelas</span>
                </a>
                <a href="<?=homeUrl()?>/spp/" class="sidebar-item <?php active("spp")?>">
                    <li class="ico ico-file-dollar c-red"></li>
                    <span class="c-secondary">SPP</span>
                </a>
                <a href="<?=homeUrl()?>/petugas/" class="sidebar-item <?php active("petugas")?>">
                    <li class="ico ico-user-secret c-sky"></li>
                    <span class="c-secondary">Petugas</span>
                </a>
                <a href="<?=homeUrl()?>/histori/" class="sidebar-item <?php active("histori")?>">
                    <li class="ico ico-history c-warning"></li>
                    <span class="c-secondary">Histori</span>
                </a>
                <a href="<?=homeUrl()?>/print/" class="sidebar-item <?php active("print")?>">
                    <li class="ico ico-print c-gray"></li>
                    <span class="c-secondary">Cetak</span>
                </a>
                <?php elseif (isPetugas()): ?>
                <a href="<?=homeUrl()?>/pembayaran/" class="sidebar-item <?php active("pembayaran")?>">
                    <li class="ico ico-sack-dollar c-green"></li>
                    <span class="c-secondary">Pembayaran</span>
                </a>
                <a href="<?=homeUrl()?>/histori/" class="sidebar-item <?php active("histori")?>">
                    <li class="ico ico-history c-warning"></li>
                    <span class="c-secondary">Histori</span>
                </a>
                <?php elseif (isSiswa()): ?>
                <a href="<?=homeUrl()?>/pembayaran/" class="sidebar-item <?php active("pembayaran")?>">
                    <li class="ico ico-sack-dollar c-green"></li>
                    <span class="c-secondary">Histori</span>
                </a>
                <?php endif;?>
            </div>
        </nav>
        <div class="content">
            <nav class="navbar">
                <div class="navbar-brand">

                </div>
                <div class="navbar-badge">
                    <div class="badge bg-green c-white" style="padding: 10px 15px;">
                        <?=ucfirst(getLevel());?>
                    </div>
                </div>
                <div class="navbar-user">
                    <div class="dropdown dropdown-right">
                        <button class="button button-transparent" style="display: flex; align-items:center; height: 50px;
                        " data-toggle="dropdown">
                            <li class="ico ico-user navbar-user-img c-green" style="margin-right: 20px"></li>
                            <span class="navbar-user-name"><?=ucfirst(getUName())?></span>
                            <li class="ico ico-angle-down c-secondary navbar-angle-down"></li>
                        </button>
                        <div class="dropdown-menu navbar-dropdown">
                            <a href="<?=homeUrl()?>/password.php" class="dropdown-item">
                                <li class="ico ico-gear c-green"></li> Password
                            </a>
                            <a class="dropdown-item" modal-target="modal-logout">
                                <li class=" ico ico-logout c-green"></li> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </nav>


            <div class="modal" modal-id="modal-logout">
                <div class="modal-content">
                    <div class="modal-header">
                        <li class="ico ico-exclamation-triangle c-danger"></li>
                        <h1>Konfirm Logout</h1>
                        <span>Konfirmasi Aksimu</span>
                    </div>
                    <div class="modal-body">
                        <a href="<?=homeUrl()?>/logout.php"><button class="button bg-danger">Logout</button></a>
                        <span>atau</span>
                        <button class="button button-outline" modal-dismiss>Batal</button>
                    </div>
                </div>
            </div>
            <main>