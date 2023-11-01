<?php

$title = "SPP | Kelas";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
alert();
if (isPost() and isset($_POST['delete-kelas'])) {
    deleteKelas($_POST);
}

$limit = 10;
if(isset($_GET['limit']) AND $_GET['limit']) $limit = $_GET['limit'];
if(isset($_GET['page']) AND $_GET['page']) $page = intval($_GET['page']); else $page = 1;

if(totalDataKelasSpp($_GET['search'] ?? '') > 100 and !isset($_GET['limit'])){
    $limit = 25;
}


$data = getKelasAndSPP($page, $limit, $_GET['search'] ?? null);

?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Data Kelas</h1>
<div class="header-container">
    <form action="" method="get">
        <div class="input-group">
            <label for="">Search Kelas</label>
            <div class="input-icon">
                <input class="input c-green bg-outline" type="text" name="search"
                    value="<?=isset($_GET['search']) ? $_GET['search'] : "";?>" placeholder="Nama Kelas">
                <button type="submit" class="button bg-white icon">
                    <li class="ico ico-search ico-2x c-black"></li>
                </button>
            </div>
        </div>
    </form>
    <div class="form" style="margin-top: 25px; display: flex; align-items: center; gap: 15px;">
        <span>Menampilkan</span>
        <form action="" >
            <?php if(isset($_GET['search'])) { ?>
                <input type="hidden" name="search" value="<?= $_GET['search'] ?>">
            <?php } ?>
            <div class="input-group">
                <select name="limit" id="" class="input" onchange="this.form.submit()">
                    <?php if($data[2] <= 100) : ?>
                        <option value="10" <?= $limit == "10" ? "selected" : ""?>>10</option>
                    <?php endif; ?>
                    <option value="25" <?= $limit == "25" ? "selected" : ""?>>25</option>
                    <option value="50" <?= $limit == "50" ? "selected" : ""?>>50</option>
                    <option value="100" <?= $limit == "100" ? "selected" : ""?>>100</option>
                </select>
            </div>
        </form>
        <span>dari <?= $data[2] ?> data</span>
    </div>
    <div></div>
    <a class="button bg-green" href="insert.php"
       style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px; margin-top: 25px; ">
        <li class="ico ico-plus c-white"></li>Tambah Kelas
    </a>
</div>
<table class="table" style="text-align: center;">
    <thead>
        <tr>
<!--            <th></th>-->
            <th>No</th>
            <th>Nama Kelas</th>
            <th>Jurusan</th>
            <th>Jenis Kelas</th>
            <th>Angkatan</th>
            <th>Harga SPP</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
$i = 1;
$noStart = ($page - 1) * $limit;
while ($kelas = mysqli_fetch_assoc($data[0])):
    $siswa = getSiswaByKelas($kelas['id_kelas']);
    ?>
        <tr>
<!--            <td>-->
<!--                <input type="checkbox" name="select">-->
<!--            </td>-->
            <td><?=$noStart+1?></td>
            <td><a class="button"
                    href="<?=homeUrl()?>/kelas/detail.php?id=<?=$kelas['id_kelas']?>"><?=getKelasSaatIni(getSemester($kelas['angkatan'])) . " " . $kelas['kelas']?></a>
            </td>
            <td><?=$kelas['jurusan']?></td>
            <td><?=ucfirst($kelas['jenis_kelas'])?></td>
            <td><?=$kelas['angkatan']?></td>
            <td><?=toRupiah($kelas['harga_spp'])?></td>
            <td>
                <div class="dropdown">
                    <button class="button button-transparent" data-toggle="dropdown">
                        <li class="ico ico-ellipsis-v c-black"></li>
                    </button>
                    <div class="dropdown-menu">
                        <a href="edit.php?id=<?=$kelas['id_kelas']?>" class="dropdown-item">
                            <li class="ico ico-pen-to-square c-green"></li>
                            Edit
                        </a>
                        <?php if (mysqli_num_rows($siswa) < 1): ?>
                        <a class="dropdown-item button-delete" data-kelas="<?=$kelas['id_kelas']?>"
                            data-nama="<?= getKelasSaatIni(getSemester($kelas['angkatan'])). " " .$kelas['kelas']?>" modal-target="delete-kelas">
                            <li class="ico ico-trash c-red"></li>
                            Hapus
                        </a>
                        <?php endif;?>
                    </div>
                </div>
            </td>
        </tr>
        <?php
$i++;
$noStart++;
endwhile;

?>
    </tbody>
</table>
<div class="pagination-wrapper">
        <div class="before">
            <?php if($page > 1) : ?>
                <li class="before"><a href="?page=<?= $page-1 ?><?=isset($_GET['search']) ? '&search='.$_GET['search'] : '' ?><?=isset($_GET['limit']) ? '&limit='.$limit : ''; ?>"><span class="ico ico-caret-left"></span></a></li>
            <?php else: ?>
                <li class="before"><a disabled><span class="ico ico-caret-left " style="color: var(--color-gray)"></span></a></li>
            <?php endif; ?>
        </div>
        <ol class="pagination">
            <?php for($i = 1; $i < $data[1]+1; $i++) { ?>
                <li class="<?= $page == $i ? 'active' : ''?>"><a href="?page=<?= $i ?><?= isset($_GET['search']) ? '&search='.$_GET['search'] : '' ?><?= isset($_GET['limit']) ? '&limit='.$limit : '' ?> "><?= $i ?></a></li>
            <?php } ?>
        </ol>
        <div class="after">
            <?php if($page < $data[1]) : ?>
                <li class="after"><a href="?page=<?= $page+1 ?><?= isset($_GET['search']) ? '&search='.$_GET['search'] : '' ?><?= isset($_GET['limit']) ? '&limit='.$limit : '' ?> "><span class="ico ico-caret-right"></span></a></li>
            <?php else: ?>
                <li class="before"><a disabled><span class="ico ico-caret-right " style="color: var(--color-gray)"></span></a></li>
            <?php endif; ?>
        </div>
    </div>
<div class="modal fade" modal-id="delete-kelas">
    <div class="modal-content">
        <div class="modal-header">
            <li class="ico ico-exclamation-triangle c-red"></li>
            <h1 class="title"></h1>
            <span>Konfirmasi Aksimu</span>
        </div>
        <div class="modal-body">
            <form action="" method="post">
                <input class="input" type="hidden" name="idKelas" value="">
                <button type="submit" class="button bg-red" name="delete-kelas">Hapus</button>
            </form>
            <span>atau</span>
            <button class="button button-outline" modal-dismiss>Batal</button>
        </div>
    </div>
</div>
<script>
const button = document.querySelectorAll(".button-delete")

button.forEach(btn => {
    btn.addEventListener("click", function() {
        const nis = btn.getAttribute("data-kelas")
        const nama = btn.getAttribute("data-nama")

        // Mencari input
        const inputNis = document.querySelector(".input[name=idKelas]")
        // Title
        const Title = document.querySelector(".title")

        // mengisi input dengan data
        inputNis.setAttribute("value", nis)
        Title.innerHTML = "Hapus Kelas <strong> " + nama + "</strong> ?"
    })

});
</script>
<?php
include '../templates/footer.php';
?>