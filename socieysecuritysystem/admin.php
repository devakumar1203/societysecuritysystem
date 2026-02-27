<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_role_simple('admin');
$conn = db();

$buildings = [];
if ($res = $conn->query('SELECT id, name, address FROM buildings ORDER BY id DESC')) {
  while ($r = $res->fetch_assoc()) { $buildings[] = $r; }
  $res->close();
}
$flats = [];
if ($res = $conn->query('SELECT f.id, f.number, f.floor, b.name AS building_name FROM flats f JOIN buildings b ON b.id = f.building_id ORDER BY f.id DESC')) {
  while ($r = $res->fetch_assoc()) { $flats[] = $r; }
  $res->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="nav">
    <div>Admin Dashboard</div>
    <div>
      <a href="supervisor.php">Supervisor</a>
      <a href="resident.php">Resident</a>
      <a href="index.php?logout=1">Logout</a>
    </div>
  </div>
  <div class="container">
    <div class="grid grid-2">
      <div class="card">
        <h3>Create Building</h3>
        <form method="POST" action="api.php">
          <input type="hidden" name="action" value="create_building">
          <div style="margin-bottom:8px"><input class="input" name="name" placeholder="Building name" required></div>
          <div style="margin-bottom:8px"><input class="input" name="address" placeholder="Address" required></div>
          <button class="btn-primary" type="submit">Add</button>
        </form>
        <h4 style="margin-top:16px">Buildings</h4>
        <table class="table"><thead><tr><th>ID</th><th>Name</th><th>Address</th></tr></thead><tbody>
          <?php foreach ($buildings as $b): ?>
            <tr><td><?php echo (int)$b['id']; ?></td><td><?php echo htmlspecialchars($b['name']); ?></td><td><?php echo htmlspecialchars($b['address']); ?></td></tr>
          <?php endforeach; ?>
        </tbody></table>
      </div>
      <div class="card">
        <h3>Create Flat</h3>
        <form method="POST" action="api.php">
          <input type="hidden" name="action" value="create_flat">
          <div style="margin-bottom:8px"><input class="input" name="building_id" placeholder="Building ID" required></div>
          <div style="margin-bottom:8px"><input class="input" name="number" placeholder="Flat Number" required></div>
          <div style="margin-bottom:8px"><input class="input" type="number" name="floor" placeholder="Floor" required></div>
          <button class="btn-primary" type="submit">Add</button>
        </form>
      </div>
    </div>
    <div class="card">
      <h3>Flats</h3>
      <table class="table"><thead><tr><th>ID</th><th>Number</th><th>Floor</th><th>Building</th></tr></thead><tbody>
        <?php foreach ($flats as $f): ?>
          <tr>
            <td><?php echo (int)$f['id']; ?></td>
            <td><?php echo htmlspecialchars($f['number']); ?></td>
            <td><?php echo (int)$f['floor']; ?></td>
            <td><?php echo htmlspecialchars($f['building_name']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>
</body>
</html>


