<?php
include_once "../../bootstrap.php";
if (!isLogin() OR isSiswa()) {
    redirect("");
}
$start = $_GET['dateMulai'];
$end = $_GET['dateAkhir'];
$kelas = $_GET['kelas'] ?? null;
$jurusan = $_GET['jurusan'] ?? null;

if ($kelas) {
    $dataKelas = getKelasById($kelas);
}

$data = dataHistoryByTanggal($start, $end, $kelas, $jurusan);
?>

<!doctype html>
<html lang=id>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>History Pembayaran<?php if ($kelas) {
    echo " - " . getKelasSaatIni(getSemester($dataKelas['angkatan'])) . " " . $dataKelas['kelas'];
}
?> <?php if ($jurusan) {
    echo " - " . $jurusan;
}
?> -
        <?php if($start AND $end): ?>
        <?=strToDate("d", $_GET['dateMulai']) . " " . BULAN[toBulanIndex($_GET['dateMulai'])] . " " . strToDate("Y", $_GET['dateMulai'])?>
        sd
        <?=strToDate("d", $_GET['dateAkhir']) . " " . BULAN[toBulanIndex($_GET['dateAkhir'])] . " " . strToDate("Y", $_GET['dateAkhir'])?>
        <?php elseif($start AND !$end): ?>
            <?=strToDate("d", $_GET['dateMulai']) . " " . BULAN[toBulanIndex($_GET['dateMulai'])] . " " . strToDate("Y", $_GET['dateMulai'])?>
        <?php elseif(!$start AND $end): ?>
            <?=strToDate("d", $_GET['dateAkhir']) . " " . BULAN[toBulanIndex($_GET['dateAkhir'])] . " " . strToDate("Y", $_GET['dateAkhir'])?>
        <?php elseif(!$start AND !$end): echo "Semua Periode"; endif;?>
    </title>
    <link rel="stylesheet" href="../../assets/css/app.css">
    <link rel="stylesheet" href="../../assets/css/page/print.css">
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
                    <p>LAPORAN HISTORY PEMBAYARAN SISWA</p>
                    <p style="margin-top: 15px">PERIODE :
                        <?php if($start AND $end): ?>
                            <b><?=strToDate("d", $_GET['dateMulai']) . " " . BULAN[toBulanIndex($_GET['dateMulai'])] . " " . strToDate("Y", $_GET['dateMulai'])?></b>
                            sd
                            <b><?=strToDate("d", $_GET['dateAkhir']) . " " . BULAN[toBulanIndex($_GET['dateAkhir'])] . " " . strToDate("Y", $_GET['dateAkhir'])?></b>
                        <?php elseif($start AND !$end): ?>
                            <b><?=strToDate("d", $_GET['dateMulai']) . " " . BULAN[toBulanIndex($_GET['dateMulai'])] . " " . strToDate("Y", $_GET['dateMulai'])?></b>
                        <?php elseif(!$start AND $end): ?>
                            <b><?=strToDate("d", $_GET['dateAkhir']) . " " . BULAN[toBulanIndex($_GET['dateAkhir'])] . " " . strToDate("Y", $_GET['dateAkhir'])?></b>
                        <?php elseif(!$start AND !$end): echo "Semua Periode"; endif; ?>
                    </p>
                    <?php if ($kelas): ?>
                    <p>
                        KELAS : <?=getKelasSaatIni(getSemester($dataKelas['angkatan'])) . " " . $dataKelas['kelas']?>
                    </p>
                    <?php endif;?>
                    <?php if ($jurusan): ?>
                    <p style="margin-top: 10px">
                        JURUSAN : <?=sanitize($jurusan)?>
                    </p>
                    <?php endif;?>
                </div>
            </div>
            <?php  if (mysqli_num_rows($data[0]) < 1): ?>
            <div style="width: 100%; margin: 5rem 0 10rem 0">
            <h2 style="text-align: center">Tidak ada Data Pembayaran!</h2>
            </div>
            <?php elseif (mysqli_num_rows($data[0]) > 0) : ?>
            <table border="">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NIS</th>
                        <th>NAMA</th>
                        <th>KELAS</th>
                        <th>TANGGAL PEMBAYARAN</th>
                        <th>BULAN TERBAYAR</th>
                        <th>TAHUN TERBAYAR</th>
                        <th>NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
    $i = 1;
    foreach ($data[0] as $history):       ?>
                    <tr>
                        <td align="center"><?=$i?></td>
                        <td align="center"><?=$history['nis']?></td>
                        <td align="left"><?=$history['nama']?></td>
                        <td align="center"><?=getKelasSaatIni(getSemester($history['angkatan'])) . " " . $history['kelas']?>
                        </td>
                        <td align="center">
                            <?=tanggalPembayaran($history['tanggal_pembayaran'])?>
                        </td>
                        <td align="center"><?=BULAN[$history['bulan'] - 1]?></td>
                        <td align="center"><?=$history['tahun']?></td>
                        <td align="right"><?=toRupiah($history['nominal'], false)?></td>
                    </tr>
                    <?php
    $i++;
    endforeach;?>
                </tbody>
            </table>
        </div>
        <footer>
            <p>TOTAL TERBAYAR PADA PERIODE
                <?=strToDate("d", $_GET['dateMulai']) . " " . BULAN[toBulanIndex($_GET['dateMulai'])] . " " . strToDate("Y", $_GET['dateMulai'])?>
                sd
                <?=strToDate("d", $_GET['dateAkhir']) . " " . BULAN[toBulanIndex($_GET['dateAkhir'])] . " " . strToDate("Y", $_GET['dateAkhir'])?>
                <?php if ($kelas): ?> PADA KELAS
                <?=getKelasSaatIni(getSemester($dataKelas['angkatan'])) . " " . $dataKelas['kelas'];endif;?>
                <?php if ($jurusan): ?> PADA JURUSAN
                <?=$jurusan;endif;?>
                &nbsp;:&nbsp; <b><?=toRupiah($data[1]['total'])?></b></p>
            <?php endif; ?>
            <footer>
            <div class="signature">
                <p>Denpasar, <?=date("d") . " " . BULAN[toBulanIndex(date("M"))] . " " . date("Y")?></p>
                <p style="margin-top: 5px">Dicetak dengan Aplikasi SPP | Dicetak Oleh,</p>
            </div>
        </footer>
        <script>
        window.print();
        window.addEventListener("afterprint", function (){
            window.location.href = "<?= homeUrl() ?>/print#history"
        })

        </script>
</body>

</html>