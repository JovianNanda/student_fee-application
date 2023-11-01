<?php
include_once "../../bootstrap.php";

if (!isLogin() OR isSiswa()) {
    redirect("");
}
$nis = sanitize($_GET['nis']);
$siswa = getSiswaByNIS($nis);
if($siswa){
    $semester = sanitize($_GET['semester'] ?? getSemester($siswa['angkatan']));
        if($semester >= 1 and $semester <= 6) {
            $pembayaran = getPembayaran($nis, $semester);
        }else {
            setAlert("Semester Tidak Valid!", "floating-alert bg-red", "ico-exclamation-triangle");
            redirect("print/#psiswa");
            return false;
        }

    }
else {
    setAlert("Siswa Tidak Ditemukan!", "floating-alert bg-red", "ico-exclamation-triangle");
    redirect("print/#psiswa");
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
    <title>Pembayaran Siswa - <?= $siswa['nama'] ?> - Semester <?= $semester ?> </title>
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
            <p>LAPORAN PEMBAYARAN SISWA</p>
            <p><b><?= $siswa['nama'] ?></b></p>
            <p><b>Semester  <?= $semester ?></b> | <b>Angkatan <?= $siswa['angkatan'] ?></b>
            </p>
        </div>
    </div>

    <table border="">
        <thead>
        <tr>
            <th>NO</th>
            <th>BULAN</th>
            <th>TAHUN</th>
            <th>TANGGAL PEMBAYARAN</th>
            <th>STATUS</th>
            <th>NOMINAL</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $j = 0;
        if (is_numeric($semester)) :
            for ($i = 0; $i < 6; $i++):

            if ($j > sizeof($pembayaran['tanggalPembayaran']) - 1) {
                $j = 0;
            }
            if ($pembayaran["bulan"][$i] == $pembayaran['bulanPembayaran'][$j] and $pembayaran["tahun"][$i] == $pembayaran['tahunPembayaran'][$j]) {
                ?>
                <tr>
                    <td align="center"><?=$i+1?></td>
                    <td align="center"><?= BULAN[$pembayaran['bulan'][$i]] ?></td>
                    <td align="center"><?= $pembayaran['tahun'][$i] ?></td>
                    <td align="center"><?=tanggalPembayaran($pembayaran['tanggalPembayaran'][$j])?></td>
                    <td align="center">
                        <div class="badge-wrapper" >
                            <div class="badge badge-success" style="width:40%; height:10px; display: flex; align-items: center; justify-content: center">Lunas</div>
                        </div>
                    </td>
                    <td align="right"><?= toRupiah($pembayaran['nominal'][$j], false); ?></td>
                </tr>
                <?php
                }else{ ?>
                <tr>
                    <td align="center"><?=$i+1?></td>
                    <td align="center"><?= BULAN[$pembayaran['bulan'][$i]] ?></td>
                    <td align="center"><?= $pembayaran['tahun'][$i] ?></td>
                    <td align="center">-</td>
                    <td align="center">

                        <div class="badge-wrapper" >
                            <?php if (cekBatasWaktu($pembayaran['bulan'][$i], $pembayaran['tahun'][$i])): ?>
                                <div class="badge badge-danger" style="width:40%; height:10px; display: flex; align-items: center; justify-content: center">Tertunggak</div>
                            <?php else: ?>
                                <div class="badge badge-warning" style="width:40%;;height:10px; display: flex; align-items: center; justify-content: center">Belum Lunas</div>
                            <?php endif;?>
                        </div>
                    </td>
                    <td align="right">0</td>
                </tr>
           <?php }
            $j++;
            endfor;
        endif;
        ?>
        </tbody>
    </table>
</div>
<footer>
    <p>TOTAL TERBAYAR SISWA <?= $siswa['nama'] ?>
        PADA Semester <?= $semester ?>&nbsp; : &nbsp;<b><?= toRupiah($pembayaran['totalBayar']) ?></b> </p>
    <div class="signature">
        <p>Denpasar, <?=date("d") . " " . BULAN[toBulanIndex(date("M"))] . " " . date("Y")?></p>
        <p style="margin-top: 5px">Dicetak dengan Aplikasi SPP | Dicetak Oleh,</p>
    </div>
</footer>
<script>
    window.print();
    window.onafterprint = function() {
        window.location.href = "<?= homeUrl() ?>/print#psiswa"
    }
</script>
</body>
</html>