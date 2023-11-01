<?php
$title = "SPP | Insert SPP";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
if (isPost()) {
    insertSpp($_POST);
}
alert();
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Tambah Data SPP</h1>
<div class="header-container">
    <div></div>
    <a class="button bg-red" href="../spp/"
        style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
        <li class="ico ico-arrow-left c-white"></li>Kembali
    </a>
</div>
<section class="spp active">
    <form action="" method="post" id="kelasForm">
        <div class="wrapper">
            <div class="input-group">
                <div class="input-layout">
                    <label for="harga">Masukkan Harga SPP</label>
                    <input id="harga" type="number" class="input" name="harga" placeholder="Harga SPP">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jenisKelas">Masukkan Jenis Kelas</label>
                    <select class="input" name="jenisKelas" id="jenisKelas">
                        <option value="" selected disabled>Pilih Jenis Kelas</option>
                        <?php foreach (JENIS_KELAS as $jk): ?>
                        <option value="<?=$jk?>"><?=ucfirst($jk)?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="angkatan">Masukkan Tahun Angkatan Kelas</label>
                    <input type="number" name="angkatan" id="angkatan" class="input" placeholder="Tahun Angkatan Kelas">
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div style="width: 57rem;"></div>
            <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Simpan
                SPP</button>
        </div>
    </form>
    <script src="/uk/assets/js/functions.js"></script>
</section>
<?php
include '../templates/footer.php';
?>