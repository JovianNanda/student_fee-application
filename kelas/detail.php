<?php
$title = "SPP | Kelas";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
alert();
if (!$_GET['id']) {
    setAlert("Kelas Tidak Ditemukan!", "floating-alert bg-danger", "ico-exclamation-circle");
    redirect("kelas/");
    return false;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $kelas = getKelasById($id);

    if ($kelas) {
        $idKelas = $kelas['id_kelas'];
        $semesterSaatIni = getSemester($kelas['angkatan']);
        $semester = $_GET['semester'] ?? $semesterSaatIni;
        $siswaDetails = getSiswaByKelas($idKelas);
        $siswa = getSiswaByKelas($idKelas);
    
        if ($semester < 1 or $semester > 6) {
            if($semesterSaatIni < 0 && $semesterSaatIni > -2 OR $semesterSaatIni > 6){
                return;
            }
            setAlert("Semester Error!", "floating-alert bg-danger", "ico-exclamation-circle");
            redirect("kelas/");
            return false;
        }
        $j = 0;
        $pembayaran = query("SELECT * FROM pembayaran INNER JOIN siswa USING(nis) INNER JOIN kelas USING (id_kelas) WHERE id_kelas = $idKelas");

        if($pembayaran){
            $kelasPembayaran = getPembayaranByKelas($idKelas, $semester);
        }else{
            $kelasPembayaran= [];
        }
    } else {
        setAlert("Kelas Tidak Ditemukan!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("kelas/");
        return false;
    }
} else {
    setAlert("Kelas Tidak Ditemukan!", "floating-alert bg-danger", "ico-exclamation-circle");
    redirect("kelas/");
    return false;
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<?php if (isset($_GET['id'])): ?>
<h1 class="page-title">Siswa Pada Kelas
    <?=getKelasSaatIni(getSemester($kelas['angkatan'])) . " " . strtoupper(($kelas['kelas']))?></h1>
<?php endif; ?>
<div class="nav-container">
    <?php if ($siswa): ?>
    <nav class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item navigation" data-target='#siswa'><a>Siswa</a></li>
            <li class="breadcrumb-item navigation" data-target='#pembayaran'><a>Pembayaran</a>
            </li>
        </ol>
    </nav>
    <div class="print">
        <form action="" method="get" class="formPrint">
            <input type="hidden" name="kelas" id="" value="<?= $_GET['id'] ?>">
            <input type="hidden" name="" class="semester">
            <button class="button bg-green">
                <li class="ico ico-print c-white"></li>
            </button>
        </form>
    </div>
    <?php endif;?>
</div>


<!--
    SISWA VIEW
    -->
<section id="siswa" class="active">
    <div class="header-container">
        <?php if (mysqli_num_rows($siswa) > 0): ?>
        <form action="" method="get">
            <div class="input-group">
                <label for="">Search Siswa</label>
                <div class="input-icon">
                    <input type="hidden" name="nama" value="<?=$kelas['kelas']?>">
                    <input type="hidden" name="angkatan" value="<?=$kelas['angkatan']?>">
                    <input class="input c-green bg-outline" type="text" name="search"
                        value="<?=isset($_GET['search']) ? $_GET['search'] : "";?>" placeholder="Nama Siswa">
                    <button type="submit" class="button bg-white icon">
                        <li class="ico ico-search ico-2x c-black"></li>
                    </button>
                </div>
            </div>
        </form>
        <?php endif;?>
        <?php if (mysqli_num_rows($siswa) < 1 ) {?>
        <div class="alert bg-info" style="margin-left: 20px;">
            <li class="ico ico-info-circle"></li>
            <div class="" style="text-align:center; margin-left:20px;">Belum Ada Siswa!</div>
            <button class="d-none" alert-dismiss></button>
        </div>
        <div style=" margin-left:20px;">
            <a class="button bg-green" href="../siswa/insert.php?kelas=<?=$idKelas?>"
                style="display:flex; padding: 15px 15px; height: 55px; align-items:center; gap:10px">
                <li class="ico ico-plus c-white"></li>Tambah Siswa
            </a>
        </div>
        <?php }?>
    </div>

    <?php if (mysqli_num_rows($siswa) > 0): ?>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Email</th>
                <th>Agama</th>
                <th>No Telp</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php
$i = 1;
while ($siswaDetail = mysqli_fetch_assoc($siswaDetails)):
?>
            <tr>
                <td><?=$i?></td>
                <td><?=$siswaDetail['nis']?></td>
                <td><?=$siswaDetail['nama']?></td>
                <td><?=$siswaDetail['email']?></td>
                <td><?=$siswaDetail['agama']?></td>
                <td><?=$siswaDetail['no_telp']?></td>
                <td><?=$siswaDetail['alamat']?></td>
            </tr>
            <?php
$i++;
endwhile;?>
        </tbody>
    </table>
    <?php endif;?>
</section>

<!--
    PEMBAYARAN VIEW
 -->
<section id="pembayaran" class="d-none">
    <div class="header-container">
        <?php if (mysqli_num_rows($siswa) > 0 AND $pembayaran): ?>
        <form action="" method="get">
            <div class="input-group">
                <label for="">Search Siswa</label>
                <div class="input-icon">
                    <input type="hidden" name="nama" value="<?=$kelas['kelas']?>">
                    <input type="hidden" name="angkatan" value="<?=$kelas['angkatan']?>">
                    <input class="input c-green bg-outline" type="text" name="search"
                        value="<?=isset($_GET['search']) ? $_GET['search'] : "";?>" placeholder="Nama Siswa">
                    <button type="submit" class="button bg-white icon">
                        <li class="ico ico-search ico-2x c-black"></li>
                    </button>
                </div>
            </div>
        </form>
        <?php endif;?>
        <?php if (mysqli_num_rows($siswa) < 1 OR !$pembayaran) {?>
        <div class="alert-wrapper">
            <?php if(mysqli_num_rows($siswa) < 1): ?>
            <div class="alert bg-info" style="margin-left: 20px; margin-bottom: 2rem">
                <li class="ico ico-info-circle"></li>
                <div class="" style="text-align:center; margin-left:20px;">Belum Ada Siswa!</div>
                <button class="d-none" alert-dismiss></button>
            </div>
            <?php endif; ?>
            <?php if (!$pembayaran): ?>
            <div class="alert bg-info" style="margin-left: 20px;">
                <li class="ico ico-info-circle"></li>
                <div class="" style="text-align:center; margin-left:20px;">Data Pembayaran Tidak Ada!</div>
                <button class="d-none" alert-dismiss></button>
            </div>
            <?php endif; ?>
        </div>
        <div style=" margin-left:20px;">
            <a class="button bg-green" href="../siswa/insert.php?kelas=<?=$idKelas?>"
                style="display:flex; padding: 15px 15px; height: 55px; align-items:center; gap:10px">
                <li class="ico ico-plus c-white"></li>Tambah Siswa
            </a>
        </div>
        <?php } else {?>
        <form action="" method="get">
            <div class="input-group">
                <div class="input-layout">
                    <input type="hidden" name="id" value="<?=$_GET['id']?>">
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
            </div>
        </form>
        <?php }?>
    </div>
    <?php if($pembayaran): ?>
    <table style="margin-left:20px;width: 96.5%; ">
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
                <td align="center">
                    <li class="ico ico-check c-success"></li>
                </td>
                <?php } else{?>
                <td></td>
                <?php } ?>
                <?php } ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    endif; ?>
