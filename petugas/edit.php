<?php
$title = "SPP | Edit Petugas";
include '../templates/header.php';

if (isGuest() or !isAdmin()) redirect("");
if (isPost()) editPetugas($_POST);

$idPetugas = sanitize($_GET['id']);
$petugas = getPetugasById($idPetugas);
if (!$petugas)
{
    setAlert("Petugas Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-triangle");
    redirect("petugas");
    return false;
}
$lvlUser = array_diff(LEVEL_USER, [$petugas['level_user']]);
alert();
?>
    <link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
    <h1 class="page-title">Edit Data Petugas</h1>
    <div class="header-container">
        <div></div>
        <a class="button bg-red" href="../petugas/"
           style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
            <li class="ico ico-arrow-left c-white"></li>Kembali
        </a>
    </div>
    <section class="spp active">
        <form action="" method="post" id="kelasForm">
            <input type="hidden" name="idpetugas" value="<?= $idPetugas ?>">
            <div class="wrapper">
                <div class="input-group">
                    <div class="input-layout">
                        <label for="username">Username</label>
                        <input id="username" type="text" class="input" name="username" placeholder="Masukkan Username" value="<?= $petugas['username'] ?>">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="password">Password Baru</label>
                        <div class="input-icon" id="password">
                            <input id="password" type="password" class="input" name="password"
                                   placeholder="Masukkan  Password Petugas">
                            <button type="button" class="button bg-transparent icon no-border" data-toggle="password"
                                    style="display: block;">
                                <li class="ico ico-eye c-black"></li>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="level_User">Level User</label>
                        <select class="input" name="level_User" id="level_User">
                            <option value="" disabled>Pilih Level Petugas</option>
                            <option value="<?= $petugas['level_user'] ?>" selected><?= ucfirst($petugas['level_user']) ?></option>
                            <?php foreach ($lvlUser as $lvl): ?>
                                <option value="<?=$lvl?>"><?=ucfirst($lvl)?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="wrapper-grid">
                <div style="width: 55rem;"></div>
                <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Simpan
                    Petugas</button>
            </div>
        </form>
    </section>
<?php
include '../templates/footer.php';
?>