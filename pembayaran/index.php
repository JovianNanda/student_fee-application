<?php
$title = "SPP | Pembayaran";
include '../templates/header.php';

if (isGuest()) {
    redirect("");
}

if (isSiswa()) {
    $_GET['nis'] = getUID();
}

if (isset($_GET['nis']) and $_GET['nis']) {
    $nis = $_GET['nis'];
    if (isset($_GET['semester'])) {
        if ($_GET['semester'] < 1 or $_GET['semester'] > 6) {
            setAlert("Semester Error!", "floating-alert bg-danger", "ico-exclamation-triangle");
            redirect("pembayaran?nis=$nis");
            $siswa = null;
        }
    }

    $siswa = getSiswaByNIS($_GET['nis']);

    if ($siswa) {
        if(!$siswa['angkatan']){
            setAlert("Error Angkatan Tidak Ditemukan!", "floating-alert bg-danger", "ico-exclamation-triangle");
            $siswa = null;
        }
        $semesterSaatIni = getSemester($siswa['angkatan']) ?? 1;
        $semester = $_GET['semester'] ?? $semesterSaatIni;
        $pembayaran = getPembayaran($nis, $semester);
        $semesterSaatIniTemp = $semesterSaatIni / 2;
        $semesterTemp = $semester / 2;
    } else {
        setAlert("Siswa Tidak Ditemukan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        $siswa = null;
    }

    $j = 0;
}

if (isset($_GET['nis'])) {
    if (!$_GET['nis']) {
        setAlert("Data Nis Kosong!", "floating-alert bg-danger", "ico-exclamation-triangle");
    }
}

if (isPost() and isset($_POST['pembayaranBayar'])) {
    insertPembayaran($_POST, intval($semester), stringBool($_GET['strictMode'] ?? true));
}
    if(isSiswa()){  ?>
        <style>
            .print{
                width: 100%!important;
            }
        </style>
   <?php }

Alert();
?>

<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/pembayaran.css">
<h1 class="page-title">Pembayaran</h1>
<section class="pembayaran active">
    <?php if (!isSiswa()): ?>
    <form action="" method="get">
        <div class="input-group">
            <div class="input-layout">
                <label for="nis" class="input-label">Masukkan NIS Siswa</label>
                <input type="number" placeholder="NIS" class="input c-green" name="nis" id="nis"
                    value="<?=$nis ?? null?>">
            </div>
            <button class="button bg-green" style="margin-top:20px; height:50px; font-size: 20px;">Cari</button>
        </div>
    </form>
    <?php endif;?>
    <?php if (isset($siswa)): ?>
    <div class="data-wrapper">
        <div class="biodata">
            <h1 class="title"><?= !isSiswa() ? "Biodata Siswa" : 'Siswa '.$siswa['nama']   ?></h1>
            <div class="biodata-table-wrapper">
                <table class="biodata-table">
                    <tbody>
                        <tr>
                            <td>NISN</td>
                            <td><?=$siswa['nisn']?></td>
                        </tr>
<!--                        --><?php //if (!isSiswa()): ?>
                        <tr>
                            <td>Nama Siswa</td>
                            <td><?=$siswa['nama']?></td>
                        </tr>
                        <tr>
                            <td>Agama</td>
                            <td><?=$siswa['agama']?></td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td><?=$siswa['jk']?></td>
                        </tr>
                        <tr>
<!--                        --><?php //endif; ?>
                            <td>Kelas</td>
                            <!-- Mengecek Kelas aslinya -->
                            <td><?php
if (ceil($semesterSaatIniTemp) != ceil($semesterTemp)) {
    echo getKelasBySemester($semester, $siswa['kelas']) . " (" . getKelasSaatIni($semesterSaatIni) . ")";
} else {
    echo getKelasBySemester($semester, $siswa['kelas']);
}
?>
                            </td>
                        </tr>
<!--                        --><?php //if (!isSiswa()): ?>
                        <tr>
                            <td>No Telp</td>
                            <td><?=$siswa['no_telp']?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td><?=$siswa['alamat']?></td>
                        </tr>
<!--                        --><?php //endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <hr style="margin-top: 40px; margin-bottom: 40px; margin-left: 10px;">
        <div class="input-group semester">
            <form action="" method="get" id="form-semester">
                <div class="left-form">
                    <?php if (!isSiswa()): ?>
                    <input type="hidden" name="nis" value="<?=$_GET['nis']?>">
                    <?php endif;?>
                    <label for="semester">Semester</label>
                    <select name="semester" class="input" id="semester" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <?php if ($semester) {?>
                        <option value="<?=$i?>" <?=$semester == $i ? 'selected' : ""?>>Semester <?=$i?></option>
                        <?php } else {?>
                        <option value="<?=$i?>">Semester <?=$i?></option>
                        <?php }?>
                        <?php endfor;?>
                    </select>
                </div>

                <div class="right-form">
                    <div class="card bg-outline">
                    <?php if (!isSiswa()): ?>
                        <div class="mode-card">
                            <div class="card-header">
                                <?php $strictMode = $_GET['strictMode'] ?? "true"?>
                                <label for="strictMode" class="strictModeLabel">Strict Mode</label>
                                <select class="input <?=!stringBool($strictMode) ? 'is-invalid' : ''?> "
                                    name="strictMode" id="strictMode" onchange="this.form.submit()">
                                    <?php if ($strictMode == "false"): ?>
                                    <option value="true">On</option>
                                    <option value="false" selected>Off</option>
                                    <?php else: ?>
                                    <option value="true" selected>On</option>
                                    <option value="false">Off</option>
                                    <?php endif;?>
                                </select>
                            </div>
                            <div class="card-footer">
                                <span>*Strict Mode : Mode untuk mengendalikan supaya petugas / admin tidak meloncati
                                    pembayaran sebelum pembayaran terakhir</span>
                            </div>
                        </div>
            </form>
            <?php if(isAdmin()) : ?>
            <form action="../print/view/psiswa.php" method="get">
                        <input type="hidden" name="nis" value="<?=$nis?>">
                        <input type="hidden" name="semester" value="<?= $semester ?>">
                        <div class="print">
                            <button class="button bg-green">
                                <li class="ico ico-print c-white"></li>
                            </button>
                        </div>
                    </form>
            <?php endif; ?>
                    </div>
        <?php endif; ?>

                </div>

                <!-- Print -->
            </form>
        </div>
        <!-- Jika Bukan Siswa -->
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Batas Waktu</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <?php if (!isSiswa()): ?>
                    <th>Action</th>
                    <?php endif;?>
                </tr>
            </thead>
            <?php
for ($i = 0; $i < 6; $i++):

    if ($j > sizeof($pembayaran['tanggalPembayaran']) - 1) {
        $j = 0;
    }
    if ($pembayaran["bulan"][$i] == $pembayaran['bulanPembayaran'][$j] and $pembayaran["tahun"][$i] == $pembayaran['tahunPembayaran'][$j]) {
        ?>
            <tr>
                <td><?=$i + 1?>
                    <?php
    ?>
                </td>
                <td><?=BULAN[$pembayaran["bulan"][$i]] . " " . $pembayaran["tahun"][$i];?></td>
                <td><?=batasWaktu($pembayaran['bulan'][$i], $pembayaran['tahun'][$i])?></td>
                <td><?=tanggalPembayaran($pembayaran['tanggalPembayaran'][$j])?></td>
                <td><?=toRupiah($pembayaran["nominal"][$j]);?></td>
                <td>
                    <div class="badge-wrapper">
                        <div class="badge badge-success">Lunas</div>
                    </div>
                </td>
            <?php if (!isSiswa()): ?>
            <td><button class="button bg-success" type="button" disabled style="width: 60px; height:35px;"
                        disabled>Bayar</button></td>
            <?php endif; ?>
            </tr>
            <?php
    $j++;
    } else {?>
            <tr>
                <td><?=$i + 1?>
                    <?php
    ?>
                </td>
                <td><?=BULAN[$pembayaran["bulan"][$i]] . " " . $pembayaran["tahun"][$i];?></td>
                <td><?=batasWaktu($pembayaran['bulan'][$i], $pembayaran['tahun'][$i])?></td>
                <td>-</td>
                <td><?=toRupiah(0);?></td>
                <td>
                    <div class="badge-wrapper">
                        <?php if (cekBatasWaktu($pembayaran['bulan'][$i], $pembayaran['tahun'][$i])): ?>
                        <div class="badge badge-danger">Tertunggak</div>
                        <?php else: ?>
                        <div class="badge badge-warning">Belum Lunas</div>
                        <?php endif;?>
                    </div>
                </td>
                <?php if (!isSiswa()): ?>
                <td>
                    <button class="button bg-success button-action" action="bayar" modal-target="modal-pembayaran"
                        data-bulan="<?=$pembayaran["bulan"][$i] + 1?>" data-tahun="<?=$pembayaran["tahun"][$i]?>"
                        data-bulan-alp="<?=BULAN[$pembayaran["bulan"][$i]]?>"
                        style="width: 60px; height:35px;">Bayar</button>
                </td>
                <?php endif;?>
            </tr>
            <?php }
endfor;
endif;
?>
        </table>
    </div>

    <div class="modal fade" modal-id="modal-pembayaran">
        <div class="modal-content">
            <div class="modal-header">
                <li class="ico ico-square-check c-success"></li>
                <h1 class="title">Konfirmasi Pembayaran</h1>
                <span>Konfirmasi Aksimu</span>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input class="input" type="hidden" name="bulan">
                    <input class="input" type="hidden" name="tahun">
                    <input class="input" type="hidden" name="nis" value="<?=$_GET['nis']?>">
                    <button type="submit" class="button bg-success" name="pembayaranBayar">Bayar</button>
                </form>
                <span>atau</span>
                <button class="button button-outline" modal-dismiss>Batal</button>
            </div>
        </div>
    </div>
</section>
<!-- Script untuk send data ke modal -->
<script src="/uk/assets/js/functions.js"></script>
<?php printValidation();?>
<script>
const button = document.querySelectorAll(".button-action")

button.forEach(btn => {
    btn.addEventListener("click", function() {
        const bulan = btn.getAttribute("data-bulan")
        const bulanAlp = btn.getAttribute("data-bulan-alp")
        const tahun = btn.getAttribute("data-tahun")

        // Mencari input
        const inputBulan = document.querySelector(".input[name=bulan]")
        const inputTahun = document.querySelector(".input[name=tahun]")
        // Title
        const Title = document.querySelector(".title")

        // mengisi input dengan data
        inputBulan.setAttribute("value", bulan)
        inputTahun.setAttribute("value", tahun)
        Title.innerHTML = "Hapus Data <strong>" + bulanAlp + " " + tahun + "</strong> ?"
    })

});
</script>
<?php include '../templates/footer.php';?>