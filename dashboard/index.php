<?php
$title = "SPP | Dashboard";
include '../templates/header.php';
if(!isAdmin()){
    redirect("pembayaran");
}
if (isGuest() ) {
    redirect("");
}
Alert();
isInvalid();
?>
<h1 class="page-title">Dashboard</h1>
<section class="dashboard-grid active">
    <div class="card bg-outline">
        <div class="card-icon">
            <div class="circle circle-green">
                <li class="ico ico-sack-dollar c-green"></li>
            </div>
        </div>
        <div class="card-content">
            <div class="card-header">
                <h1><?=toRupiah(totalPembayaran()['total'])?></h1>
            </div>
            <div class="card-text c-gray">
                <span>Total Pembayaran</span>
            </div>
        </div>
    </div>
    <div class="card bg-outline">
        <div class="card-icon">
            <div class="circle circle-red">
                <li class="ico ico-users c-red"></li>
            </div>
        </div>
        <div class="card-content">
            <div class="card-header">
                <h1><?=totalSiswa()['total']?></h1>
            </div>
            <div class="card-text c-gray">
                <span>Total Siswa</span>
            </div>
        </div>
    </div>
    <div class="card bg-outline">
        <div class="card-icon">
            <div class="circle circle-sky">
                <li class="ico ico-classroom c-sky"></li>
            </div>
        </div>
        <div class="card-content">
            <div class="card-header">
                <h1><?=totalKelas()['total']?></h1>
            </div>
            <div class="card-text c-gray">
                <span>Total Kelas</span>
            </div>
        </div>
    </div>
</section>
<?php include '../templates/footer.php';?>