<?php
session_start();
if (!isset($_SESSION["user_id"])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hapus Akun - Fonnte API</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;font-family:'Inter',sans-serif}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:20px;background:#fff7f7;color:#0f172a}.panel{width:100%;max-width:480px;padding:30px;background:#fff;border:1px solid #fecaca;border-radius:10px;box-shadow:0 12px 30px rgba(127,29,29,.08)}h1{margin:0 0 10px;color:#991b1b;font-size:24px}p{color:#475569;font-size:14px;line-height:1.6}label{display:block;margin:22px 0 7px;font-size:13px;font-weight:600}input{width:100%;padding:12px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px}button,a{display:inline-block;margin-top:18px;padding:12px 15px;border:0;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer}button{color:#fff;background:#dc2626}a{color:#475569;background:#f1f5f9}.alert{display:none;padding:12px;margin-top:18px;border-radius:8px;color:#991b1b;background:#fef2f2;font-size:13px}
</style>
</head>
<body><main class="panel"><h1>Hapus akun?</h1><p>Tindakan ini permanen. Data akun akan dihapus dan notifikasi WhatsApp akan dikirim ke nomor yang terdaftar. Ketik <strong>HAPUS AKUN</strong> untuk melanjutkan.</p><div id="alert" class="alert"></div>
<form id="deleteForm"><label for="confirmation">Konfirmasi</label><input id="confirmation" name="confirmation" placeholder="HAPUS AKUN" required><button type="submit">Hapus Akun Permanen</button><a href="dashboard.php">Batal</a></form></main>
<script>
document.getElementById('deleteForm').addEventListener('submit', async function(event) {
    event.preventDefault();
    const alertBox = document.getElementById('alert');
    if (!window.confirm('Yakin ingin menghapus akun secara permanen?')) return;
    try {
        const response = await fetch('api/delete-account.php', { method: 'POST', body: new FormData(this) });
        const result = await response.json();
        if (result.status) {
            alert(result.message + (result.whatsapp_status ? ' - ' + result.whatsapp_status : ''));
            window.location.href = result.redirect;
        } else {
            alertBox.innerText = result.message;
            alertBox.style.display = 'block';
        }
    } catch (error) {
        alertBox.innerText = 'Terjadi kesalahan koneksi';
        alertBox.style.display = 'block';
    }
});
</script>
</body>
</html>
