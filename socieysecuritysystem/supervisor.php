<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_role_simple('supervisor');
$conn = db();

$visitors = [];
if ($res = $conn->query('SELECT vn.id, vn.name, vn.phone, vn.status, f.number AS flat_number FROM visitors_normal vn JOIN flats f ON f.id = vn.flat_id ORDER BY vn.id DESC')) {
  while ($r = $res->fetch_assoc()) { $visitors[] = $r; }
  $res->close();
}

$staff = [];
if ($res = $conn->query('SELECT id, name, role, phone FROM staff ORDER BY id DESC')) {
  while ($r = $res->fetch_assoc()) { $staff[] = $r; }
  $res->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Supervisor</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="nav">
    <div>Supervisor Dashboard</div>
    <div>
      <a href="admin.php">Admin</a>
      <a href="resident.php">Resident</a>
      <a href="index.php?logout=1">Logout</a>
    </div>
  </div>
  <div class="container">
    <div class="grid grid-2">
      <div class="card">
        <h3>Add Normal Visitor</h3>
        <form method="POST" action="api.php">
          <input type="hidden" name="action" value="create_visitor">
          <div style="margin-bottom:8px"><input class="input" name="name" placeholder="Name" required></div>
          <div style="margin-bottom:8px"><input class="input" name="phone" placeholder="Phone" required></div>
          <div style="margin-bottom:8px"><input class="input" name="flat_id" placeholder="Flat ID" required></div>
          <button class="btn-primary" type="submit">Create</button>
        </form>
        <h4 style="margin-top:16px">Normal Visitors</h4>
        <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Flat</th><th>Status</th></tr></thead><tbody>
          <?php foreach ($visitors as $v): ?>
            <tr>
              <td><?php echo (int)$v['id']; ?></td>
              <td><?php echo htmlspecialchars($v['name']); ?></td>
              <td><?php echo htmlspecialchars($v['phone']); ?></td>
              <td><?php echo htmlspecialchars($v['flat_number']); ?></td>
              <td><?php echo htmlspecialchars($v['status']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody></table>
      </div>
      <div class="card">
        <h3>Staff</h3>
        <form method="POST" action="api.php">
          <input type="hidden" name="action" value="create_staff">
          <div style="margin-bottom:8px"><input class="input" name="name" placeholder="Name" required></div>
          <div style="margin-bottom:8px"><input class="input" name="role" placeholder="Role" required></div>
          <div style="margin-bottom:8px"><input class="input" name="phone" placeholder="Phone" required></div>
          <button class="btn-primary" type="submit">Add</button>
        </form>
        <h4 style="margin-top:16px">Staff List</h4>
        <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Phone</th></tr></thead><tbody>
          <?php foreach ($staff as $s): ?>
            <tr>
              <td><?php echo (int)$s['id']; ?></td>
              <td><?php echo htmlspecialchars($s['name']); ?></td>
              <td><?php echo htmlspecialchars($s['role']); ?></td>
              <td><?php echo htmlspecialchars($s['phone']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody></table>
      </div>
    </div>
  </div>
</body>
</html>


