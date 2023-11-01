<?php

function query($query, $returnFetch = true, $returnInArray = false)
{
    global $koneksi;
    $query = mysqli_query($koneksi, $query);

    $row_data = [];

    if ($returnFetch) {
        while ($rows = mysqli_fetch_assoc($query)) {
            $row_data[] = $rows;
        }

        if (count($row_data) === 1) {
            if ($returnInArray) {
                return $row_data;
            } else {
                return $row_data[0];
            }
        }
        return $row_data;
    }


    return $query;

}


/*
 *
 * Mengubah angka bulan menjadi index bulan yg digunakan untuk constant
 * */
function toBulanIndex($bulan)
{
    return ltrim(strToDate("m", $bulan), 0) - 1;
}

// * GET FUNCTIONS
function getSiswaByNIS($nis)
{
    if (is_numeric($nis)) {
        return query("SELECT * FROM siswa INNER JOIN kelas USING (id_kelas) WHERE nis = $nis");
    } else {
        return false;
    }

}

function getSiswaByKelas($idKelas, $action = false)
{
    return query("SELECT * FROM siswa WHERE id_kelas = $idKelas", $action);
}

function getSiswaByJurusan($jurusan)
{
    return query("SELECT * FROM siswa JOIN kelas USING(id_kelas) WHERE jurusan = '$jurusan'", false);
}

function getSiswaByKelasOrJurusan($idKelas, $jurusan, $redirect)
{
    if ($idKelas and $jurusan) {
        setAlert("Pilih antara Kelas atau Jurusan", "floating-alert bg-red", "ico-exclamation-circle");
        redirect($redirect);
        return false;
    }
    if ($idKelas) {
        return getSiswaByKelas($idKelas);
    }
    if ($jurusan) {
        return getSiswaByJurusan($jurusan);
    }
    return false;
}

// * ALL USEABLE FUNCTIONS FOR WEBSITES
function login($data)
{
    $username = $data['username'];
    $password = $data['password'];

    validate($data, [
        'username' => [RULE_REQUIRED],
        'password' => [RULE_REQUIRED],
    ]);

    if (isErrors()) return false;

    $login = query("SELECT * FROM petugas WHERE username = '$username'");
    $sessionAuth = $login['level_user'] ?? null;
    $sessionName = $login['username'] ?? null;
    $sessionUid = $login['id_petugas'] ?? null;

    if (!$login) {
        $login = query("SELECT * FROM siswa WHERE nis = '$username'");
        $sessionName = $login['nama'] ?? null;
        $sessionUid = $login['nis'] ?? null;
        $sessionAuth = "siswa";
    }

    // if account doesnt found
    if (!$login) {
        setAlert("Nama Akun Atau Password Salah!", "floating-alert bg-red", "ico-exclamation-circle");
        return false;
    }

    if (password_verify($password, $login['password'])) {
        // Set Session
        $_SESSION['login'] = ["auth" => $sessionAuth, "nama" => $sessionName, "uid" => $sessionUid];
        setAlert("Login Success!", "floating-alert bg-success mx-auto", "ico-check-circle");
        redirect("dashboard/");
        exit();
    } // if password salah
    else {
        setAlert("Nama Akun Atau Password Salah!", "floating-alert bg-red", "ico-exclamation-circle");
        return false;
    }
}

function getKelasBySemester($semester, $data = null)
{
    $kelas = $semester / 2;
    if($kelas < 0 && $kelas > -1){
        $kelas = 1;
    }
    if($kelas > 3){
        return "Angkatan ". getAngkatan($semester);
    }
    $kelasRomawi = match (intval(ceil($kelas))) {
        1 => "X",
        2 => "XI",
        3 => "XII",
        default => "Class error!",
    };

    if (!is_null($data)) {
        return $kelasRomawi . " " . $data;
    } else {
        return $kelasRomawi;
    }

}

function getKelasSaatIni($semesterSaatIni)
{
    return getKelasBySemester($semesterSaatIni);
}

function getSemester($angkatan)
{
    $startYear = date("Y-m-d", mktime(0, 0, 0, 1, 1, date("Y")));
    $bulanSaatIni = date("m");
    $hasilAngka = intval($startYear) - intval($angkatan);

    if (in_array($bulanSaatIni, range(1, 7))) {
        return $hasilAngka * 2;
    } else {
        return $hasilAngka * 2 - 1;
    }
}
function getAngkatan($semester){
    $startYear = date("Y-m-d", mktime(0, 0, 0, 1, 1, date("Y")));

    $perhitungan = $semester / 2;
    $angkatan = intval($startYear) - ceil($perhitungan);

    return $angkatan;
}

/*
 * Mencari Perbedaan Bulan antar tahun sekarang dan tahun yg kemarin
 * ex: 2023 dan 2022 perbedaan bulannya adalah 12 bulan
 * */
function perbedaanBulan(int $tahunSekarang, int $tahunKemarin)
{

    $tahunSekarang = $tahunSekarang * 12;
    $tahunKemarin = $tahunKemarin * 12;

    return $tahunSekarang - $tahunKemarin;
}

