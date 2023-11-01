<?php
$title = "SPP | Edit Siswa";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}

if ($_GET['nis'] and isset($_GET['nis'])) {
    $nis = $_GET['nis'];
    $siswa = getSiswaByNIS($nis);

    if (!$siswa) {
        setAlert("Siswa Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("siswa");
        return false;
    }
} else {
    setAlert("Siswa Tidak Ditemukan", "floating-alert bg-danger", "ico-exclamation-circle");
    redirect("siswa");
    return false;
}
if (isPost()) {
    editSiswa($_POST);
}
$id_kelas = $siswa['id_kelas'];
$kelas = query("SELECT * FROM kelas WHERE id_kelas != $id_kelas");
$selectedKelas = query("SELECT * FROM kelas WHERE id_kelas = $id_kelas");
$newAgama = array_diff(AGAMA, [$siswa['agama']]);
$newJK = array_diff(JENIS_KELAMIN, [$siswa['jk']]);
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Edit Data Siswa <strong><?=$siswa['nama']?></strong></ h1>
    <div class="header-container">
        <div></div>
        <a class="button bg-red" href="../siswa/"
            style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px; font-size:1rem; font-weight: normal;">
            <li class="ico ico-arrow-left c-white"></li>Kembali
        </a>
    </div>
    <section class="siswa active">
        <form action="" method="post" id="siswaForm">
            <div class="wrapper-grid">
                <div class="input-group">
                    <div class="input-layout">
                        <label for="nis">NIS Siswa</label>
                        <input type="hidden" name="nis_lama" value=<?=$siswa['nis']?>>
                        <input id="nis" type="number" class="input c-info" placeholder="NIS Siswa" name="nis"
                            value="<?=$siswa['nis']?>">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="nisn">NISN Siswa</label>
                        <input id="nisn" type="number" class="input c-info" name="nisn" placeholder="NISN Siswa"
                            value="<?=$siswa['nisn']?>">
                    </div>
                </div>
            </div>
            <div class="wrapper">
                <div class="input-group">
                    <div class="input-layout">
                        <label for="nama">Masukkan Nama Siswa</label>
                        <input id="nama" type="text" class="input c-info" name="nama" placeholder="Nama Siswa"
                            value="<?=$siswa['nama']?>">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="email">Masukkan Email Siswa</label>
                        <input id="email" type="email" class="input c-info" name="email"
                            placeholder="Email Siswa | example@gmail.com" value="<?=$siswa['email']?>">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="password">Masukkan Password Baru Untuk Siswa</label>
                        <div class="input-icon" id="password">
                            <input id="password" type="text" class="input c-info" name="password"
                                placeholder="Password Baru">
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
                            <option disabled>Pilih Kelas Siswa</option>
                            <option value="<?=$selectedKelas['id_kelas']?>">
                                <?=getKelasSaatIni(getSemester($selectedKelas['angkatan'])) . " " . $selectedKelas['kelas']?>
                                <?php //$data['jurusan'] ?> |
                                <?=ucfirst($selectedKelas['jenis_kelas'])?></option>
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
                            <option disabled>Pilih Agama Siswa</option>
                            <option value="<?=$siswa['agama']?>"><?=$siswa['agama']?></option>
                            <?php foreach ($newAgama as $agama): ?>
                            <option value="<?=$agama?>"><?=$agama?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="jk">Masukkan Jenis Kelamin Siswa</label>
                        <select class="input" name="jk" id="jk">
                            <option value="" disabled>Pilih Jenis Kelamin Siswa</option>
                            <option selected value="<?=$siswa['jk']?>"><?=$siswa['jk']?></option>
                            <?php foreach ($newJK as $jk): ?>
                            <option value="<?=$jk?>"><?=$jk?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="noTelp">Masukkan No Telp Untuk Siswa</label>
                        <input id="noTelp" type="number" class="input c-info" name="noTelp" placeholder="No Telp"
                            value="<?=$siswa['no_telp']?>">
                    </div>
                </div>
            </div>
            <div class="wrapper">
                <div class="input-group">
                    <div class="input-layout">
                        <label for="alamat">Masukkan Alamat Siswa</label>
                        <textarea name="alamat" placeholder="Alamat Siswa" class="input c-info" id="alamat"
                            style="resize: none;" cols="1" rows="2"><?=$siswa['alamat']?></textarea>
                    </div>
                </div>
            </div>
            <div class="wrapper-grid">
                <div style="width: 54rem;"></div>
                <button class="button bg-success" style="padding: 0 20px; height: 50px; font-size: 14px;">Ubah
                    Siswa</button>
            </div>
        </form>
    </section>
    <?php
include '../templates/footer.php';
?>