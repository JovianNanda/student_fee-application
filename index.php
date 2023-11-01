<?php 
require "bootstrap.php";

if (!isGuest()) {
    redirect("dashboard");
}else {
    redirect("login.php");
}
?>