<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../functions/fonnte.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => false, "message" => "Akses admin diperlukan"]);
    exit;
}

$accessStmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$accessStmt->bind_param("i", $_SESSION["user_id"]);
$accessStmt->execute();
$accessUser = $accessStmt->get_result()->fetch_assoc();
if (!$accessUser || $accessUser["role"] !== "admin") {
    echo json_encode(["status" => false, "message" => "Akses admin diperlukan"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => false, "message" => "Gunakan method POST"]);
    exit;
}

$studentId = (int) ($_POST["id"] ?? 0);
$nama = trim($_POST["nama"] ?? "");
$email = trim($_POST["email"] ?? "");
$noHp = formatNomor(trim($_POST["no_hp"] ?? ""));
$password = $_POST["password"] ?? "";

if ($studentId < 1 || $nama === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($noHp) < 10) {
    echo json_encode(["status" => false, "message" => "Data siswa tidak valid"]);
    exit;
}

$studentStmt = $conn->prepare("SELECT id, nama, no_hp FROM users WHERE id = ? AND role = 'siswa' LIMIT 1");
$studentStmt->bind_param("i", $studentId);
$studentStmt->execute();
$student = $studentStmt->get_result()->fetch_assoc();
if (!$student) {
    echo json_encode(["status" => false, "message" => "Siswa tidak ditemukan"]);
    exit;
}

$duplicate = $conn->prepare("SELECT id FROM users WHERE (email = ? OR no_hp = ?) AND id <> ? LIMIT 1");
$duplicate->bind_param("ssi", $email, $noHp, $studentId);
$duplicate->execute();
if ($duplicate->get_result()->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Email atau nomor WhatsApp sudah digunakan"]);
    exit;
}

if ($password !== "" && strlen($password) < 6) {
    echo json_encode(["status" => false, "message" => "Password minimal 6 karakter"]);
    exit;
}

if ($password !== "") {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ?, password = ? WHERE id = ? AND role = 'siswa'");
    $update->bind_param("ssssi", $nama, $email, $noHp, $passwordHash, $studentId);
} else {
    $update = $conn->prepare("UPDATE users SET nama = ?, email = ?, no_hp = ? WHERE id = ? AND role = 'siswa'");
    $update->bind_param("sssi", $nama, $email, $noHp, $studentId);
}

if (!$update->execute()) {
    echo json_encode(["status" => false, "message" => "Data siswa gagal diperbarui"]);
    exit;
}

$pesan = "Profil siswa diperbarui\n\nHalo, " . $nama . "!\nData akun Anda telah diperbarui oleh administrator Fonnte API.";
$hasilWA = kirimWhatsApp($noHp, $pesan);
$waStatus = isset($hasilWA["status"]) && $hasilWA["status"] == true;
$logStatus = $waStatus ? "success" : "failed";
$logResponse = json_encode($hasilWA);
$log = $conn->prepare("INSERT INTO log_whatsapp (user_id, no_tujuan, pesan, status, response) VALUES (?, ?, ?, ?, ?)");
$log->bind_param("issss", $studentId, $noHp, $pesan, $logStatus, $logResponse);
$log->execute();

echo json_encode(["status" => true, "message" => "Data siswa berhasil diperbarui", "whatsapp_status" => $waStatus ? "Notifikasi WhatsApp terkirim" : "Data tersimpan, tetapi notifikasi WhatsApp gagal"]);