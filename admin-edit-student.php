<?php
session_start();
if (!isset($_SESSION["user_id"]) || ($_SESSION["user_role"] ?? "") !== "admin") { header("Location: dashboard.php"); exit; }
require_once __DIR__ . "/config/database.php";
$studentId = (int) ($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT id, nama, email, no_hp FROM users WHERE id = ? AND role = 'siswa' LIMIT 1");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) { header("Location: admin.php"); exit; }
function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Siswa - Fonnte API</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}body{margin:0;min-height:100vh;background:#f8fafc;color:#0f172a}.topbar{display:flex;justify-content:space-between;padding:18px max(24px,calc((100% - 720px)/2));color:#fff;background:#0f172a}.topbar a{color:#cbd5e1;text-decoration:none;font-size:13px}.main{max-width:720px;margin:auto;padding:42px 24px}.panel{padding:30px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 12px 30px rgba(15,23,42,.05)}h1{margin:0 0 8px;font-size:26px}.intro{margin:0 0 26px;color:#64748b;font-size:14px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.full{grid-column:1/-1}.group{margin-bottom:2px}label{display:block;margin-bottom:7px;font-size:13px;font-weight:600}input{width:100%;padding:12px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:0}input:focus{border-color:#25d366;box-shadow:0 0 0 4px rgba(37,211,102,.14)}small{display:block;margin-top:6px;color:#64748b;font-size:11px}.actions{display:flex;justify-content:flex-end;gap:10px;margin-top:26px}.button,.back{padding:12px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none}.button{border:0;color:#fff;background:#25d366;cursor:pointer}.back{color:#475569;background:#f1f5f9}.alert{display:none;padding:12px 14px;margin-bottom:18px;border-radius:8px;font-size:13px}.ok{color:#166534;background:#dcfce7}.error{color:#991b1b;background:#fef2f2}@media(max-width:600px){.main{padding:24px 18px}.grid{display:block}.group{margin-bottom:18px}.actions{justify-content:stretch}.actions>*{flex:1;text-align:center}}
</style>
</head>
<body>
<nav class="topbar"><a href="admin.php"><i class="fa-solid fa-arrow-left"></i> Dashboard Admin</a><a href="logout.php">Keluar</a></nav>
<main class="main"><section class="panel"><h1>Edit Data Siswa</h1><p class="intro">Perubahan akan disimpan dan diberitahukan ke nomor WhatsApp siswa.</p><div id="alert" class="alert"></div>
<form id="editForm"><input type="hidden" name="id" value="<?= (int) $student["id"] ?>"><div class="grid"><div class="group"><label for="nama">Nama Lengkap</label><input id="nama" name="nama" value="<?= e($student["nama"]) ?>" required></div><div class="group"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($student["email"]) ?>" required></div><div class="group full"><label for="no_hp">Nomor WhatsApp</label><input id="no_hp" name="no_hp" value="<?= e($student["no_hp"]) ?>" required><small>Nomor akan dinormalisasi ke format 62.</small></div><div class="group full"><label for="password">Password Baru</label><input id="password" name="password" type="password" placeholder="Kosongkan jika tidak diubah"><small>Password baru minimal 6 karakter.</small></div></div><div class="actions"><a class="back" href="admin.php">Batal</a><button class="button" id="submit" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button></div></form></section></main>
<script>
document.getElementById('editForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const alertBox = document.getElementById('alert');
    const button = document.getElementById('submit');
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
    try {
        const response = await fetch('api/admin-update-student.php', {method:'POST', body:new FormData(this)});
        const result = await response.json();
        alertBox.className = 'alert ' + (result.status ? 'ok' : 'error');
        alertBox.innerText = result.message + (result.whatsapp_status ? ' - ' + result.whatsapp_status : '');
        alertBox.style.display = 'block';
        if (result.status) setTimeout(() => window.location.href = 'admin.php', 1200);
    } catch (error) {
        alertBox.className = 'alert error';
        alertBox.innerText = 'Terjadi kesalahan koneksi atau server.';
        alertBox.style.display = 'block';
    } finally {
        button.disabled = false;
        button.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan';
    }
});
</script>
</body>
</html>