function batasWaktu($bulan, $tahun, $returnNum = false)
{

    $tanggalAkhirPembayaran = 10;

    if (!$returnNum) {
        $hasil = "$tanggalAkhirPembayaran " . BULAN[$bulan] . " $tahun";
    } else {
        $hasil = "$tahun-$bulan-$tanggalAkhirPembayaran";
    }

    return $hasil;
}

function cekBatasWaktu($bulan, $tahun)
{
    $jatuhTempo = strToDate("Y-m-d", batasWaktu($bulan + 1, $tahun, true));
    $hariIni = date("Y-m-d");
    if ($hariIni > $jatuhTempo) {
        return true;
    } else {
        return false;
    }
}

function totalSiswa()
{
    return query("SELECT COUNT(nis) as total FROM siswa");
}

function totalKelas()
{
    return query("SELECT COUNT(id_kelas) as total FROM kelas");
}

/**
 * FUNCTION PEMBAYARAN
 *
 * tanpa ajax
 * */

function getBulanDanTahun($angkatan, int $semester)
{

    // * MENCARI BULAN
    [$bulanMulai, $bulanAkhir, $dbMulai, $dbAkhir] = [0, 6, 1, 6];
    if (isOdd($semester)) {
        [$bulanMulai, $bulanAkhir, $dbMulai, $dbAkhir] = [6, 12, 7, 12];
    }

    for ($i = $bulanMulai; $i <= $bulanAkhir; $i++) {
        $bulan[] = $i;
    }

    // * MENCARI TAHUN
    // PENGURANGAN 6 BULAN

    $decrementBy = 6;
    $startYear = date("Y-m-d", mktime(0, 0, 0, 1, 1, date("Y")));

    // ! Berapa Bulan Lalu Semester Dimulai
    $bulanSemesterMulai = -perbedaanBulan(intval($startYear), $angkatan);

    //    Perulangan Untuk mengurangi bulan tergantung dengan semesternya
    //    ex : Jika Semester 2  = -24 bulan
    //    Dikurangi berdasarkan semester / jika semester 1 bulanSemesterMulai yg berisi 36 - 6 maka 30 bulan sebelum 1 januari tahun ini
    for ($i = 0; $i < $semester; $i++) {
        $bulanSemesterMulai += $decrementBy;
    }

    $year = date("Y", strtotime($startYear . "$bulanSemesterMulai Months"));

    for ($i = 0; $i < 6; $i++) {
        $tahun[] = $year;
    }

    return [
        "bulan" => $bulan,
        "tahun" => $tahun,
        "bulanMulai" => $bulanMulai,
        "bulanAkhir" => $bulanAkhir,
        "dbMulai" => $dbMulai,
        "dbAkhir" => $dbAkhir
    ];
}

function getPembayaran($nis, int $semester)
{
    $angkatanSiswa = query("SELECT angkatan FROM siswa JOIN kelas USING(id_kelas) WHERE nis =$nis");

    //    Ambil Bulan Dan Tahun
    $dataBulanTahun = getBulanDanTahun($angkatanSiswa['angkatan'], $semester);
    // GET DATA
    $bulanMulai = $dataBulanTahun['bulanMulai'] + 1;
    $bulanAkhir = $dataBulanTahun['bulanAkhir'];
    $year = $dataBulanTahun['tahun'][0];
    $tahun = $dataBulanTahun['tahun'];
    $bulan = $dataBulanTahun['bulan'];

    $data = getPembayaranBySiswa($nis, $bulanMulai, $bulanAkhir, $year);

    if (mysqli_num_rows($data) > 0) {
        while ($dataPembayaran = mysqli_fetch_assoc($data)) {
            // Data Pembayaran dikurangi 1 supaya index Sama seperti const BULAN dimana mulai dari 0 bukan 1
            $bulanPembayaran[] = $dataPembayaran['bulan'] - 1;
            $tanggalPembayaran[] = $dataPembayaran['tanggal_pembayaran'];
            $nominal[] = $dataPembayaran['nominal'];
            $tahunPembayaran[] = $dataPembayaran['tahun'];
        }

        $totalBayarSemester = query("SELECT sum(nominal) as total FROM pembayaran WHERE bulan BETWEEN $bulanMulai AND $bulanAkhir AND tahun = '$year' AND nis = $nis");

    } else {
        $bulanPembayaran[] = "0";
        $tanggalPembayaran[] = "-";
        $nominal[] = 0;
        $tahunPembayaran[] = "-";
        $totalBayarSemester['total'] = 0;
    }

    return [
        "bulan" => $bulan,
        "tahun" => $tahun,
        "bulanPembayaran" => $bulanPembayaran,
        "tanggalPembayaran" => $tanggalPembayaran,
        "nominal" => $nominal,
        "tahunPembayaran" => $tahunPembayaran,
        "totalBayar" => $totalBayarSemester['total'],
    ];
}

