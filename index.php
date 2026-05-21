<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'store') {
    $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
    $dive_date     = mysqli_real_escape_string($conn, $_POST['dive_date']);
    $max_depth     = mysqli_real_escape_string($conn, $_POST['max_depth']);
    $duration      = mysqli_real_escape_string($conn, $_POST['duration']);
    $start_press   = !empty($_POST['start_press']) ? intval($_POST['start_press']) : "NULL";
    $end_press     = !empty($_POST['end_press']) ? intval($_POST['end_press']) : "NULL";
    $biota         = mysqli_real_escape_string($conn, $_POST['biota']);
    $notes         = mysqli_real_escape_string($conn, $_POST['notes']);

    $query = "INSERT INTO dive_logs (user_id, location_name, dive_date, max_depth, duration, start_press, end_press, biota, notes) 
              VALUES ($user_id, '$location_name', '$dive_date', $max_depth, $duration, $start_press, $end_press, '$biota', '$notes')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Log penyelaman baru berhasil disimpan!";
    }
}

$logs_query = mysqli_query($conn, "SELECT * FROM dive_logs WHERE user_id = $user_id ORDER BY dive_date DESC");

$title = "MarineLog - Dashboard Jurnal Selam";

ob_start();
?>

<div style="max-width: 1000px; margin: 0 auto; display: flex; gap: 30px; flex-wrap: wrap;">
    
    <div class="container" style="flex: 1; min-width: 350px; margin: 0;">
        <h3>📝 Tambah Log Selam Manual</h3>
        <?php if(!empty($success)): ?> <div class="alert alert-success"><?= $success; ?></div> <?php endif; ?>
        
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="store">
            
            <div class="form-group">
                <label>Nama Lokasi / Spot Selam</label>
                <input type="text" name="location_name" placeholder="Misal: Crystal Bay, Bali" required>
            </div>
            <div class="form-group">
                <label>Tanggal Menyelam</label>
                <input type="date" name="dive_date" required>
            </div>
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Kedalaman Maks (Meter)</label>
                    <input type="number" name="max_depth" step="0.1" required>
                </div>
                <div class="form-group flex-1">
                    <label>Durasi (Menit)</label>
                    <input type="number" name="duration" required>
                </div>
            </div>

            <div class="form-group" style="background: rgba(0, 210, 196, 0.05); padding: 12px; border-radius: 6px;">
                <label style="color:#00d2c4;">⛽ Hitung Konsumsi Udara (Logika Strava)</label>
                <div class="form-row" style="margin-top: 5px;">
                    <input type="number" id="startPress" name="start_press" placeholder="Tekanan Awal (Bar)">
                    <input type="number" id="endPress" name="end_press" placeholder="Tekanan Akhir (Bar)">
                </div>
                <div id="calcOutput" class="calc-result">Tekanan tabung dihitung otomatis saat diisi.</div>
            </div>

            <div class="form-group">
                <label>Biota yang Dijumpai</label>
                <input type="text" name="biota" placeholder="Contoh: Mola-Mola, Nudibranch">
            </div>
            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea name="notes" rows="3"></textarea>
            </div>
            <button type="submit">Simpan ke Jurnal Pribadi</button>
        </form>
    </div>

    <div style="flex: 1.2; min-width: 350px;">
        <h3>📚 Perpustakaan Riwayat Selam Anda</h3>
        <?php if(mysqli_num_rows($logs_query) == 0): ?>
            <p style="color:#aaa; font-style:italic;">Belum ada riwayat penyelaman yang tercatat.</p>
        <?php else: ?>
            <?php while($row = mysqli_fetch_assoc($logs_query)): ?>
                <div style="background:#1e3e62; padding:20px; border-radius:8px; margin-bottom:15px; border-left: 5px solid #00d2c4;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h4 style="margin:0; color:#00d2c4;">📍 <?= htmlspecialchars($row['location_name']); ?></h4>
                        <small style="color:#ccc;"><?= date('d M Y', strtotime($row['dive_date'])); ?></small>
                    </div>
                    <p style="margin:10px 0 5px 0; font-size:14px;">
                        📉 Kedalaman: <strong><?= $row['max_depth']; ?> m</strong> | 
                        ⏱️ Durasi: <strong><?= $row['duration']; ?> mnt</strong>
                        <?php if($row['start_press'] && $row['end_press']): ?>
                            | ⛽ Konsumsi: <strong><?= $row['start_press'] - $row['end_press']; ?> Bar</strong>
                        <?php endif; ?>
                    </p>
                    <?php if(!empty($row['biota'])): ?>
                        <p style="margin:5px 0; font-size:13px; color:#ffb200;">🐟 <em>Biota: <?= htmlspecialchars($row['biota']); ?></em></p>
                    <?php endif; ?>
                    <?php if(!empty($row['notes'])): ?>
                        <p style="margin:5px 0 0 0; font-size:13px; color:#ddd; background: rgba(0,0,0,0.2); padding:8px; border-radius:4px;">"<?= htmlspecialchars($row['notes']); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    const startInput = document.getElementById('startPress');
    const endInput = document.getElementById('endPress');
    const output = document.getElementById('calcOutput');

    function hitung() {
        const start = parseInt(startInput.value);
        const end = parseInt(endInput.value);
        if(!isNaN(start) && !isNaN(end)) {
            if(start > end) {
                output.innerHTML = `🔥 Pemakaian gas bersih: <strong>${start - end} Bar</strong>`;
            } else {
                output.innerHTML = `⚠️ Tekanan awal harus lebih besar!`;
            }
        }
    }
    startInput.addEventListener('input', hitung);
    endInput.addEventListener('input', hitung);
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>