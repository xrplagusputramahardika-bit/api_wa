<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/config/database.php";

$userId = (int) $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT id, nama, email, no_hp, role, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION["user_role"] = $user["role"];
if ($user["role"] === "admin") {
    header("Location: admin.php");
    exit;
}

$statsStmt = $conn->prepare("SELECT COUNT(*) AS total, COALESCE(SUM(status = 'success'), 0) AS berhasil, COALESCE(SUM(status = 'failed'), 0) AS gagal FROM log_whatsapp WHERE user_id = ?");
$statsStmt->bind_param("i", $userId);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();

$logStmt = $conn->prepare("SELECT no_tujuan, pesan, status, created_at FROM log_whatsapp WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$logStmt->bind_param("i", $userId);
$logStmt->execute();
$logs = $logStmt->get_result();

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fonnte API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { margin: 0; color: #0f172a; background: #f8fafc; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 18px max(24px, calc((100% - 1120px) / 2)); color: #fff; background: #0f172a; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; } .brand i { color: #25d366; font-size: 24px; }
        .logout { display: inline-flex; align-items: center; gap: 8px; color: #cbd5e1; font-size: 13px; text-decoration: none; } .logout:hover { color: #fff; }
        .main { max-width: 1120px; margin: 0 auto; padding: 42px 24px; }
        .welcome { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 30px; } h1 { margin: 0 0 8px; font-size: clamp(24px, 4vw, 34px); } .welcome p { margin: 0; color: #64748b; font-size: 14px; }
        .profile { color: #64748b; text-align: right; font-size: 13px; } .profile strong { display: block; color: #0f172a; font-size: 14px; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 28px; }
        .stat, .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
        .stat { padding: 22px; } .stat-label { color: #64748b; font-size: 13px; } .stat-value { display: block; margin-top: 12px; font-size: 28px; font-weight: 700; } .stat i { float: right; color: #25d366; font-size: 20px; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 22px; border-bottom: 1px solid #e2e8f0; } h2 { margin: 0; font-size: 17px; } .panel-header span { color: #64748b; font-size: 12px; }
        .table-wrap { overflow-x: auto; } table { width: 100%; border-collapse: collapse; font-size: 13px; } th, td { padding: 15px 22px; text-align: left; border-bottom: 1px solid #f1f5f9; } th { color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; } td { max-width: 360px; } .message { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #475569; }
        .badge { display: inline-block; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; } .success { color: #166534; background: #dcfce7; } .failed { color: #991b1b; background: #fee2e2; } .empty { padding: 36px 22px; color: #64748b; text-align: center; }
        @media (max-width: 680px) { .topbar { padding: 16px 18px; } .main { padding: 30px 18px; } .welcome { display: block; } .profile { margin-top: 16px; text-align: left; } .stats { grid-template-columns: 1fr; gap: 12px; } .stat { padding: 18px; } th, td { padding: 13px 14px; } }
    </style>
</head>
<body>
<nav class="topbar"><div class="brand"><i class="fa-brands fa-whatsapp"></i> Fonnte API</div><div><a class="logout" href="profile.php"><i class="fa-solid fa-user-pen"></i> Profil</a> <a class="logout" href="delete-account.php"><i class="fa-solid fa-user-xmark"></i> Hapus Akun</a> <a class="logout" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a></div></nav>
<main class="main">
    <section class="welcome">
        <div><h1>Halo, <?= e($user["nama"]) ?>.</h1><p>Berikut ringkasan aktivitas WhatsApp Anda.</p></div>
        <div class="profile"><strong><?= e($user["email"]) ?></strong><?= e($user["no_hp"]) ?></div>
    </section>
    <section class="stats">
        <div class="stat"><i class="fa-regular fa-paper-plane"></i><span class="stat-label">Total pesan</span><strong class="stat-value"><?= (int) $stats["total"] ?></strong></div>
        <div class="stat"><i class="fa-solid fa-circle-check"></i><span class="stat-label">Berhasil terkirim</span><strong class="stat-value"><?= (int) $stats["berhasil"] ?></strong></div>
        <div class="stat"><i class="fa-solid fa-circle-exclamation"></i><span class="stat-label">Gagal terkirim</span><strong class="stat-value"><?= (int) $stats["gagal"] ?></strong></div>
    </section>
    <section class="panel">
        <div class="panel-header"><h2>Aktivitas terbaru</h2><span>Maksimal 5 aktivitas</span></div>
        <?php if ($logs->num_rows === 0): ?>
            <div class="empty">Belum ada aktivitas pengiriman WhatsApp.</div>
        <?php else: ?>
            <div class="table-wrap"><table><thead><tr><th>Tujuan</th><th>Pesan</th><th>Status</th><th>Waktu</th></tr></thead><tbody>
            <?php while ($log = $logs->fetch_assoc()): ?>
                <tr><td><?= e($log["no_tujuan"]) ?></td><td class="message" title="<?= e($log["pesan"]) ?>"><?= e($log["pesan"]) ?></td><td><span class="badge <?= e($log["status"]) ?>"><?= e(ucfirst($log["status"])) ?></span></td><td><?= e(date("d/m/Y H:i", strtotime($log["created_at"]))) ?></td></tr>
            <?php endwhile; ?>
            </tbody></table></div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>