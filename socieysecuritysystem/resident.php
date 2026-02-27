<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_role_simple('resident');
$conn = db();
start_app_session();

$residentId = (int)($_SESSION['user']['resident_id'] ?? 0);
$flatId = 0;
if ($res = $conn->query('SELECT flat_id FROM residents WHERE id = ' . $residentId . ' LIMIT 1')) {
  $row = $res->fetch_assoc();
  $flatId = (int)($row['flat_id'] ?? 0);
  $res->close();
}

$requests = [];
if ($flatId > 0) {
  $stmt = $conn->prepare('SELECT id, name, phone, status FROM visitors_normal WHERE flat_id = ? ORDER BY id DESC');
  $stmt->bind_param('i', $flatId);
  $stmt->execute();
  $r = $stmt->get_result();
  if ($r) { while ($x = $r->fetch_assoc()) { $requests[] = $x; } $r->close(); }
  $stmt->close();
}

$maintenance = [];
if ($flatId > 0) {
  $stmt = $conn->prepare('SELECT id, amount, is_due, due_date, paid_at, payment_mode FROM maintenance WHERE flat_id = ? ORDER BY id DESC');
  $stmt->bind_param('i', $flatId);
  $stmt->execute();
  $r = $stmt->get_result();
  if ($r) { while ($x = $r->fetch_assoc()) { $maintenance[] = $x; } $r->close(); }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resident</title>
  <link rel="stylesheet" href="styles.css">
  <script>
    async function act(id, action){
      const form = new FormData();
      form.append('action', action);
      form.append('id', id);
      const res = await fetch('api.php', { method:'POST', body: form });
      if(res.ok) location.reload(); else alert('Failed');
    }
  </script>
</head>
<body>
  <div class="nav">
    <div>Resident Dashboard</div>
    <div>
      <a href="admin.php">Admin</a>
      <a href="supervisor.php">Supervisor</a>
      <a href="index.php?logout=1">Logout</a>
    </div>
  </div>
  <div class="container">
    <div class="card">
      <h3>Visitor Requests</h3>
      <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead><tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td><?php echo htmlspecialchars($r['phone']); ?></td>
            <td><?php echo htmlspecialchars($r['status']); ?></td>
            <td>
              <?php if ($r['status'] === 'pending'): ?>
                <button class="btn-primary" onclick="act(<?php echo (int)$r['id']; ?>,'approve_visitor')">Approve</button>
                <button class="btn-primary" style="background:#b91c1c" onclick="act(<?php echo (int)$r['id']; ?>,'deny_visitor')">Deny</button>
              <?php else: ?> - <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    </div>
    <div class="card">
      <h3>My Maintenance</h3>
      <table class="table"><thead><tr><th>ID</th><th>Amount</th><th>Due</th><th>Due Date</th><th>Paid At</th><th>Mode</th></tr></thead><tbody>
        <?php foreach ($maintenance as $m): ?>
          <tr>
            <td><?php echo (int)$m['id']; ?></td>
            <td><?php echo number_format((float)$m['amount'],2); ?></td>
            <td><?php echo $m['is_due'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo htmlspecialchars((string)$m['due_date']); ?></td>
            <td><?php echo htmlspecialchars((string)$m['paid_at']); ?></td>
            <td><?php echo htmlspecialchars((string)$m['payment_mode']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
</body>
</html>


