<?php
$title = "SPP | Edit Kelas";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
$spp = getAllspp();

if ($_GET['id'] and isset($_GET['id'])) {
    $id = $_GET['id'];
    $kelas = getKelasById($id);

    if ($kelas) {
        $newJurusan = array_diff(JURUSAN, [$kelas['jurusan']]);
        $newJenisKelas = array_diff(JENIS_KELAS, [$kelas['jenis_kelas']]);
        $idSpp = $kelas['id_spp'];
        $spp = query("SELECT * FROM spp WHERE id_spp != $idSpp", false);
        $selectedSpp = query("SELECT * FROM spp WHERE id_spp = $idSpp");
    }
    if (!$kelas) {
        setAlert("Kelas Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("kelas");
        return false;
    }
} else {
    setAlert("Kelas Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
    redirect("kelas");
    return false;
}
if (isPost()) {
    editKelas($_POST);
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Edit Kelas</h1>
<div class="header-container">
    <div></div>
    <a class="button bg-red" href="../kelas/"
        style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
        <li class="ico ico-arrow-left c-white"></li>Kembali
    </a>
</div>
<section class="kelas active">
    <form action="" method="post" id="kelasForm">
        <div class="wrapper-grid">
            <input type="hidden" name="idkelas" value="<?=$kelas['id_kelas']?>">
            <div class="input-group">
                <div class="input-layout">
                    <label for="kelas">Masukkan Nama Kelas</label>
                    <input id="kelas" type="text" class="input" name="kelas" value="<?=$kelas['kelas']?>"
                        placeholder="Nama Kelas ex: RPL 2">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jurusan">Masukkan Jurusan Kelas</label>
                    <select class="input" name="jurusan" id="jurusan">
                        <option value="" disabled>Pilih Jurusan Kelas</option>
                        <option value="<?=$kelas['jurusan']?> " selected><?=$kelas['jurusan']?></option>
                        <?php foreach ($newJurusan as $jurusan): ?>
                        <option value="<?=$jurusan?>"><?=$jurusan?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jenisKelas">Masukkan Jenis Kelas</label>
                    <select class="input" name="jenisKelas" id="jenisKelas">
                        <option value="" disabled>Pilih Jenis Kelas</option>
                        <option value="<?=$kelas['jenis_kelas']?>" selected><?=ucfirst($kelas['jenis_kelas'])?></option>
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
                        value="<?=$kelas['angkatan']?>">
                </div>
            </div>
        </div>
        <div class="wrapper">
            <div class="input-group">
                <div class="input-layout">
                    <label for="spp">Masukkan SPP Kelas</label>
                    <select class="input" name="spp" id="spp">
                        <option value="" disabled>Pilih SPP Kelas</option>
                        <option value="<?=$selectedSpp['id_spp']?>" selected><?=toRupiah($selectedSpp['harga_spp'])?> |
                            <?=ucfirst($selectedSpp['jenis_kelas'])?> | <?=$selectedSpp['tahun_angkatan']?></option>
                        <?php foreach ($spp as $data): ?>
                        <option value="<?=$data['id_spp']?>"><?=toRupiah($data['harga_spp'])?> |
                            <?=ucfirst($data['jenis_kelas'])?> | <?=$data['tahun_angkatan']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div style="width: 57.5rem;"></div>
            <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Ubah
                Kelas</button>
        </div>
    </form>
</section>
<?php
include '../templates/footer.php';
?>