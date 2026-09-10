<?php
session_start();
require_once __DIR__ . "/config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$accessStmt = $conn->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$accessStmt->bind_param("i", $_SESSION["user_id"]);
$accessStmt->execute();
$accessUser = $accessStmt->get_result()->fetch_assoc();

if (!$accessUser || $accessUser["role"] !== "admin") {
    $_SESSION["user_role"] = $accessUser["role"] ?? "siswa";
    header("Location: dashboard.php");
    exit;
}

$_SESSION["user_role"] = "admin";

$userCount = (int) $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$studentCount = (int) $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'siswa'")->fetch_assoc()["total"];
$logCount = (int) $conn->query("SELECT COUNT(*) AS total FROM log_whatsapp")->fetch_assoc()["total"];
$successCount = (int) $conn->query("SELECT COUNT(*) AS total FROM log_whatsapp WHERE status = 'success'")->fetch_assoc()["total"];
$users = $conn->query("SELECT id, nama, email, no_hp, role, created_at FROM users ORDER BY created_at DESC");
$logs = $conn->query("SELECT l.no_tujuan, l.pesan, l.status, l.created_at, u.nama FROM log_whatsapp l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC LIMIT 10");

function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fonnte API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{box-sizing:border-box;font-family:'Inter',sans-serif}body{margin:0;color:#0f172a;background:#f8fafc}.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px max(24px,calc((100% - 1180px)/2));color:#fff;background:#0f172a}.brand{display:flex;align-items:center;gap:10px;font-weight:700}.brand i{color:#25d366;font-size:24px}.topbar a{color:#cbd5e1;text-decoration:none;font-size:13px}.main{max-width:1180px;margin:auto;padding:40px 24px}.heading{display:flex;justify-content:space-between;align-items:end;margin-bottom:28px}.heading h1{margin:0 0 8px;font-size:30px}.heading p{margin:0;color:#64748b;font-size:14px}.admin-label{padding:7px 10px;color:#166534;background:#dcfce7;border-radius:999px;font-size:11px;font-weight:600}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}.stat,.panel{background:#fff;border:1px solid #e2e8f0;border-radius:10px}.stat{padding:20px}.stat i{float:right;color:#25d366;font-size:20px}.stat span{display:block;color:#64748b;font-size:12px}.stat strong{display:block;margin-top:12px;font-size:27px}.panel{margin-bottom:24px}.panel h2{margin:0;padding:20px 22px;border-bottom:1px solid #e2e8f0;font-size:17px}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;font-size:13px}th,td{padding:14px 20px;text-align:left;border-bottom:1px solid #f1f5f9;white-space:nowrap}th{color:#64748b;font-size:11px;text-transform:uppercase}.badge{padding:5px 9px;border-radius:999px;font-size:11px;font-weight:600}.admin{color:#7c2d12;background:#ffedd5}.siswa{color:#1d4ed8;background:#dbeafe}.success{color:#166534;background:#dcfce7}.failed{color:#991b1b;background:#fee2e2}.message{max-width:300px;overflow:hidden;text-overflow:ellipsis}.empty{padding:25px;color:#64748b;text-align:center}@media(max-width:760px){.main{padding:28px 18px}.stats{grid-template-columns:1fr 1fr}.heading{align-items:start}.admin-label{margin-top:4px}}@media(max-width:460px){.stats{grid-template-columns:1fr}.heading{display:block}}
        .actions{display:flex;gap:8px}.action{display:inline-flex;align-items:center;gap:5px;padding:7px 9px;border-radius:7px;font-size:11px;font-weight:600;text-decoration:none;border:0;cursor:pointer}.edit{color:#1d4ed8;background:#dbeafe}.delete{color:#991b1b;background:#fee2e2}
    </style>
</head>
<body>
<nav class="topbar"><div class="brand"><i class="fa-brands fa-whatsapp"></i> Fonnte API <span class="admin-label">ADMIN</span></div><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a></nav>
<main class="main">
    <section class="heading"><div><h1>Dashboard Admin</h1><p>Kelola pengguna dan pantau aktivitas WhatsApp.</p></div></section>
    <section class="stats"><div class="stat"><i class="fa-solid fa-users"></i><span>Total akun</span><strong><?= $userCount ?></strong></div><div class="stat"><i class="fa-solid fa-user-graduate"></i><span>Total siswa</span><strong><?= $studentCount ?></strong></div><div class="stat"><i class="fa-regular fa-paper-plane"></i><span>Total log pesan</span><strong><?= $logCount ?></strong></div><div class="stat"><i class="fa-solid fa-circle-check"></i><span>Pesan berhasil</span><strong><?= $successCount ?></strong></div></section>
    <section class="panel"><h2>Daftar pengguna</h2><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>WhatsApp</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead><tbody>
    <?php if ($users->num_rows === 0): ?><tr><td class="empty" colspan="6">Belum ada pengguna.</td></tr><?php endif; ?>
    <?php while ($user = $users->fetch_assoc()): ?><tr><td><?= e($user["nama"]) ?></td><td><?= e($user["email"]) ?></td><td><?= e($user["no_hp"]) ?></td><td><span class="badge <?= e($user["role"]) ?>"><?= e(ucfirst($user["role"])) ?></span></td><td><?= e(date("d/m/Y H:i", strtotime($user["created_at"]))) ?></td><td><?php if ($user["role"] === "siswa"): ?><div class="actions"><a class="action edit" href="admin-edit-student.php?id=<?= (int) $user["id"] ?>"><i class="fa-solid fa-pen"></i> Edit</a><button class="action delete delete-student" type="button" data-id="<?= (int) $user["id"] ?>" data-name="<?= e($user["nama"]) ?>"><i class="fa-solid fa-trash"></i> Hapus</button></div><?php else: ?><span class="badge admin">Dilindungi</span><?php endif; ?></td></tr><?php endwhile; ?>
    </tbody></table></div></section>
    <section class="panel"><h2>Log WhatsApp terbaru</h2><div class="table-wrap"><table><thead><tr><th>Pengguna</th><th>Tujuan</th><th>Pesan</th><th>Status</th><th>Waktu</th></tr></thead><tbody>
    <?php if ($logs->num_rows === 0): ?><tr><td class="empty" colspan="5">Belum ada log WhatsApp.</td></tr><?php endif; ?>
    <?php while ($log = $logs->fetch_assoc()): ?><tr><td><?= e($log["nama"] ?? "Akun terhapus") ?></td><td><?= e($log["no_tujuan"]) ?></td><td class="message" title="<?= e($log["pesan"]) ?>"><?= e($log["pesan"]) ?></td><td><span class="badge <?= e($log["status"]) ?>"><?= e(ucfirst($log["status"])) ?></span></td><td><?= e(date("d/m/Y H:i", strtotime($log["created_at"]))) ?></td></tr><?php endwhile; ?>
    </tbody></table></div></section>
</main>
<script>
document.querySelectorAll('.delete-student').forEach(function (button) {
    button.addEventListener('click', async function () {
        if (!window.confirm('Hapus akun siswa ' + this.dataset.name + ' secara permanen?')) return;
        this.disabled = true;
        try {
            const data = new FormData();
            data.append('id', this.dataset.id);
            const response = await fetch('api/admin-delete-student.php', {method: 'POST', body: data});
            const result = await response.json();
            if (!result.status) throw new Error(result.message);
            alert(result.message + (result.whatsapp_status ? ' - ' + result.whatsapp_status : ''));
            window.location.reload();
        } catch (error) {
            alert(error.message || 'Siswa gagal dihapus.');
            this.disabled = false;
        }
    });
});
</script>
</body>
</html>