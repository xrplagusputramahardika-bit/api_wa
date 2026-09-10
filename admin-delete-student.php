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
$stmt = $conn->prepare("SELECT nama, no_hp FROM users WHERE id = ? AND role = 'siswa' LIMIT 1");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    echo json_encode(["status" => false, "message" => "Siswa tidak ditemukan atau bukan akun siswa"]);
    exit;
}

$pesan = "Akun dihapus oleh admin\n\nHalo, " . $student["nama"] . "!\nAkun Anda pada Fonnte API telah dihapus oleh administrator.";
$hasilWA = kirimWhatsApp($student["no_hp"], $pesan);
$waStatus = isset($hasilWA["status"]) && $hasilWA["status"] == true;
$logStatus = $waStatus ? "success" : "failed";
$logResponse = json_encode($hasilWA);

$conn->begin_transaction();
$log = $conn->prepare("INSERT INTO log_whatsapp (user_id, no_tujuan, pesan, status, response) VALUES (?, ?, ?, ?, ?)");
$log->bind_param("issss", $studentId, $student["no_hp"], $pesan, $logStatus, $logResponse);
$log->execute();
$delete = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'siswa'");
$delete->bind_param("i", $studentId);
$deleted = $delete->execute();
if (!$deleted) {
    $conn->rollback();
    echo json_encode(["status" => false, "message" => "Siswa gagal dihapus"]);
    exit;
}
$conn->commit();

echo json_encode(["status" => true, "message" => "Siswa berhasil dihapus", "whatsapp_status" => $waStatus ? "Notifikasi WhatsApp terkirim" : "Siswa dihapus, tetapi notifikasi WhatsApp gagal"]);