function getPembayaranByKelas($idKelas, $semester)
{
    $angkatan = query("SELECT angkatan FROM kelas WHERE id_kelas = $idKelas");
    $bulanTahun = getBulanDanTahun($angkatan['angkatan'], $semester);

    // GET DATA
    $bulanMulai = $bulanTahun['bulanMulai'];
    $bulanAkhir = $bulanTahun['bulanAkhir'];
    $dbMulai = $bulanTahun['dbMulai'];
    $dbAkhir = $bulanTahun['dbAkhir'];
    $year = $bulanTahun['tahun'][0];

    $siswa = query("SELECT * FROM siswa JOIN kelas USING(id_kelas) WHERE id_kelas = $idKelas", true, true);
    $nisSiswa = implode(',', array_map(function ($value) {
        return $value['nis'];
    }, $siswa));


    $data = [];
    foreach ($siswa as $value) {

        $data[$value['nis']] = [];
        $data[$value['nis']]['nama'] = $value['nama'];
        $data[$value['nis']]['pembayaran'] = [];

        for ($i = $bulanMulai; $i < $bulanAkhir; $i++) {
            $bulan = substr(BULAN[$i], 0, 3);
            $data[$value['nis']]['pembayaran'][$bulan] = false;
        }
    }

    $pembayaran = query("SELECT * FROM pembayaran WHERE nis IN ($nisSiswa) AND tahun = '$year' AND (bulan BETWEEN '$dbMulai' AND '$dbAkhir')", false);

    foreach ($pembayaran as $value) {
        $bulan = substr(BULAN[$value['bulan'] - 1], 0, 3);
        $data[$value['nis']]['pembayaran'][$bulan] = true;
    }

    return $data;
}


function getPembayaranBySiswa($nis, $bulanMulai, $bulanAkhir, $tahun)
{
    return query("SELECT * FROM pembayaran WHERE bulan BETWEEN $bulanMulai AND $bulanAkhir AND tahun = '$tahun' AND nis = $nis ORDER by bulan", false);
}

function totalPembayaran()
{
    return query("SELECT sum(nominal) AS total FROM pembayaran");
}

function getSiswaSpp($nis)
{
    return query("SELECT spp.id_spp, spp.harga_spp, spp.jenis_kelas FROM siswa INNER JOIN kelas USING(id_kelas) INNER JOIN spp using(id_spp) WHERE nis = $nis");
}

function pembayaranTerakhir($nis)
{
    return query("SELECT * FROM pembayaran WHERE nis = $nis ORDER BY tahun DESC, bulan DESC LIMIT 1");
}

function totalDataHistory($search)
{
    return mysqli_num_rows(query("SELECT * FROM pembayaran JOIN siswa using(nis) WHERE siswa.nama LIKE '%$search%' ORDER BY tanggal_pembayaran DESC ", false));
}

function dataHistoryByTanggal($start, $end, $kelas = null, $jurusan = null)
{
    $start = sanitize($start);
    $end = sanitize($end);
    $kelas = sanitize($kelas);
    $jurusan = sanitize($jurusan);

    $query = "";
    $total = "";

    if (!$jurusan and !$kelas and $start and $end) {

        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE tanggal_pembayaran BETWEEN '$start' AND '$end' ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE tanggal_pembayaran BETWEEN '$start' AND '$end'";
    }
    if ($kelas and $start and $end) {
        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE id_kelas = $kelas AND tanggal_pembayaran BETWEEN '$start' AND '$end'ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE  id_kelas = $kelas AND tanggal_pembayaran BETWEEN '$start' AND '$end' ";
    }
    if ($jurusan and $start and $end) {
        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan' AND tanggal_pembayaran BETWEEN '$start' AND '$end' ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan' AND tanggal_pembayaran BETWEEN '$start' AND '$end' ";
    }

//    Jika Salah satu tanggal saja
    if (!$jurusan and !$kelas and $start and !$end or !$jurusan and !$kelas and $end and !$start) {
        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE tanggal_pembayaran = '$start' OR tanggal_pembayaran = '$end' ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran WHERE  tanggal_pembayaran = '$start' OR tanggal_pembayaran = '$end'";

    }
    if ($kelas and $start and !$end or $kelas and $end and !$start) {
        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE id_kelas = $kelas AND tanggal_pembayaran = '$start' OR  id_kelas = $kelas AND tanggal_pembayaran = '$end' ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE id_kelas = $kelas AND tanggal_pembayaran = '$start' OR id_kelas = $kelas AND tanggal_pembayaran = '$end' ";
    }
    if ($jurusan and $start and !$end or $jurusan and $end and !$start) {
        $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan' AND tanggal_pembayaran = '$start' OR jurusan = '$jurusan' AND tanggal_pembayaran = '$end'  ORDER BY tanggal_pembayaran DESC";

        $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan' AND tanggal_pembayaran = '$start' OR jurusan = '$jurusan' AND tanggal_pembayaran = '$end'";
    }

    if ($query) {
        $query = query($query, false);
        $total = query($total);
    } else {
        if ($kelas) {
            $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE id_kelas = $kelas ORDER BY tanggal_pembayaran DESC";
            $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE id_kelas =$kelas";
        }
        if ($jurusan) {
            $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan' ORDER BY tanggal_pembayaran DESC";
            $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) WHERE jurusan = '$jurusan'";
        }
        if (!$kelas and !$jurusan) {
            $query = "SELECT * FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas) ORDER BY tanggal_pembayaran DESC";
            $total = "SELECT sum(nominal) AS total FROM pembayaran JOIN siswa using(nis) JOIN kelas using(id_kelas)";
        }

        $query = query($query, false);
        $total = query($total);
    }

    return [$query, $total];
}

