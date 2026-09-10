<?php
session_start();
if (isset($_SESSION["user_id"])) {
    $redirect = ($_SESSION["user_role"] ?? "siswa") === "admin" ? "admin.php" : "dashboard.php";
    header("Location: " . $redirect);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fonnte API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 20px; color: #0f172a; background: linear-gradient(135deg, #0f172a, #1e293b); }
        .container { width: 100%; max-width: 440px; overflow: hidden; background: #fff; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,.3); }
        .header { padding: 30px 25px; text-align: center; color: #fff; background: linear-gradient(135deg, #25d366, #128c7e); }
        .header i { font-size: 42px; margin-bottom: 10px; }
        h1 { margin: 0; font-size: 22px; } .header p { margin: 5px 0 0; font-size: 13px; opacity: .9; }
        .form-body { padding: 30px 25px; } .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #334155; font-size: 13px; font-weight: 600; }
        .input-wrapper { position: relative; } .input-wrapper i { position: absolute; left: 14px; top: 50%; color: #94a3b8; transform: translateY(-50%); }
        input { width: 100%; padding: 12px 14px 12px 42px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px; }
        input:focus { border-color: #25d366; box-shadow: 0 0 0 4px rgba(37,211,102,.15); }
        button { width: 100%; display: flex; justify-content: center; gap: 8px; padding: 13px; margin-top: 10px; border: 0; border-radius: 8px; color: #fff; background: #25d366; font-size: 15px; font-weight: 600; cursor: pointer; }
        button:hover { background: #1eb757; } button:disabled { background: #94a3b8; cursor: not-allowed; }
        .alert { display: none; gap: 10px; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .alert-danger { color: #991b1b; background: #fef2f2; border: 1px solid #fecaca; }
        .register { margin: 20px 0 0; text-align: center; color: #64748b; font-size: 13px; } a { color: #128c7e; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<main class="container">
    <header class="header"><i class="fa-brands fa-whatsapp"></i><h1>Selamat Datang Kembali</h1><p>Masuk ke dashboard Fonnte API Anda</p></header>
    <section class="form-body">
        <div id="alertBox" class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i><span id="alertMessage"></span></div>
        <form id="formLogin">
            <div class="form-group"><label for="email">Alamat Email</label><div class="input-wrapper"><i class="fa-regular fa-envelope"></i><input id="email" type="email" name="email" placeholder="nama@email.com" required></div></div>
            <div class="form-group"><label for="password">Password</label><div class="input-wrapper"><i class="fa-solid fa-lock"></i><input id="password" type="password" name="password" placeholder="Masukkan password" required></div></div>
            <button type="submit" id="btnSubmit"><span>Masuk ke Dashboard</span><i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <p class="register">Belum punya akun? <a href="index.php">Daftar sekarang</a></p>
    </section>
</main>
<script>
document.getElementById('formLogin').addEventListener('submit', async function (event) {
    event.preventDefault();
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');
    const button = document.getElementById('btnSubmit');
    alertBox.style.display = 'none';
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';

    try {
        const response = await fetch('api/login.php', { method: 'POST', body: new FormData(this) });
        const result = await response.json();
        if (!result.status) throw new Error(result.message);
        window.location.href = result.redirect;
    } catch (error) {
        alertMessage.innerText = error.message || 'Login gagal. Silakan coba lagi.';
        alertBox.style.display = 'flex';
    } finally {
        button.disabled = false;
        button.innerHTML = '<span>Masuk ke Dashboard</span><i class="fa-solid fa-arrow-right"></i>';
    }
});
</script>
</body>
</html>