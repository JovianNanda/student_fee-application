<?php
$title = "SPP | Petugas";
include '../templates/header.php';

if (isGuest() or !isAdmin()) {
    redirect("");
}
alert();
if (isPost() and isset($_POST['delete-petugas'])) {
    deletePetugas($_POST);
}
?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Data Petugas</h1>
<div class="header-container">
    <form action="" method="get">
<!--        <div class="input-group">-->
<!--            <label for="">Search Petugas</label>-->
<!--            <div class="input-icon">-->
<!--                <input class="input c-green bg-outline" type="text" name="search"-->
<!--                    value="--><?//=isset($_GET['search']) ? $_GET['search'] : "";?><!--" placeholder="Nama Petugas | Level User">-->
<!--                <button type="submit" class="button bg-white icon">-->
<!--                    <li class="ico ico-search ico-2x c-black"></li>-->
<!--                </button>-->
<!--            </div>-->
<!--        </div>-->
    </form>
    <a class="button bg-green" href="insert.php"
        style="display:flex; padding: 15px 15px; height: 45px; align-items:center; gap:10px">
        <li class="ico ico-plus c-white"></li>Tambah Petugas
    </a>
</div>
<table class="table" style="text-align: center;">
    <thead>
        <tr>
<!--            <th></th>-->
            <th>No</th>
            <th>Username</th>
            <th>Level User</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
$loggedIn = $_SESSION['login']['uid'];
$data = query("SELECT * FROM petugas", false);
$i = 1;
foreach ($data as $petugas):
?>
    <tr>
<!--            <td>-->
<!--                <input type="checkbox" name="select">-->
<!--            </td>-->
            <td><?=$i?></td>
            <td><?=ucfirst($petugas['username'])?></td>
            <td align="center">
                <div style="width: 100px;">
                    <div class="badge badge-info">
                        <?=ucfirst($petugas['level_user'])?>
                    </div>
                </div>
            <td align="center">
                <div class="dropdown dropdown-right">
                    <button class="button button-transparent" data-toggle="dropdown">
                        <li class="ico ico-ellipsis-v c-black"></li>
                    </button>
                    <div class="dropdown-menu">
                        <a href="edit.php?id=<?=$petugas['id_petugas']?>" class="dropdown-item">
                            <li class="ico ico-pen-to-square c-green"></li>
                            Edit
                        </a>
                        <?php if($petugas['id_petugas'] != getUID()) { ?>
                        <a class="dropdown-item button-delete" data-nama="<?=ucfirst($petugas['username'])?>"
                            data-petugas="<?=$petugas['id_petugas']?>" modal-target="delete-petugas">
                            <li class="ico ico-trash c-red"></li>
                            Hapus
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </td>
            </td>
        </tr>
        <?php
$i++;
endforeach;
?>
    </tbody>
</table>

<div class="modal fade" modal-id="delete-petugas">
    <div class="modal-content">
        <div class="modal-header">
            <li class="ico ico-exclamation-triangle c-red"></li>
            <h1 class="title"></h1>
            <span>Konfirmasi Aksimu</span>
        </div>
        <div class="modal-body">
            <form action="" method="post">
                <button type="submit" class="button bg-red" name="delete-petugas">Hapus</button>
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
        const id = btn.getAttribute("data-petugas")
        const nama = btn.getAttribute("data-nama")

        // Mencari input
        const inputNis = document.querySelector(".input[name=idpetugas]")
        // Title
        const Title = document.querySelector(".title")

        // mengisi input dengan data
        inputNis.setAttribute("value", id)
        Title.innerHTML = "Yakin Hapus Petugas <strong>" + nama + "</strong> ? "
    })

});
</script>
<?php
include '../templates/footer.php';
?>