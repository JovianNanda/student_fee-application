<?php

function active(String $key){
    $filePath = explode('/', $_SERVER['REQUEST_URI']);
    $filePath = $filePath[2];

    echo ($filePath == $key) ? "active" : "";
}

function strToDate($format, $time)
{
    return date($format, strtotime($time));
}

/** Mengubah Tanggal Pembayaran dari angka menjadi string
 * yang dapat dilihat oleh user menggunakan const BULAN yang
 * sudah tersedia jadi Bulan yang dilihat oleh user berbahasa
 * indonesia.
 */
function tanggalPembayaran($data){
    return strToDate("d", $data) . " " .BULAN[ltrim(strToDate("m",$data), '0')-1]." ". strToDate("Y", $data);
}
function toRupiah(Int $nominal, Bool $withRp = true)
{
    if (!$withRp) return  number_format($nominal, 0, ',',',');

    return  "Rp " . number_format($nominal, 0, ',', '.');
}

function isOdd(Int $num)
{
    if ($num % 2 !== 0) {
        return true;
    }
    return false;
}

function stringBool($string){
    if($string == "true") return true;
    if($string == "false") return false;
}

// GET URL FUNCTIONS

function homeUrl()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . searchUrl(3);
}
function searchUrl($urlArrayNum = 0)
{
    $pathInPieces = explode('\\', __DIR__);
    return $pathInPieces[$urlArrayNum];
}

