<?php
$title = "SPP | Print";
include '../templates/header.php';

if (!isLogin() OR isSiswa()) {
    redirect("");
}
$nis = sanitize($_GET['nis'] ?? "");
$semester =sanitize($_GET['semester'] ?? "");
$kelas = getAllKelas();
alert();

?>
    <style>
        .wrapper{
            margin: 30px 20px;
        }
        .page-title{
            margin: 30px 0;
        }
        .input-form{
            margin-top: 3rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 35px;
        }
        .input-form .input-group{
            width: 45%;
        }
        .input-form .input-group .input-layout .input{
            width: 100%;
        }
    </style>
<div class="wrapper">
    <h1 class="page-title">Halaman Print</h1>
    <!--History | Pembayaran Siswa (per Orang) | Pembayaran Kelas (yg centang) | Data Siswa-->
    <nav class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item navigation" data-target="#history"><a>History</a></li>
            <li class="breadcrumb-item navigation" data-target="#psiswa"><a>Pembayaran Siswa</a>
            <li class="breadcrumb-item navigation" data-target="#pkelas"><a>Pembayaran Kelas</a>
            <li class="breadcrumb-item navigation" data-target="#datasiswa"><a>Data Siswa</a>
            </li>
        </ol>
    </nav>

<!--History -->
<section class="history active" id="history">
    <h1 style="font-size: 1.5rem;">Print History Pembayaran</h1>



    <form action="view/history.php" method="get">
        <div class="input-form">
            <div class="input-group">
                <div class="input-layout">
                    <label for="dateMulai">Pilih Tanggal Mulai</label>
                    <input type="date" name="dateMulai" class="input" id="dateMulai" max="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout">
                    <label for="dateAkhir">Pilih Tanggal Akhir</label>
                    <input type="date" name="dateAkhir" class="input" id="dateAkhir" max="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="optional" style="display:flex; width: 100%; gap: 35px; flex-wrap: wrap; justify-content: space-between; transition: all 250ms ease;">
                <div class="input-group">
                    <label for="">Pilihan Opsional</label>
                    <hr>
                </div>
                <div class="input-group">
                    <label for="">&nbsp;</label>
                    <hr>
                </div>
                <div class="input-group">
                    <div class="input-layout">
                        <label for="pilihan">Metode Print Data Siswa</label>
                        <select name="pilihan" class="input" id="pilihan">
                            <option selected disabled>Pilih Metode</option>
                            <option value="kelas">Kelas</option>
                            <option value="jurusan">Jurusan</option>
                        </select>
                    </div>
                </div>
            <div class="input-group d-none" id="kelas">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="kelas">Kelas</label>
                    <select name="kelas" class="input" id="kelas">
                        <option selected disabled>Pilih Kelas</option>
                        <?php foreach ($kelas as $dataKelas): ?>
                            <option value="<?= $dataKelas['id_kelas'] ?>"><?= getKelasSaatIni(getSemester($dataKelas['angkatan']))." ". $dataKelas['kelas'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group d-none" id="jurusan">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="jurusan">Jurusan</label>
                    <select name="jurusan" class="input" id="jurusan">
                        <option selected disabled>Pilih Jurusan</option>
                        <?php foreach (JURUSAN as $jurusan): ?>
                        <option value="<?= $jurusan ?>"><?= $jurusan ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
        </div>
        <div class="input-group">
            <div class="input-layout" style="display: flex; align-items: flex-end">
                <button type="submit" class="button bg-success" style="width: 200px;height: 45px; font-size: 1rem; margin-top: 40px; display: flex; align-items: center; justify-content: center; "><li class="ico ico-print c-white" style="margin-right: 15px;"></li>Print</button>
            </div>
        </div>
    </form>
</section>
<!--Pembayaran Siswa Per Orang-->
<section id="psiswa" class="d-none">
    <h1 style="font-size: 1.5rem;">Print Pembayaran Siswa</h1>
    <form action="view/psiswa.php" method="get">
        <div class="input-form">
            <div class="input-group">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="nis">NIS Siswa</label>
                    <input type="number" name="nis"  class="input" placeholder="Masukkan Nis Siswa"  id="nis" value="<?php if($nis) { echo $nis ;} ?>">
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="semester">Semester</label>
                    <select type="number" name="semester"  class="input"  id="semester">
                        <option disabled selected>Pilih Semester</option>
                        <?php if($nis) ?>
                        <?php for($i = 1; $i <= 6 ; $i++) { ?>
                            <?php if ($semester) {?>
                                <option value="<?=$i?>" <?=$semester == $i ? 'selected' : ""?>>Semester <?=$i?></option>
                            <?php } else {?>
                                <option value="<?=$i?>">Semester <?=$i?></option>
                            <?php }?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group">
            <div class="input-layout" style="display: flex; align-items: flex-end">
                <button type="submit" class="button bg-success" style="width: 200px;height: 45px; font-size: 1rem; margin-top: 40px; display: flex; align-items: center; justify-content: center; "><li class="ico ico-print c-white" style="margin-right: 15px;"></li>Print</button>
            </div>
        </div>
    </form>

</section>
<!--Pembayaran per Kelas-->
<section id="pkelas" class="d-none">
    <h1 style="font-size: 1.5rem;">Print Pembayaran Kelas</h1>
    <form action="view/pkelas.php" method="get">
        <div class="input-form">
            <div class="input-group" >
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="kelas">Kelas</label>
                    <select name="kelas" class="input" id="kelas">
                        <option selected disabled>Pilih Kelas</option>
                        <?php foreach ($kelas as $dataKelas): ?>
                            <option value="<?= $dataKelas['id_kelas'] ?>"><?= getKelasSaatIni(getSemester($dataKelas['angkatan']))." ". $dataKelas['kelas'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="semester">Semester</label>
                    <select type="number" name="semester"  class="input"  id="semester">
                        <option selected disabled>Pilih Semester</option>
                        <?php if($nis) ?>
                        <?php for($i = 1; $i <= 6 ; $i++) { ?>
                            <?php if ($semester) {?>
                                <option value="<?=$i?>" <?=$semester == $i ? 'selected' : ""?>>Semester <?=$i?></option>
                            <?php } else {?>
                                <option value="<?=$i?>">Semester <?=$i?></option>
                            <?php }?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group">
            <div class="input-layout" style="display: flex; align-items: flex-end">
                <button type="submit" class="button bg-success" style="width: 200px;height: 45px; font-size: 1rem; margin-top: 40px; display: flex; align-items: center; justify-content: center; "><li class="ico ico-print c-white" style="margin-right: 15px;"></li>Print</button>
            </div>
        </div>
    </form>
</section>
<!-- Data Siswa -->
<section id="datasiswa" class="d-none">
    <h1 style="font-size: 1.5rem;">Print Data Siswa</h1>
    <form action="view/dsiswa.php" method="get">
        <div class="input-form">
            <div class="input-group">
                <div class="input-layout">
                    <label for="pilihan">Metode Print Data Siswa</label>
                    <select name="pilihan" class="input" id="pilihan">
                        <option selected disabled>Pilih Metode</option>
                        <option value="kelas">Kelas</option>
                        <option value="jurusan">Jurusan</option>
                    </select>
                </div>
            </div>
            <div class="input-group d-none" id="kelas" style="opacity: 0; transition: all 250ms ease;">
                <div class="input-layout" style="transition: all 250ms ease;">
                    <label for="kelas">Kelas</label>
                    <select name="kelas" class="input" id="kelas">
                        <option selected disabled>Pilih Kelas</option>
                        <?php foreach ($kelas as $dataKelas): ?>
                            <option value="<?= $dataKelas['id_kelas'] ?>"><?= getKelasSaatIni(getSemester($dataKelas['angkatan']))." ". $dataKelas['kelas'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-group d-none" id="jurusan" style="opacity: 0;transition: all 250ms ease;">
                <div class="input-layout">
                    <label for="jurusan">Jurusan</label>
                    <select name="jurusan" class="input" id="jurusan">
                        <option selected disabled>Pilih Jurusan</option>
                        <?php foreach (JURUSAN as $jurusan): ?>
                            <option value="<?= $jurusan ?>"><?= $jurusan ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="input-group">
            <div class="input-layout" style="display: flex; align-items: flex-end">
                <button type="submit" class="button bg-success" style="width: 200px;height: 45px; font-size: 1rem; margin-top: 40px; display: flex; align-items: center; justify-content: center; "><li class="ico ico-print c-white" style="margin-right: 15px;"></li>Print</button>
            </div>
        </div>
    </form>
</div>
</section>
</div>
    <script src="../assets/js/functions.js"></script>
    <script>
        const pilihan = document.querySelectorAll("select#pilihan")
        const kelas = document.querySelectorAll("div#kelas")
        const jurusan = document.querySelectorAll("div#jurusan")
        const optional = document.querySelector(".optional")
        const inputDate = document.querySelectorAll("input.input[type=date]")

        pilihan.forEach(function (pl) {
            pl.addEventListener("input", function () {
                if(pl.value == "kelas"){
                    kelas.forEach(function (kl) {
                        kl.style.opacity = "1";
                        setTimeout(() => {
                            kl.classList.remove("d-none");
                        }, 250);
                        jurusan.forEach(function (jr) {
                            jr.style.opacity = "0";
                            setTimeout(() => {
                                jr.classList.add("d-none");
                            }, 250);
                        })
                    })
                }
                else if (pl.value == "jurusan") {
                    jurusan.forEach(function (jr) {
                        jr.style.opacity = "1";
                        setTimeout(() => {
                            jr.classList.remove("d-none");
                        }, 250);
                        kelas.forEach(function (kl) {
                            kl.style.opacity = "0";
                            setTimeout(() => {
                                kl.classList.add("d-none");
                            }, 250);
                        })
                    })
                }
            })
        })

        inputDate.forEach(function (input) {
            input.addEventListener("input", function () {
                if(input.getAttribute("name") == "dateAkhir"){
                    inputDate[0].setAttribute("max", input.value)
                    if(inputDate[0].value != ""){
                        if(inputDate[0].value > inputDate[1].value){
                            inputDate[0].value = inputDate[1].value
                        }
                    }
                }
            })

        })

        // kelas.forEach(function (kl) {
        //     kl.addEventListener("input", () => {
        //         jurusan.forEach(function (jr) {
        //             jr.parentElement.style.opacity = "0";
        //             setTimeout(() => {
        //                 jr.parentElement.remove();
        //             }, 250);
        //         })
        //     })
        // })
        // jurusan.forEach(function (jr) {
        //     jr.addEventListener("input", () => {
        //         kelas.forEach(function (kl){
        //             kl.parentElement.style.opacity = "0";
        //             setTimeout(() => {
        //                 kl.parentElement.parentElement.remove();
        //             }, 250);
        //         })
        //     })
        // })


    </script>
<?php
include '../templates/footer.php';
?>