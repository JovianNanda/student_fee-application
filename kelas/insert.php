<?php
$title = "SPP | Insert Kelas";
include '../templates/header.php';
if (isGuest() or !isAdmin()) {
    redirect("");
}
if (isPost()) {
    insertKelas($_POST);
}
$spp = getAllspp();
alert();
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Tambah Data Kelas</h1>
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
            <div class="input-group">
                <div class="input-layout">
                    <label for="kelas">Masukkan Nama Kelas</label>
                    <input id="kelas" type="text" class="input" name="kelas" placeholder="Nama Kelas ex: RPL 2">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jurusan">Masukkan Jurusan Kelas</label>
                    <select class="input" name="jurusan" id="jurusan">
                        <option value="" selected disabled>Pilih Jurusan Kelas</option>
                        <?php foreach (JURUSAN as $jurusan): ?>
                        <option value="<?=$jurusan?>"><?=$jurusan?></option>
                        <?php endforeach;?>
                    </select>
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
        <div class="wrapper">
            <div class="input-group">
                <div class="input-layout">
                    <label for="spp">Masukkan SPP Kelas</label>
                    <select class="input" name="spp" id="spp">
                        <option value="" selected disabled>Pilih SPP Kelas</option>
                        <?php foreach ($spp as $data): ?>
                        <option value="<?=$data['id_spp']?>"><?=toRupiah($data['harga_spp'])?> |
                            <?=ucfirst($data['jenis_kelas'])?> | <?=$data['tahun_angkatan']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div style="width: 56.5rem;"></div>
            <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Simpan
                Kelas</button>
        </div>
    </form>
</section>
<?php
include '../templates/footer.php';
?>