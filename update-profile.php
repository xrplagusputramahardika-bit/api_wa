<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../functions/fonnte.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => false, "message" => "Sesi login telah berakhir"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => false, "message" => "Gunakan method POST"]);
    exit;
}

$userId = (int) $_SESSION["user_id"];
$nama = trim($_POST["nama"] ?? "");
$email = trim($_POST["email"] ?? "");
$noHp = formatNomor(trim($_POST["no_hp"] ?? ""));
$password = $_POST["password"] ?? "";

if ($nama === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($noHp) < 10) {
    echo json_encode(["status" => false, "message" => "Nama, email, dan nomor WhatsApp harus valid"]);
    exit;
}

$duplicate = $conn->prepare("SELECT id FROM users WHERE (email = ? OR no_hp = ?) AND id <> ? LIMIT 1");
$duplicate->bind_param("ssi", $email, $noHp, $userId);
$duplicate->execute();
if ($duplicate->get_result()->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Email atau nomor WhatsApp sudah digunakan akun lain"]);
    exit;
}

if ($password !== "" && strlen($password) < 6) {
    echo json_encode(["status" => false, "message" => "Password baru minimal 6 karakter"]);
    exit;
}

if ($password !== "") {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama, $email, $noHp, $passwordHash, $userId);
} else {
    $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama, $email, $noHp, $userId);
}

if (!$stmt->execute()) {
    echo json_encode(["status" => false, "message" => "Profil gagal diperbarui"]);
    exit;
}

$_SESSION["user_name"] = $nama;
$_SESSION["user_email"] = $email;
$pesan = "Profil diperbarui\n\nHalo, " . $nama . "!\nData profil akun Fonnte API Anda berhasil diperbarui.";
$hasilWA = kirimWhatsApp($noHp, $pesan);
$waStatus = isset($hasilWA["status"]) && $hasilWA["status"] == true;
$logStatus = $waStatus ? "success" : "failed";
$logResponse = json_encode($hasilWA);
$logStmt = $conn->prepare("INSERT INTO log_whatsapp (user_id, no_tujuan, pesan, status, response) VALUES (?, ?, ?, ?, ?)");
$logStmt->bind_param("issss", $userId, $noHp, $pesan, $logStatus, $logResponse);
$logStmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Profil berhasil diperbarui",
    "whatsapp_status" => $waStatus ? "Notifikasi WhatsApp terkirim" : "Profil diperbarui, tetapi notifikasi WhatsApp gagal"
]);