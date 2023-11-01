<?php
include_once "../../bootstrap.php";

if (!isLogin() OR isSiswa()) {
    redirect("");
}

if($_GET['kelas']){
    $idKelas = sanitize($_GET['kelas']);
}else{
    setAlert("Kelas Tidak Ditemukan!", "floating-alert bg-red", "ico-exclamation-triangle");
    redirect("print/#pkelas");
    return false;
}

$kelas = getKelasById($idKelas);
if($kelas) {
    $semester = sanitize($_GET['semester'] ?? getSemester($kelas['angkatan']));
    if($semester >= 1 and $semester <= 6) {
        $pembayaran = query("SELECT * FROM pembayaran INNER JOIN siswa USING(nis) INNER JOIN kelas USING (id_kelas) WHERE id_kelas = $idKelas");
        if($pembayaran){
            $kelasPembayaran = getPembayaranByKelas($idKelas, $semester);
        }else{
            setAlert("Tidak Ada Data Pembayaran!", "floating-alert bg-info", "ico-exclamation-circle");
            redirect("print/#pkelas");
            return false;
        }
    }else {
        setAlert("Semester Tidak Valid!", "floating-alert bg-red", "ico-exclamation-triangle");
        redirect("print/#pkelas");
        return false;
    }
}else{
    setAlert("Kelas Tidak Ditemukan!", "floating-alert bg-red", "ico-exclamation-triangle");
    redirect("print/#pkelas");
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
    <title>Pembayaran Kelas - <?= getKelasSaatIni(getSemester($kelas['angkatan']))." " .$kelas['kelas']?> - Semester <?= ($semester) ? $semester : getSemester($kelas['angkatan']) ?></title>
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
            <p>LAPORAN PEMBAYARAN KELAS</p>
            <p><b><?= getKelasSaatIni(getSemester($kelas['angkatan'])). " ". $kelas['kelas'] ?></b></p>
            <p><b>Semester  <?= $semester ?></b> | <b>Angkatan <?= $kelas['angkatan'] ?></b>
            </p>
        </div>
    </div>

    <table border="">
        <thead>
        <th>Nama Siswa</th>
        <?php $tempBayar = array_values($kelasPembayaran)[0]['pembayaran'];?>
        <?php foreach($tempBayar as $key => $dataPembayaran): ?>
            <th><?= $key ?></th>
            <?php
            $kunci[] = $key;
        endforeach;
        ?>
        </thead>
        <tbody>
        <?php foreach($kelasPembayaran as $key => $dataPembayaran): ?>
            <tr>
                <td><?= $dataPembayaran['nama'] ?></td>
                <?php for ($i = 0; $i < 6; $i++) { ?>
                    <?php if($dataPembayaran['pembayaran'][$kunci[$i]]){ ?>
                        <td align="center"><li class="ico ico-check c-black" style="font-size: 0.8rem" ></li></td>
                    <?php } else{?>
                        <td align="center">-</td>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="keterangan">
        <p>Ket</p>
    </div>
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
        window.location.href = "<?= homeUrl() ?>/print#pkelas"

    }
</script>
</body>
</html>