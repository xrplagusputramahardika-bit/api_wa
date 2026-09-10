<?php

require_once "functions/fonnte.php";

$nomor = "0882010162305";

$pesan = "Halo! 👋

Ini adalah pesan percobaan dari project API Fonnte saya.

Jika pesan ini masuk, berarti koneksi Fonnte berhasil.";

$hasil = kirimWhatsApp($nomor, $pesan);

echo "<pre>";
print_r($hasil);
echo "</pre>";

?>