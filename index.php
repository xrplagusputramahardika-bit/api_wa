<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - Fonnte API</title>
    <!-- Google Fonts & Font Awesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            padding: 30px 25px;
            text-align: center;
            color: #ffffff;
        }

        .header i {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 22px;
            font-weight: 700;
        }

        .header p {
            font-size: 13px;
            opacity: 0.9;
            margin-top: 4px;
        }

        .form-body {
            padding: 30px 25px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-wrapper input:focus {
            border-color: #25d366;
            box-shadow: 0 0 0 4px rgba(37, 211, 102, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: #25d366;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #1eb757;
        }

        .btn-submit:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <i class="fa-brands fa-whatsapp"></i>
        <h2>Registrasi Akun</h2>
        <p>Dapatkan notifikasi pendaftaran via WhatsApp</p>
    </div>

    <div class="form-body">
        <!-- Box Alert Pengumuman -->
        <div id="alertBox" class="alert">
            <i id="alertIcon" class="fa-solid"></i>
            <span id="alertMessage"></span>
        </div>

        <form id="formRegister">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Email</label>
                <div class="input-wrapper">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="budi@gmail.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nomor WhatsApp</label>
                <div class="input-wrapper">
                    <i class="fa-brands fa-whatsapp"></i>
                    <input type="text" name="no_hp" placeholder="081234567890" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" id="btnSubmit" class="btn-submit">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <p class="footer-text">Sudah punya akun? <a href="login.php">Masuk ke dashboard</a></p>
    </div>
</div>

<script>
document.getElementById('formRegister').addEventListener('submit', async function(e) {
    e.preventDefault();

    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');
    const alertIcon = document.getElementById('alertIcon');
    const btnSubmit = document.getElementById('btnSubmit');

    // Reset Alert
    alertBox.style.display = 'none';
    alertBox.className = 'alert';
    
    // Disable Button saat loading
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';

    const formData = new FormData(this);

    try {
        const response = await fetch('api/register.php', {
            method: 'POST',
            body: formData
        });

        const textResponse = await response.text();
        let result;

        try {
            result = JSON.parse(textResponse);
        } catch (err) {
            throw new Error('Server error (Non-JSON response): ' + textResponse);
        }

        alertBox.style.display = 'flex';
        
        if (result.status) {
            alertBox.classList.add('alert-success');
            alertIcon.className = 'fa-solid fa-circle-check';
            alertMessage.innerText = result.message + (result.whatsapp_status ? ' (' + result.whatsapp_status + ')' : '');
            this.reset();
        } else {
            alertBox.classList.add('alert-danger');
            alertIcon.className = 'fa-solid fa-triangle-exclamation';
            alertMessage.innerText = result.message;
        }
    } catch (error) {
        alertBox.style.display = 'flex';
        alertBox.classList.add('alert-danger');
        alertIcon.className = 'fa-solid fa-triangle-exclamation';
        alertMessage.innerText = error.message;
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<span>Daftar Sekarang</span> <i class="fa-solid fa-arrow-right"></i>';
    }
});
</script>

</body>
</html>