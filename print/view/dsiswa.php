<?php
include_once "../../bootstrap.php";

if (!isLogin() OR isSiswa()) {
    redirect("");
}
$idKelas = sanitize($_GET['kelas'] ?? null);
$jurusan = sanitize(strtolower($_GET['jurusan'] ?? null));
$siswa = getSiswaByKelasOrJurusan($idKelas, $jurusan, "print/#datasiswa");

if($idKelas) {
    $kelas= getKelasById($idKelas);
}

if(!$siswa OR mysqli_num_rows($siswa) < 1 ){
    setAlert("Tidak Ada Siswa!", "floating-alert bg-red", "ico-exclamation-circle");
    redirect("print#datasiswa");
    return false;
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../../assets/css/app.css">
    <link rel="stylesheet" href="../../assets/css/page/print.css">
    <title>Data Siswa - <?php if ($idKelas) { ?>Kelas
        <?= getKelasSaatIni(getSemester($kelas['angkatan'])) . " " . $kelas['kelas']; } ?> <?php if($jurusan) { ?>
        <?= ucfirst($jurusan);} ?></title>
</head>

<body>
    <div class="container">
        <div class="header" style="margin: 25px">
            <div class="header-img">
                <img src="<?=homeUrl()?>/assets/img/logosmk.png" alt="" width="80">
            </div>
            <div class="header-title">
                <h1>SMK TI BALI GLOBAL DENPASAR</h1>
                <p class="alamat">Jl. Tukad Citarum No.44 Denpasar Telp (0361) 249434</p>
                <hr>
                <p>LAPORAN DATA SISWA</p>
                <?php if($idKelas): ?>
                <p><b><?= getKelasSaatIni(getSemester($kelas['angkatan'])). " ". $kelas['kelas'] ?></b></p>
                <p><b>Semester <?= getSemester($kelas['angkatan']) ?></b> | <b>Angkatan <?= $kelas['angkatan'] ?></b>
                </p>
                <?php elseif($_GET['jurusan']): ?>
                <p>Jurusan : <b><?= ucfirst($jurusan) ?></b></p>
                <?php endif; ?>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <?php if($jurusan){ ?>
                    <th>Kelas</th>
                    <?php } ?>
                    <th>Agama</th>
                    <th>Jenis Kelamin</th>
                    <th>Telp</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                <?php
            while($data = mysqli_fetch_assoc($siswa)) {
            ?>
                <tr>
                    <td align="center"><?= $data['nis'] ?></td>
                    <td align="left"><?= $data['nama'] ?></td>
                    <td align="left"><?= $data['email'] ?></td>
                    <?php if($jurusan){ ?>
                    <td align="center"><?= getKelasSaatIni(getSemester($data['angkatan'])). " " .$data['kelas'] ?></td>
                    <?php } ?>
                    <td align="center"><?= $data['agama'] ?></td>
                    <td align="center"><?= $data['jk'] ?></td>
                    <td align="center"><?= $data['no_telp'] ?></td>
                    <td align="center"><?= $data['alamat'] ?></td>
                </tr>
                <?php
                }
            ?>
            </tbody>
        </table>
    </div>
    <footer>
        <div class="signature">
            <p>Denpasar, <?=date("d") . " " . BULAN[toBulanIndex(date("M"))] . " " . date("Y")?></p>
            <p style="margin-top: 5px">Dicetak dengan Aplikasi SPP | Dicetak Oleh,</p>
        </div>
    </footer>
    <script>
    window.print();
    window.onafterprint = function() {
        window.location.href = "<?= homeUrl() ?>/print#datasiswa"
    }
    </script>
</body>

</html>