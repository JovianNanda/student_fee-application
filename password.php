<?php
$title = "SPP | Ubah Password";
include ("templates/header.php");

if (isGuest() ) {
    redirect("");
}

if (isPost() and isset($_POST['change'])){
    changePassword($_POST);
}
?>
<link rel="stylesheet" href="assets/css/page/form.css">
<div class="wrapper" style="width: 80%;">
    <div class="change-password" style="padding: 15px 35px;">
        <form action="" method="post">
            <h1 style="font-size: 1.5rem; margin-bottom: 25px;">Ganti Password <?= ucfirst(getUName()) ?></h1>
            <?php alert();?>
            <div class="input-group">
                <div class="input-layout" style="margin-bottom: 20px;">
                    <label for="passwordLama">Password Lama</label>
                    <div class="input-icon" >
                        <input type="password" id="passwordLama" name="passwordLama" class="input c-success"
                               placeholder="Password Lama">
                        <button type="button" class="button button-transparent icon no-border"
                                data-toggle="password">
                            <li class="ico ico-eye c-black"></li>
                        </button>
                    </div>
                </div>
            </div>
            <div class="wrapper-grid" style="margin : 0">
                <div class="input-group" style="width: 47.5%">
                    <div class="input-layout" style="margin-bottom: 20px;">
                        <label for="passwordBaru">Password Baru</label>
                        <div class="input-icon" >
                            <input type="password" id="passwordBaru" name="passwordBaru" class="input c-success"
                                   placeholder="Password">
                            <button type="button" class="button button-transparent icon no-border"
                                    data-toggle="password">
                                <li class="ico ico-eye c-black"></li>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="input-group" style="width: 48%">
                    <div class="input-layout" style="margin-bottom: 20px;">
                        <label for="konfirmPass">Konfirmasi Password</label>
                        <div class="input-icon" >
                            <input type="password" id="konfirmPass" name="konfirmPass" class="input c-success"
                                   placeholder="Konfirmasi Password Baru">
                            <button type="button" class="button button-transparent icon no-border"
                                    data-toggle="password">
                                <li class="ico ico-eye c-black"></li>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button class="button bg-green" name="change" id="change" style="width:100%; height: 50px; font-size: 1rem">Ganti Password</button>
        </form>
    </div>
</div>

<script src="assets/js/functions.js"></script>
<?php
    include ("templates/footer.php");
?>
