<?php
$title = "SPP | Edit SPP";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
if (isset($_GET['id']) and $_GET['id']) {
    $idSpp = $_GET['id'];
    $spp = query("SELECT * FROM spp WHERE id_spp = $idSpp");

    if ($spp) {
        $newJenisKelas = array_diff(JENIS_KELAS, [$spp['jenis_kelas']]);
    }

    if (!$spp) {
        setAlert("SPP Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("spp");
        return false;
    }
} else {
    setAlert("SPP Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
    redirect("spp");
    return false;
}
if (isPost()) {
    editSpp($_POST);
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Edit Data SPP</h1>
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
            <input type="hidden" name="id_spp" value="<?=$_GET['id']?>">
            <div class="input-group">
                <div class="input-layout">
                    <label for="harga">Masukkan Harga SPP</label>
                    <input id="harga" type="number" class="input" name="harga" placeholder="Harga SPP"
                        value="<?=$spp['harga_spp']?>">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jenisKelas">Masukkan Jenis Kelas</label>
                    <select class="input" name="jenisKelas" id="jenisKelas">
                        <option value="" disabled>Pilih Jenis Kelas</option>
                        <option value="<?=$spp['jenis_kelas']?>" selected><?=ucfirst($spp['jenis_kelas'])?></option>
                        <?php foreach ($newJenisKelas as $jk): ?>
                        <option value="<?=$jk?>"><?=ucfirst($jk)?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="angkatan">Masukkan Tahun Angkatan Kelas</label>
                    <input type="number" name="angkatan" id="angkatan" class="input" placeholder="Tahun Angkatan Kelas"
                        value="<?=$spp['tahun_angkatan']?>">
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div style="width: 58rem;"></div>
            <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Ubah
                Spp</button>
        </div>
    </form>
    <script src="/uk/assets/js/functions.js"></script>
</section>
<?php
include '../templates/footer.php';
?>