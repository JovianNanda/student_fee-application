<?php
$title = "SPP | Insert Siswa";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
if (isPost()) {
    insertSiswa($_POST);
}
if (isset($_GET['kelas']) and $_GET['kelas']) {
    $idKelas = $_GET['kelas'];
    $kelas = query("SELECT * FROM kelas WHERE id_kelas != $idKelas");
    $kelasSelected = query("SELECT * FROM kelas WHERE id_kelas = $idKelas");
} else {
    $kelas = getAllKelas();
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Tambah Data Siswa</h1>
<div class="header-container">
    <div></div>
    <a class="button bg-red" href="../siswa/"
        style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
        <li class="ico ico-arrow-left c-white"></li>Kembali
    </a>
</div>
<section class="siswa active">
    <form action="" method="post" id="siswaForm">
        <div class="wrapper-grid">
            <div class="input-group">
                <div class="input-layout">
                    <label for="nis">Masukkan NIS Siswa</label>
                    <input id="nis" type="number" class="input" name="nis" placeholder="NIS Siswa">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="nisn">Masukkan NISN Siswa</label>
                    <input id="nisn" type="number" class="input" name="nisn" placeholder="NISN Siswa">
                </div>
            </div>
        </div>
        <div class="wrapper">
            <div class="input-group">
                <div class="input-layout">
                    <label for="nama">Masukkan Nama Siswa</label>
                    <input id="nama" type="text" class="input" name="nama" placeholder="Nama Siswa">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="email">Masukkan Email Siswa</label>
                    <input id="email" type="email" class="input" name="email"
                        placeholder="Email Siswa | example@gmail.com">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="password">Masukkan Password Untuk Siswa</label>
                    <div class="input-icon" id="password">
                        <input id="password" type="text" class="input" name="password" placeholder="Password Siswa"
                            value="smktibaliglobal">
                        <button type="button" class="button bg-transparent icon no-border" data-toggle="password">
                            <li class="ico ico-eye-slash c-black"></li>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div class="input-group">
                <div class="input-layout">
                    <label for="kelas">Masukkan Kelas Siswa</label>
                    <select class="input" name="kelas" id="kelas">
                        <?php if (isset($_GET['kelas']) and $_GET['kelas']): ?>
                        <option value="" disabled>Pilih Kelas Siswa</option>
                        <option value="<?=$kelasSelected['id_kelas']?>" selected>
                            <?=getKelasSaatIni(getSemester($kelasSelected['angkatan'])) . " " . $kelasSelected['kelas']?>
                            <?php //$data['jurusan'] ?> |
                            <?=ucfirst($kelasSelected['jenis_kelas'])?></option>
                        <?php else: ?>
                        <option value="" selected disabled>Pilih Kelas Siswa</option>
                        <?php endif;?>
                        <?php foreach ($kelas as $data): ?>
                        <option value="<?=$data['id_kelas']?>">
                            <?=getKelasSaatIni(getSemester($data['angkatan'])) . " " . $data['kelas']?>
                            <?php //$data['jurusan'] ?> |
                            <?=ucfirst($data['jenis_kelas'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="agama">Masukkan Agama Siswa</label>
                    <select class="input" name="agama" id="agama">
                        <option value="" selected disabled>Pilih Agama Siswa</option>
                        <?php foreach (AGAMA as $agama): ?>
                        <option value="<?=$agama?>"><?=$agama?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="jk">Masukkan Jenis Kelamin Siswa</label>
                    <select class="input" name="jk" id="jk">
                        <option value="" selected disabled>Pilih Jenis Kelamin Siswa</option>
                        <?php foreach (JENIS_KELAMIN as $jk): ?>
                        <option value="<?=$jk?>"><?=$jk?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="noTelp">Masukkan No Telp Untuk Siswa</label>
                    <input id="noTelp" type="number" class="input" name="noTelp" placeholder="No Telp">
                </div>
            </div>
        </div>
        <div class="wrapper">
            <div class="input-group">
                <div class="input-layout">
                    <label for="alamat">Masukkan Alamat Siswa</label>
                    <textarea name="alamat" placeholder="Alamat Siswa" class="input" id="alamat" style="resize: none;"
                        cols="1" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="wrapper-grid">
            <div style="width: 55.5rem;"></div>
            <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Simpan
                Siswa</button>
        </div>
    </form>
</section>
<?php
include '../templates/footer.php';
?>