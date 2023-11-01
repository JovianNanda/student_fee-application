<?php
$title = "SPP | Histori";
include '../templates/header.php';
if (isGuest() or isSiswa()) {
    redirect("");
}
$limit = 10;
if(isset($_GET['search']) AND $_GET['search']) {
    $search = sanitize($_GET['search']);
}
if (isset($_GET['limit']) and $_GET['limit']) {
    $limit = $_GET['limit'];
}

if (isset($_GET['page']) and $_GET['page']) {
    $page = intval($_GET['page']);
} else {
    $page = 1;
}
if (totalDataHistory($search ?? '') > 100 and !isset($_GET['limit'])) {
    $limit = 25;
}

$dataHistory = dataHistory($page, $limit, $search ?? null);

?>
<link rel="stylesheet" href="<?=homeUrl()?>/assets/css/page/form.css">
<h1 class="page-title">Data Histori Pembayaran</h1>
<div class="header-container">
    <form action="" method="get">
        <div class="input-group">
            <label for="">Search Nama Siswa</label>
            <div class="input-icon">
                <input class="input c-green bg-outline" type="text" name="search"
                    value="<?=isset($_GET['search']) && $_GET['search'] ? $search : "";?>" placeholder="Nama Siswa">
                <button type="submit" class="button bg-white icon">
                    <span class="ico ico-search ico-2x c-black"></span>
                </button>
            </div>
        </div>
    </form>
    <div class="form" style="margin-top: 25px; display: flex; align-items: center; gap: 15px;">
        <span>Menampilkan</span>
        <form action="">
            <?php if (isset($_GET['search']) && $_GET['search']) {?>
            <input type="hidden" name="search" value="<?=$search?>">
            <?php }?>
            <div class="input-group">
                <select name="limit" id="" class="input" onchange="this.form.submit()">
                    <?php if(isset($_GET['limit']) AND $_GET['limit'] AND !array_search($_GET['limit'], ["0", "10","25","50","100"])): ?>
                        <option value="<?= $_GET['limit'] ?>"><?= $_GET['limit'] ?></option>
                    <?php endif; ?>
                    <option value="10" <?=$limit == "10" ? "selected" : ""?>>10</option>
                    <option value="25" <?=$limit == "25" ? "selected" : ""?>>25</option>
                    <option value="50" <?=$limit == "50" ? "selected" : ""?>>50</option>
                    <option value="100" <?=$limit == "100" ? "selected" : ""?>>100</option>
                </select>
            </div>
        </form>
        <span>dari <?=$dataHistory[2]?> data</span>
    </div>
</div>
<?php
if (mysqli_num_rows($dataHistory[0]) > 0) {?>
<table class="table" style="text-align: center;">
    <thead>
        <tr>
<!--            <th></th>-->
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Bulan Dibayar</th>
            <th>Tahun Dibayar</th>
            <th>Tanggal Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php
$i = 1;
    $noStart = ($page - 1) * $limit;
    foreach ($dataHistory[0] as $history):
    ?>
        <tr>
<!--            <td>-->
<!--                <input type="checkbox" name="select">-->
<!--            </td>-->
            <td><?=$noStart + 1?></td>
            <td><?=ucfirst($history['nama'])?></td>
            <td align="center"><?=BULAN[$history['bulan'] - 1]?></td>
            <td align="center"><?=$history['tahun']?></td>
            <td align="center"><?=strToDate("d", $history['tanggal_pembayaran'])?>
                <?=BULAN[strToDate("n", $history['tanggal_pembayaran']) - 1]?>
                <?=strToDate("Y", $history['tanggal_pembayaran'])?></td>
        </tr>
        <?php
$i++;
    $noStart++;
    endforeach;
    ?>
    </tbody>
</table>
<div class="pagination-wrapper">
    <div class="before">
        <?php if ($page > 1): ?>
        <li class="before"><a
                href="?page=<?=$page - 1?><?=isset($_GET['search']) ? '&search=' . $search : ''?><?=isset($_GET['limit']) ? '&limit=' . $limit : '';?>"><span
                    class="ico ico-caret-left"></span></a></li>
        <?php else: ?>
        <li class="before"><a disabled><span class="ico ico-caret-left " style="color: var(--color-gray)"></span></a>
        </li>
        <?php endif;?>
    </div>
    <ol class="pagination">
        <?php for ($i = 1; $i < $dataHistory[1] + 1; $i++) {?>
        <li class="<?=$page == $i ? 'active' : ''?>"><a
                href="?page=<?=$i?><?=isset($_GET['search']) ? '&search=' . $search : ''?><?=isset($_GET['limit']) ? '&limit=' . $limit : ''?> "><?=$i?></a>
        </li>
        <?php }?>
    </ol>
    <div class="after">
        <?php if ($page < $dataHistory[1]): ?>
        <li class="after"><a
                href="?page=<?=$page + 1?><?=isset($_GET['search']) ? '&search=' . $search : ''?><?=isset($_GET['limit']) ? '&limit=' . $limit : ''?> "><span
                    class="ico ico-caret-right"></span></a></li>
        <?php else: ?>
        <li class="before"><a disabled><span class="ico ico-caret-right " style="color: var(--color-gray)"></span></a>
        </li>
        <?php endif;?>
    </div>
</div>
<?php } else {
    setAlert("Data Tidak Ditemukan!", " bg-red", "ico-exclamation-circle");
}?>
<div class="modal fade" modal-id="delete-petugas">
    <div class="modal-content">
        <div class="modal-header">
            <li class="ico ico-exclamation-triangle c-red"></li>
            <h1 class="title"></h1>
            <span>Konfirmasi Aksimu</span>
        </div>
        <div class="modal-body">
            <form action="" method="post">
                <input class="input" type="hidden" name="idpetugas" value="">
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
<div style="margin-left: 20px;">
    <?php alert();?>
</div>
<?php include '../templates/footer.php';?>