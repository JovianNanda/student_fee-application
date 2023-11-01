<?php
session_start();
function setAlert(string $pesan, string $tipe, string $icon = null)
{
    $_SESSION['alert'] = [
        'pesan' => $pesan,
        'tipe' => $tipe,
        'icon' => $icon,
    ];
}

function alert()
{
    if (isset($_SESSION['alert'])) {

        if (!$_SESSION['alert']['icon']) {
            $_SESSION['alert']['icon'] = "reset";
        }

        echo sprintf('<div class="alert %s"> <li class="ico %s"></li> <div>%s</div> <button type="button" class="button button-close" alert-dismiss><li class="ico ico-times"></li></button> </div>', $_SESSION['alert']['tipe'], $_SESSION['alert']['icon'], $_SESSION['alert']['pesan']);
        unset($_SESSION['alert']);
    }

}

function setIsInvalid(array $tags, array $pesan)
{
    $_SESSION['is-invalid'] = [
        'pesan' => $pesan,
        'tag' => $tags,
    ];
}

function isInvalid()
{
    if (isset($_SESSION['is-invalid'])) {
        $pesan = $_SESSION['is-invalid']['pesan'];
        $tags = $_SESSION['is-invalid']['tag'];

        $pattern = "{{{tag}}}";
        echo "<div style='display:none'>";
        for ($i = 0; $i < array_key_last($tags) + 1; $i++) {
            $pesanBaru = $pesan[$i] ?? null;
            $tagBaru = str_replace("#", "", $tags[$i] ?? null);
            echo sprintf("<script>isInvalid('%s', '%s')</script>", $tags[$i] ?? null, preg_replace($pattern, ucfirst($tagBaru), $pesanBaru));
            unset($_SESSION['is-invalid']);
        }
        echo "</div>";
    }

}

function setValue(array $tags, array $pesan)
{
    $_SESSION['value'] = [
        'pesan' => $pesan,
        'tag' => $tags,
    ];
}

function value()
{
    if (isset($_SESSION['value'])) {
        $pesan = $_SESSION['value']['pesan'];
        $tags = $_SESSION['value']['tag'];

        $pattern = "{{{tag}}}";
        echo "<div style='display:none'>";
        for ($i = 0; $i < array_key_last($tags) + 1; $i++) {
            $pesanBaru = $pesan[$i] ?? null;
            $tagBaru = str_replace("#", "", $tags[$i] ?? null);
            echo sprintf("<script>setValue('%s', '%s')</script>", $tags[$i] ?? null, preg_replace($pattern, ucfirst($tagBaru), $pesanBaru));
            unset($_SESSION['value']);
        }
        echo "</div>";
    }

}

function printValidation()
{
    echo "<js>";
    isInvalid();
    value();
    echo "</js>";
}