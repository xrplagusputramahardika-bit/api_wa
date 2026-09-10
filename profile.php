<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }
require_once __DIR__ . "/config/database.php";
$stmt = $conn->prepare("SELECT nama, email, no_hp FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { session_destroy(); header("Location: login.php"); exit; }
function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Profile - Fonnte API</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}body{margin:0;min-height:100vh;background:#f8fafc;color:#0f172a}.topbar{display:flex;justify-content:space-between;padding:18px max(24px,calc((100% - 760px)/2));color:#fff;background:#0f172a}.topbar a{color:#cbd5e1;text-decoration:none;font-size:13px}.container{max-width:760px;margin:0 auto;padding:42px 24px}.panel{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:28px}.panel h1{margin:0 0 8px;font-size:25px}.panel p{margin:0 0 26px;color:#64748b;font-size:14px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.group{margin-bottom:18px}.group.full{grid-column:1/-1}label{display:block;margin-bottom:7px;font-size:13px;font-weight:600}input{width:100%;padding:12px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:0}input:focus{border-color:#25d366;box-shadow:0 0 0 4px rgba(37,211,102,.15)}.hint{display:block;margin-top:6px;color:#64748b;font-size:11px}.actions{display:flex;justify-content:flex-end;gap:10px;margin-top:8px}.button{padding:12px 16px;border:0;border-radius:8px;color:#fff;background:#25d366;font-weight:600;cursor:pointer}.back{padding:12px 16px;color:#475569;text-decoration:none;font-size:13px}.alert{display:none;padding:12px 16px;margin-bottom:20px;border-radius:8px;font-size:13px}.success{color:#166534;background:#dcfce7}.danger{color:#991b1b;background:#fef2f2}@media(max-width:600px){.container{padding:24px 18px}.grid{display:block}.group{margin-bottom:16px}.actions{justify-content:stretch}.actions>*{flex:1;text-align:center}}
</style>
</head>
<body>
<nav class="topbar"><a href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Dashboard</a><a href="logout.php">Keluar</a></nav>
<main class="container"><section class="panel"><h1>Update Profile</h1><p>Perbarui data akun. Notifikasi perubahan akan dikirim ke nomor WhatsApp baru.</p>
<div id="alert" class="alert"></div>
<form id="profileForm"><div class="grid">
<div class="group"><label for="nama">Nama Lengkap</label><input id="nama" name="nama" value="<?= e($user["nama"]) ?>" required></div>
<div class="group"><label for="email">Alamat Email</label><input id="email" type="email" name="email" value="<?= e($user["email"]) ?>" required></div>
<div class="group full"><label for="no_hp">Nomor WhatsApp</label><input id="no_hp" name="no_hp" value="<?= e($user["no_hp"]) ?>" required><span class="hint">Format 08xx atau 62xx.</span></div>
<div class="group full"><label for="password">Password Baru</label><input id="password" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"><span class="hint">Minimal 6 karakter jika diisi.</span></div>
</div><div class="actions"><a class="back" href="dashboard.php">Batal</a><button class="button" id="submit" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button></div></form></section></main>
<script>
document.getElementById('profileForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const alertBox = document.getElementById('alert');
    const button = document.getElementById('submit');
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
    try {
        const response = await fetch('api/update-profile.php', { method: 'POST', body: new FormData(this) });
        const result = await response.json();
        alertBox.className = 'alert ' + (result.status ? 'success' : 'danger');
        alertBox.innerText = result.message + (result.whatsapp_status ? ' - ' + result.whatsapp_status : '');
        alertBox.style.display = 'block';
        if (result.status) setTimeout(() => window.location.href = 'dashboard.php', 1200);
    } catch (error) {
        alertBox.className = 'alert danger';
        alertBox.innerText = 'Terjadi kesalahan koneksi';
        alertBox.style.display = 'block';
    } finally {
        button.disabled = false;
        button.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan';
    }
});
</script>
</body>
</html>
