<?php
$title = "SPP | List SPP";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
alert();

if (isPost() and isset($_POST['delete-spp'])) {
    deleteSpp($_POST);
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Data SPP</h1>
<div class="header-container">
    <form action="" method="get">
<!--        <div class="input-group">-->
<!--            <label for="">Filter SPP</label>-->
<!--            <div class="input-icon">-->
<!--                <input class="input c-green bg-outline" type="text" name="search"-->
<!--                    value="--><?//=isset($_GET['search']) ? $_GET['search'] : "";?><!--" placeholder="Nama SPP">-->
<!--                <button type="submit" class="button bg-white icon">-->
<!--                    <li class="ico ico-search ico-2x c-black"></li>-->
<!--                </button>-->
<!--            </div>-->
<!--        </div>-->
    </form>
    <a class="button bg-green" href="insert.php"
        style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
        <li class="ico ico-plus c-white"></li>Tambah SPP
    </a>
</div>
<table class="table" style="text-align: center;">
    <thead>
        <tr>
<!--            <th></th>-->
            <th>No</th>
            <th>Harga SPP</th>
            <th>Jenis Kelas</th>
            <th>Angkatan</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
$data = getAllSpp();
$i = 1;
foreach ($data as $spp):
    $idSpp = $spp['id_spp'];
    $kelasWSpp = query("SELECT * FROM kelas WHERE id_spp = $idSpp", false);
    ?>
        <tr>
<!--            <td>-->
<!--                <input type="checkbox" name="select">-->
<!--            </td>-->
            <td><?=$i?></td>
            <td><?=toRupiah($spp['harga_spp'])?></a>
            </td>
            <td><?=ucfirst($spp['jenis_kelas'])?></td>
            <td><?=$spp['tahun_angkatan']?></td>
            <td align="center">
                <div class="dropdown">
                    <button class="button button-transparent" data-toggle="dropdown">
                        <li class="ico ico-ellipsis-v c-black"></li>
                    </button>
                    <div class="dropdown-menu">
                        <a href="edit.php?id=<?=$spp['id_spp']?>" class="dropdown-item">
                            <li class="ico ico-pen-to-square c-green"></li>
                            Edit
                        </a>
                        <?php if (mysqli_num_rows($kelasWSpp) < 1): ?>
                        <a class="dropdown-item button-delete"
                            data-nama="<?=toRupiah($spp['harga_spp'])?> | <?=ucfirst($spp['jenis_kelas'])?> | <?=$spp['tahun_angkatan']?>"
                            data-spp="<?=$spp['id_spp']?>" modal-target="delete-spp">
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
endforeach;
?>
    </tbody>
</table>

<div class="modal fade" modal-id="delete-spp">
    <div class="modal-content">
        <div class="modal-header">
            <li class="ico ico-exclamation-triangle c-red"></li>
            <h1 class="title"></h1>
            <span>Konfirmasi Aksimu</span>
        </div>
        <div class="modal-body">
            <form action="" method="post">
                <input class="input" type="hidden" name="idspp" value="">
                <button type="submit" class="button bg-red" name="delete-spp">Hapus</button>
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
        const id = btn.getAttribute("data-spp")
        const nama = btn.getAttribute("data-nama")

        // Mencari input
        const inputNis = document.querySelector(".input[name=idspp]")
        // Title
        const Title = document.querySelector(".title")

        // mengisi input dengan data
        inputNis.setAttribute("value", id)
        Title.innerHTML = "Yakin Hapus SPP <strong>" + nama + "</strong> ? "
    })

});
</script>
<?php
include '../templates/footer.php';
?>