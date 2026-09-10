<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../functions/fonnte.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => false,
        "message" => "Gunakan method POST"
    ]);
    exit;
}

$nama     = trim($_POST["nama"] ?? "");
$email    = trim($_POST["email"] ?? "");
$no_hp    = trim($_POST["no_hp"] ?? "");
$password = $_POST["password"] ?? "";

// Validasi Kelengkapan Data
if ($nama === "" || $email === "" || $no_hp === "" || $password === "") {
    echo json_encode(["status" => false, "message" => "Semua data wajib diisi"]);
    exit;
}

// Validasi Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => false, "message" => "Format email tidak valid"]);
    exit;
}

// Validasi Password
if (strlen($password) < 6) {
    echo json_encode(["status" => false, "message" => "Password minimal 6 karakter"]);
    exit;
}

// Format nomor ke bentuk canonical agar 08..., 62..., dan +62... dianggap sama.
$no_hpInput = $no_hp;
$no_hp = formatNomor($no_hp);
if (strlen($no_hp) < 10 || substr($no_hp, 0, 2) !== "62") {
    echo json_encode(["status" => false, "message" => "Nomor WhatsApp tidak valid"]);
    exit;
}

// Cek Duplikasi Email
$cekEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$cekEmail->bind_param("s", $email);
$cekEmail->execute();
if ($cekEmail->get_result()->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Email sudah terdaftar"]);
    exit;
}

// Cek nomor lama dalam berbagai format, bukan hanya string yang persis sama.
$cekNomor = $conn->query("SELECT id, no_hp FROM users");
while ($userNomor = $cekNomor->fetch_assoc()) {
    if (formatNomor($userNomor["no_hp"]) === $no_hp) {
        echo json_encode([
            "status" => false,
            "message" => "Nomor WhatsApp sudah terdaftar",
            "nomor_terdeteksi" => $no_hp
        ]);
        exit;
    }
}

// Simpan User ke Database
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (nama, email, no_hp, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $email, $no_hp, $passwordHash);

if (!$stmt->execute()) {
    echo json_encode(["status" => false, "message" => "Registrasi gagal disimpan ke database"]);
    exit;
}

// Kirim Notifikasi WhatsApp
$pesan  = "🎉 REGISTRASI BERHASIL\n\n";
$pesan .= "Halo, " . $nama . "!\n\n";
$pesan .= "Akun Anda telah berhasil dibuat.\n\n";
$pesan .= "📧 Email: " . $email . "\n";
$pesan .= "📱 WhatsApp: " . $no_hp . "\n\n";
$pesan .= "Terima kasih telah melakukan registrasi.";

$hasilWA = kirimWhatsApp($no_hp, $pesan);

// Evaluasi Response WhatsApp
$waStatus = isset($hasilWA["status"]) && $hasilWA["status"] == true;
$waReason = $hasilWA["reason"] ?? ($hasilWA["message"] ?? "Gagal terkirim");

echo json_encode([
    "status" => true,
    "message" => "Registrasi berhasil!",
    "whatsapp_status" => $waStatus ? "WhatsApp terkirim" : "WhatsApp gagal: " . $waReason,
    "whatsapp_detail" => $hasilWA
]);