function dataHistory($start, $limit, $search = null)
{

    //    Sanitize All
    $start = sanitize($start);
    $limit = sanitize($limit);
    $search = sanitize($search);

    if ($start < 0 or $limit < 1 or !is_numeric($start) or !is_numeric($limit)) {
        setAlert("Error data tidak ditemukan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("histori");
        return false;
    }

    $total = mysqli_num_rows(query("SELECT * FROM pembayaran JOIN siswa using(nis) WHERE siswa.nama LIKE '%$search%' ORDER BY tanggal_pembayaran DESC", false));

    $start = $start - 1;
    $start = $start * $limit;

    $data = query("SELECT * FROM pembayaran JOIN siswa using(nis) WHERE siswa.nama LIKE '%$search%' ORDER BY tanggal_pembayaran DESC, tahun DESC, bulan DESC LIMIT $start, $limit", false);

    $pagination = $total / $limit;

    return [$data, intval(ceil($pagination)), $total];
}

function insertPembayaran($data, $semester, $strictMode = true)
{

    $bulanPembayaran = $data['bulan'];
    $tahunPembayaran = $data['tahun'];
    $tanggalPembayaran = date('Y-m-d');
    $nis = $data['nis'];
    $idPetugas = getUID();

    if ($semester < 1 or $semester > 6) {
        setAlert("Semester Error!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("pembayaran?nis=$nis");
        return false;
    }

    $spp = getSiswaSpp($nis);
    $hargaSpp = $spp['harga_spp'];

    $cek = query("SELECT * FROM pembayaran WHERE bulan = $bulanPembayaran AND tahun = $tahunPembayaran AND nis = $nis", false);

    $angkatan = query("SELECT angkatan FROM siswa INNER JOIN kelas USING(id_kelas) WHERE nis = $nis");

    // Jika Data Sudah Ada
    if (mysqli_num_rows($cek) > 0) {
        setAlert("Data Pembayaran Sudah Ada!", "floating-alert bg-red", "ico-exclamation-triangle");
        return false;
    }

    if ($strictMode) {
        // Cek pembayaran terakhir
        $pembayaranTerakhir = pembayaranTerakhir($nis);
        $bulanTerakhir = $pembayaranTerakhir["bulan"] ?? 6;
        $tahunTerakhir = $pembayaranTerakhir["tahun"] ?? $angkatan["angkatan"];

        $tahunBaru = $tahunTerakhir + 1;
        // Jika pembayaran 1 bulan lebih depan dari pembayaran terakhir
        if ($bulanTerakhir < 12 and $bulanPembayaran == $bulanTerakhir + 1 and $tahunPembayaran == $tahunTerakhir) {
            $query = query("INSERT INTO pembayaran VALUES ('', $idPetugas, $nis, '$tanggalPembayaran', $tahunPembayaran, $bulanPembayaran, $hargaSpp)", false);
        } else if ($bulanTerakhir == 12 and $bulanPembayaran == 1 and $tahunPembayaran > $tahunTerakhir and $tahunPembayaran == $tahunBaru) {
            $query = query("INSERT INTO pembayaran VALUES ('', $idPetugas, $nis, '$tanggalPembayaran', $tahunPembayaran, $bulanPembayaran, $hargaSpp)", false);
        } else {
            setAlert("Pembayaran Terloncat!", "floating-alert bg-warning", "ico-exclamation-triangle");
            redirect("pembayaran/?nis=$nis&semester=$semester");
            return false;
        }
    } else {

        $query = query("INSERT INTO pembayaran VALUES ('', $idPetugas, $nis, '$tanggalPembayaran', $tahunPembayaran, $bulanPembayaran, $hargaSpp)", false);
    }

    if ($query) {
        setAlert("Pembayaran Berhasil!", "floating-alert bg-success", "ico-check-circle");
        redirect("pembayaran/?nis=$nis&semester=$semester");
        return false;
    }
    return true;
}

/**
 *  FUNCTION SISWA
 *
 *  Function Select, Delete, Update Siswa
 *
 */

function getAllSiswa($start, $limit, $search = null)
{
    $start = sanitize($start);
    $limit = sanitize($limit);
    $search = sanitize($search);

    if ($start < 0 or $limit < 1 or !is_numeric($start) or !is_numeric($limit)) {
        setAlert("Error data tidak ditemukan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("siswa");
        return false;
    }

    $total = mysqli_num_rows(query("SELECT * FROM siswa INNER JOIN kelas USING(id_kelas) WHERE nama LIKE '%$search%' OR nis LIKE '%$search%'", false));

    $start = $start - 1;
    $start = $start * $limit;

    $data = query("SELECT * FROM siswa INNER JOIN kelas USING(id_kelas) WHERE nama LIKE '%$search%' OR nis LIKE '%$search%' LIMIT $start, $limit", false);

    $pagination = $total / $limit;

    return [$data, intval(ceil($pagination)), $total];
}

function insertSiswa(array $data)
{

    // Get All Data
    $nis = $data['nis'];
    $nisn = $data['nisn'];
    $nama = $data['nama'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $kelas = $data['kelas'] ?? "";
    $agama = $data['agama'] ?? "";
    $jk = $data['jk'] ?? "";
    $noTelp = $data['noTelp'];
    $alamat = $data['alamat'];
    validate($data, [
        'nis' => [RULE_REQUIRED, RULE_NUM, RULE_UNIQUE],
        'nisn' => [RULE_REQUIRED, RULE_NUM, RULE_UNIQUE],
        'nama' => [RULE_REQUIRED],
        'email' => [RULE_REQUIRED, RULE_EMAIL, RULE_UNIQUE],
        'password' => [RULE_REQUIRED, [RULE_MIN, "min" => 6]],
        'kelas' => [RULE_REQUIRED],
        'agama' => [RULE_REQUIRED],
        'jk' => [[RULE_REQUIRED, "required" => "Jenis Kelamin"]],
        'noTelp' => [RULE_REQUIRED, RULE_NUM],
        'alamat' => [RULE_REQUIRED],
    ], "siswa");

    if (isErrors()) return false;

    $queryInsert = query("INSERT INTO siswa VALUES($nis, $nisn, '$nama', '$email', '$password', $kelas, '$agama', '$jk', $noTelp, '$alamat')", false);

    if ($queryInsert) {
        setAlert("Data Siswa Berhasil Dimasukkan!", "floating-alert bg-success", "ico-check-circle");
        redirect("siswa");
        return false;
    } else {
        setAlert("Data Siswa Gagal Dimasukkan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("siswa");
        return false;
    }

}

/**
 * Function edit data siswa
 */
function editSiswa(array $data)
{

    // Get All Data
    $nis = $data['nis'];
    $nisLama = $data['nis_lama'];
    $nisn = $data['nisn'];
    $nama = $data['nama'];
    $email = $data['email'];
    $passwordBaru = $data['password'];
    $kelas = $data['kelas'] ?? "";
    $agama = $data['agama'] ?? "";
    $jk = $data['jk'] ?? "";
    $noTelp = $data['noTelp'];
    $alamat = $data['alamat'];

    $dataLama = query("SELECT * FROM siswa WHERE nis = $nisLama");
    $nisnLama = $dataLama['nisn'];
    $passwordLama = $dataLama['password'];
    $emailLama = $dataLama['email'];

    $validate = [
        'nis' => [[RULE_REQUIRED, "required" => "NIS"], [RULE_NUM, "num" => "NIS"]],
        'nisn' => [[RULE_REQUIRED, "required" => "NISN"], [RULE_NUM, "num" => "NISN"]],
        'nama' => [RULE_REQUIRED],
        'email' => [RULE_REQUIRED, RULE_EMAIL],
        'password' => [[RULE_MIN, "min" => 6]],
        'kelas' => [RULE_REQUIRED],
        'agama' => [RULE_REQUIRED],
        'jk' => [[RULE_REQUIRED, "required" => "Jenis Kelamin"]],
        'noTelp' => [[RULE_REQUIRED, "required" => "No Telepon"], [RULE_NUM, "num" => "No Telepon"]],
        'alamat' => [RULE_REQUIRED],
    ];

    if ($nisLama != $nis) {
        $validate["nis"] = [[RULE_REQUIRED, "required" => "NIS"], [RULE_NUM, "num" => "NIS"], RULE_UNIQUE];
    }
    if ($nisnLama != $nisn) {
        $validate["nisn"] = [[RULE_REQUIRED, "required" => "NISN"], [RULE_NUM, "num" => "NISN"], RULE_UNIQUE];
    }
    if ($emailLama != $email) {
        $validate["email"] = [RULE_REQUIRED, RULE_EMAIL, RULE_UNIQUE];
    }

    if ($passwordBaru) {
        $password = password_hash($passwordBaru, PASSWORD_DEFAULT);
    } else {
        $password = $passwordLama;
    }

    validate($data, $validate, "siswa");

    if (isErrors()) return false;


    $queryUpdate = query("UPDATE siswa SET nis=$nis, nisn=$nisn, nama='$nama', email='$email', password='$password', id_kelas=$kelas, agama='$agama', jk='$jk', no_telp=$noTelp, alamat='$alamat' WHERE nis=$nisLama", false);

    if ($queryUpdate) {
        setAlert("Data Siswa Berhasil Diubah!", "floating-alert bg-sky", "ico-check-circle");
        redirect("siswa");
        return false;
    } else {
        setAlert("Data Siswa Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("siswa");
        return false;
    }

}

function deleteSiswa($data)
{

    $nis = $data['nis'];
    $queryDelete = query("DELETE FROM siswa WHERE nis = $nis", false);

    if ($queryDelete) {
        setAlert("Data Siswa Berhasil Dihapus!", "floating-alert bg-green", "ico-info-circle");
        redirect("siswa");
        return false;
    } else {
        setAlert("Data Siswa Gagal Dihapus!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("siswa");
        return false;
    }
}

function totalDataSiswa($search)
{
    return mysqli_num_rows(query("SELECT * FROM siswa INNER JOIN kelas USING(id_kelas) WHERE nama LIKE '%$search%' OR nis LIKE '%$search%'", false));
}

/**
 * FUNCTION KELAS
 *
 * Function Select, Delete, Update Kelas
 *
 */
function getKelasById($id)
{
    return query("SELECT * FROM kelas WHERE id_kelas = $id");
}
function getAllKelas()
{
    return query("SELECT * FROM kelas ORDER BY angkatan", false);
}

function totalDataKelasSpp($search)
{
    return mysqli_num_rows(query("SELECT * FROM kelas JOIN spp using(id_spp) WHERE kelas.kelas LIKE '%$search%'  ORDER BY angkatan DESC", false));
}

function getKelasAndSPP($start, $limit, $search = null)
{
    //    Sanitize All
    $start = sanitize($start);
    $limit = sanitize($limit);
    $search = strtolower(sanitize($search));
    $tahunAngkatan = "";
    $queryAngkatan = "";

    if ($search) {
        $searchFirst = explode(' ', $search);
        $condition = false;
        switch ($search) {
            case(in_array("xii", $searchFirst)) :
                $tahunAngkatan = date("Y") - 3;
                $condition = true;
                break;
            case(in_array("xi", $searchFirst)) :
                $tahunAngkatan = date("Y") - 2;
                $condition = true;
                break;
            case(in_array("x", $searchFirst)) :
                $tahunAngkatan = date("Y") - 1;
                $condition = true;
                break;
        }
    }

    if ($start < 0 or $limit < 1 or !is_numeric($start) or !is_numeric($limit)) {
        setAlert("Error data tidak ditemukan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("kelas");
        return false;
    }

    if ($tahunAngkatan) {
        $queryAngkatan = "angkatan = $tahunAngkatan AND ";
        if ($condition) {
            $search = array_splice($searchFirst, 1);
            $search = implode(' ', $search);
        }
    }
    $total = mysqli_num_rows(query("SELECT * FROM kelas JOIN spp using(id_spp) WHERE $queryAngkatan kelas.kelas LIKE '%$search%'  ORDER BY angkatan DESC", false));

    $start = $start - 1;
    $start = $start * $limit;

    $data = query("SELECT * FROM kelas JOIN spp using(id_spp) WHERE  $queryAngkatan kelas.kelas LIKE '%$search%' ORDER BY angkatan DESC LIMIT $start, $limit", false);

    $pagination = $total / $limit;

    return [$data, intval(ceil($pagination)), $total];
}

function insertKelas($data)
{
    // Get All Data
    $spp = $data['spp'] ?? null;
    $kelas = $data['kelas'];
    $jurusan = $data['jurusan'] ?? "";
    $jenisKelas = $data['jenisKelas'] ?? "";
    $angkatan = $data['angkatan'];

    validate($data, [
        'spp' => [RULE_REQUIRED],
        'kelas' => [RULE_REQUIRED],
        'jurusan' => [RULE_REQUIRED],
        'jenisKelas' => [[RULE_REQUIRED, "required" => "Jenis kelas"]],
        'angkatan' => [RULE_REQUIRED, RULE_NUM, [RULE_MAX, "max" => 4], [RULE_MIN, "min" => 4]],
    ]);

    if (isErrors()) return false;


    $check = query("SELECT * FROM kelas WHERE kelas = '$kelas' AND jurusan = '$jurusan' AND jenis_kelas = '$jenisKelas' AND angkatan = $angkatan");

    if ($check) {
        setAlert("Kelas Sudah Ada!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("kelas/insert.php");
        return false;
    }
    $queryInsert = query("INSERT INTO kelas VALUES(null, $spp, '$kelas', '$jurusan', '$jenisKelas', $angkatan)", false);

    if ($queryInsert) {
        setAlert("Data Kelas Berhasil Dimasukkan!", "floating-alert bg-success", "ico-check-circle");
        redirect("kelas");
        return false;
    } else {
        setAlert("Data Kelas Gagal Dimasukkan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("kelas");
        return false;
    }

}

function editKelas($data)
{
    // Get All Data
    $idKelas = $data['idkelas'];
    $spp = $data['spp'] ?? null;
    $kelas = $data['kelas'];
    $jurusan = $data['jurusan'] ?? "";
    $jenisKelas = $data['jenisKelas'] ?? "";
    $angkatan = $data['angkatan'];

    validate($data, [
        'spp' => [RULE_REQUIRED],
        'kelas' => [RULE_REQUIRED],
        'jurusan' => [RULE_REQUIRED],
        'jenisKelas' => [[RULE_REQUIRED, "required" => "Jenis kelas"]],
        'angkatan' => [RULE_REQUIRED, RULE_NUM, [RULE_MAX, "max" => 4], [RULE_MIN, "min" => 4]],
    ]);

    if (isErrors()) return false;


    $check = query("SELECT * FROM kelas WHERE kelas = '$kelas' AND jurusan = '$jurusan' AND jenis_kelas = '$jenisKelas' AND angkatan = $angkatan");

    if ($check) {
        setAlert("Kelas Sudah Ada!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("Kelas");
        return false;
    }

    $queryUpdate = query("UPDATE kelas SET id_spp = $spp, kelas = '$kelas', jurusan ='$jurusan', jenis_kelas = '$jenisKelas', angkatan = $angkatan WHERE id_kelas = $idKelas", false);

    if ($queryUpdate) {
        setAlert("Data Kelas Berhasil Diubah!", "floating-alert bg-success", "ico-check-circle");
    } else {
        setAlert("Data Kelas Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-triangle");
    }
    redirect("Kelas");
    return false;

}

function deleteKelas($data)
{
    $id = $data['idKelas'];
    $queryDelete = query("DELETE FROM kelas WHERE id_kelas = $id", false);

    if ($queryDelete) {
        setAlert("Data Kelas Berhasil Dihapus!", "floating-alert bg-green", "ico-info-circle");
    } else {
        setAlert("Data Kelas Gagal Dihapus!", "floating-alert bg-danger", "ico-exclamation-circle");
    }
    redirect("kelas");
    return false;
}

/**
 * FUNCTION SPP
 *
 * Function Select, Delete, Update SPP
 *
 */

function getAllSpp()
{
    return query("SELECT * FROM spp ORDER BY harga_spp", false);
}

function insertSpp($data)
{
    // Get All Data
    $harga = $data['harga'];
    $jenisKelas = $data['jenisKelas'] ?? "";
    $angkatan = $data['angkatan'];

    validate($data, [
        'harga' => [RULE_REQUIRED],
        'jenisKelas' => [[RULE_REQUIRED, "required" => "Jenis kelas"]],
        'angkatan' => [RULE_REQUIRED, RULE_NUM, [RULE_MAX, "max" => 4], [RULE_MIN, "min" => 4]],
    ]);

    if (isErrors()) return false;


    $check = query("SELECT * FROM spp WHERE harga_spp = $harga AND jenis_kelas = '$jenisKelas' AND tahun_angkatan = $angkatan");

    if ($check) {
        setAlert("Data SPP Sudah Ada!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("spp/insert.php");
        return false;
    }

    $queryInsert = query("INSERT INTO spp VALUES(null, $harga, '$jenisKelas', '$angkatan')", false);

    if ($queryInsert) {
        setAlert("Data SPP Berhasil Dimasukkan!", "floating-alert bg-success", "ico-check-circle");
        redirect("spp");
        return false;
    } else {
        setAlert("Data SPP Gagal Dimasukkan!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("spp");
        return false;
    }

}

function editSpp($data)
{
    // Get All Data
    $idSpp = $data['id_spp'];
    $harga = $data['harga'];
    $jenisKelas = $data['jenisKelas'] ?? "";
    $angkatan = $data['angkatan'];

    validate($data, [
        'harga' => [RULE_REQUIRED],
        'jenisKelas' => [[RULE_REQUIRED, "required" => "Jenis kelas"]],
        'angkatan' => [RULE_REQUIRED, RULE_NUM, [RULE_MAX, "max" => 4], [RULE_MIN, "min" => 4]],
    ]);

    if (isErrors()) return false;


    $check = query("SELECT * FROM spp WHERE harga_spp = $harga AND jenis_kelas = '$jenisKelas' AND tahun_angkatan = $angkatan");

    if ($check) {
        setAlert("Data SPP Sudah Ada!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("Kelas/insert.php");
        return false;
    }

    $queryUpdate = query("UPDATE spp SET harga_spp = $harga, jenis_kelas = '$jenisKelas', tahun_angkatan = $angkatan WHERE id_spp = $idSpp", false);

    if ($queryUpdate) {
        setAlert("Data SPP Berhasil Diubah!", "floating-alert bg-success", "ico-check-circle");
        redirect("spp");
        return false;
    } else {
        setAlert("Data SPP Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("spp");
        return false;
    }

}

function deleteSpp($data)
{
    $id = $data['idspp'];
    $queryDelete = query("DELETE FROM spp WHERE id_spp = $id", false);

    if ($queryDelete) {
        setAlert("Data SPP Berhasil Dihapus!", "floating-alert bg-green", "ico-info-circle");
        redirect("spp");
        return false;
    } else {
        setAlert("Data SPP Gagal Dihapus!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("spp");
        return false;
    }
}

function getPetugasById($id)
{
    return query("SELECT * FROM petugas WHERE id_petugas = $id");

}

function insertPetugas($data)
{
    $username = $data['username'];
    $password = $data['password'];
    $levelUser = $data['level_User'] ?? "";

    $validate = validate($data, [
        "username" => [RULE_REQUIRED],
        "password" => [RULE_REQUIRED],
        "level_User" => [RULE_REQUIRED],
    ]);

    if (isErrors()) return false;

    $check = query("SELECT * FROM petugas WHERE username = '$username'");

    if ($check) {
        setAlert("Petugas Sudah terdaftar!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("petugas/insert.php");
        return false;
    }

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);

    $queryInsert = query("INSERT INTO petugas VALUES(null, '$username', '$hashedPass', '$levelUser')", false);

    if ($queryInsert) {
        setAlert("Petugas Berhasil Ditambah!", "floating-alert bg-success", "ico-check-circle");
        redirect("petugas");
        return false;
    } else {
        setAlert("Petugas Gagal Ditambah!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("petugas");
        return false;
    }

}

function editPetugas($data)
{
    $username = $data['username'];
    $password = $data['password'];
    $levelUser = $data['level_User'] ?? "";
    $idpetugas = $data['idpetugas'];

    $select = "SELECT * FROM petugas WHERE id_petugas = $idpetugas";
    $dataSiswa = query($select);
    $passwordLama = $dataSiswa['password'];


    if (!$password){
        $hashedPass = $passwordLama;
    }else{
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    }

    $validate = validate($data, [
        "username" => [RULE_REQUIRED],
        "level_User" => [RULE_REQUIRED],
    ]);

    if (isErrors()) return false;


    $queryUpdate = query("UPDATE petugas SET username ='$username',password='$hashedPass', level_user='$levelUser' WHERE id_petugas = $idpetugas", false);

    if ($queryUpdate) {
        setAlert("Petugas Berhasil Diubah!", "floating-alert bg-success", "ico-check-circle");
        if (getUID() == $idpetugas){
            unset($_SESSION['login']);
            redirect('login.php');
        }
        redirect("petugas");
        return false;
    } else {
        setAlert("Petugas Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-triangle");
        redirect("petugas");
        return false;
    }

}

function deletePetugas($data)
{
    $id = $data['idpetugas'];
    $loggedInPetugas = getUID();

    if ($id == $loggedInPetugas) {
        setAlert("Petugas Gagal Dihapus!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("petugas");
        return false;
    }

    $queryDelete = query("DELETE FROM petugas WHERE id_petugas = $id AND id_petugas != $loggedInPetugas", false);
    if ($queryDelete) {
        setAlert("Petugas Berhasil Dihapus!", "floating-alert bg-green", "ico-info-circle");
        redirect("petugas");
        return false;
    } else {
        setAlert("Petugas Gagal Dihapus!", "floating-alert bg-danger", "ico-exclamation-circle");
        redirect("petugas");
        return false;
    }
}

function changePassword($data){
    $id = getUID();
    $passwordLama = $data['passwordLama'];
    $passwordBaru = $data['passwordBaru'];
    $konfirmPassword = $data['konfirmPass'];

    $validate = validate($data, [
        "passwordLama" => [[RULE_REQUIRED, "required" => "Password Lama"]],
        "passwordBaru" => [[RULE_REQUIRED, "required" => "Password Baru"]],
        "konfirmPass" => [[RULE_REQUIRED, "required" => "Konfirmasi Password"]],
    ]);

    if (isErrors()) return false;


    if (isSiswa()){
        $siswa = query("SELECT * FROM siswa WHERE nis = $id");
        $passLama = $siswa['password'];
        if (password_verify($passwordLama, $passLama)){
            if ($passwordBaru == $konfirmPassword){
                $pass = password_hash($passwordBaru, PASSWORD_DEFAULT);
                $sql = query("UPDATE siswa SET password = '$pass' WHERE nis = $id", false);

                if($sql) {
                    setAlert("Password Berhasil Diubah!", "floating-alert bg-green", "ico-info-circle");
                    unset($_SESSION['login']);
                    redirect("");
                    return false;
                }else{
                    setAlert("Password Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-circle");
                    return false;
                }

            }else{
                setIsInvalid(["#konfirmPass"], ["Konfirmasi Password harus Sama dengan field Password Baru!"]);
                return false;
            }
        }else{
            setIsInvalid(["#password"], ["Password Lama Salah!"]);
            return false;
        }

    }

    if (isPetugas() or isAdmin()){
        $petugas = query("SELECT * FROM petugas WHERE id_petugas = $id");
        $passLama = $petugas['password'];
        if (password_verify($passwordLama, $passLama)){
            if ($passwordBaru == $konfirmPassword){
                $pass = password_hash($passwordBaru, PASSWORD_DEFAULT);
                $sql = query("UPDATE petugas SET password = '$pass' WHERE id_petugas = $id", false);

                if($sql) {
                    setAlert("Password Berhasil Diubah!", "floating-alert bg-green", "ico-info-circle");
                    unset($_SESSION['login']);
                    redirect("");
                    return false;
                }else{
                    setAlert("Password Gagal Diubah!", "floating-alert bg-danger", "ico-exclamation-circle");
                    return false;
                }

            }else{
                setIsInvalid(["#konfirmPass"], ["Konfirmasi Password harus Sama dengan field Password Baru!"]);
                return false;
            }
        }else{
            setIsInvalid(["#password"], ["Password Lama Salah!"]);
            return false;
        }

    }

}