</section>
<script src="../assets/js/functions.js"></script>
<script>
const buttonNav = document.querySelectorAll(".breadcrumb-item")
const formPrint = document.querySelector(".formPrint")
const semesterInput = document.querySelector(".semester")
const urlParams = new URLSearchParams(window.location.search)
const semester = urlParams.get('semester');

document.addEventListener("DOMContentLoaded", function() {
    const hash = window.location.hash
    if (hash == "#siswa") {
        semesterInput.setAttribute("name", "")
        semesterInput.value = ""
        formPrint.setAttribute("action", "<?= homeUrl() ?>/print/view/dsiswa.php")
    } else if (hash == "#pembayaran") {
        if (semester) {
            semesterInput.setAttribute("name", "semester")
            semesterInput.value = semester
        }
        formPrint.setAttribute("action", "<?= homeUrl() ?>/print/view/pkelas.php")
    }
})

buttonNav.forEach(function(nav) {
    nav.addEventListener("click", function() {
        const hash = window.location.hash
        if (hash == "#siswa") {
            semesterInput.setAttribute("name", "")
            semesterInput.value = ""
            formPrint.setAttribute("action", "<?= homeUrl() ?>/print/view/dsiswa.php")
        } else if (hash == "#pembayaran") {
            if (semester) {
                semesterInput.setAttribute("name", "semester")
                semesterInput.value = semester
            }
            formPrint.setAttribute("action", "<?= homeUrl() ?>/print/view/pkelas.php")
        }
    })
})
</script>
<?php
include '../templates/footer.php';
?>