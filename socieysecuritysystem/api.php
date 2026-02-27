<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
start_app_session();
$conn = db();

$action = $_POST['action'] ?? '';

function ensure(string $role): void {
  if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) { json_out(['error'=>'forbidden'], 403); }
}

switch ($action) {
  case 'create_building':
    ensure('admin');
    $name = trim((string)($_POST['name'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    if ($name === '' || $address === '') json_out(['error'=>'missing'], 400);
    $stmt = $conn->prepare('INSERT INTO buildings (name, address) VALUES (?, ?)');
    $stmt->bind_param('ss', $name, $address);
    $stmt->execute();
    $id = $stmt->insert_id; $stmt->close();
    json_out(['ok'=>true,'id'=>(int)$id]);

  case 'create_flat':
    ensure('admin');
    $buildingId = (int)($_POST['building_id'] ?? 0);
    $number = trim((string)($_POST['number'] ?? ''));
    $floor = (int)($_POST['floor'] ?? 0);
    if ($buildingId<=0 || $number==='') json_out(['error'=>'missing'],400);
    $stmt = $conn->prepare('INSERT INTO flats (building_id, number, floor) VALUES (?, ?, ?)');
    $stmt->bind_param('isi', $buildingId, $number, $floor);
    $stmt->execute(); $id = $stmt->insert_id; $stmt->close();
    json_out(['ok'=>true,'id'=>(int)$id]);

  case 'create_visitor':
    ensure('supervisor');
    $name = trim((string)($_POST['name'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $flatId = (int)($_POST['flat_id'] ?? 0);
    if ($name===''||$phone===''||$flatId<=0) json_out(['error'=>'missing'],400);
    $stmt = $conn->prepare('INSERT INTO visitors_normal (name, phone, flat_id) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $name, $phone, $flatId);
    $stmt->execute(); $id = $stmt->insert_id; $stmt->close();
    json_out(['ok'=>true,'id'=>(int)$id]);

  case 'approve_visitor':
  case 'deny_visitor':
    if (!isset($_SESSION['user']) || $_SESSION['user']['role']!=='resident') json_out(['error'=>'forbidden'],403);
    $id = (int)($_POST['id'] ?? 0);
    if ($id<=0) json_out(['error'=>'bad id'],400);
    $residentId = (int)($_SESSION['user']['resident_id'] ?? 0);
    $flatId = 0;
    $stmt_flat = $conn->prepare('SELECT flat_id FROM residents WHERE id = ? LIMIT 1');
    $stmt_flat->bind_param('i', $residentId);
    $stmt_flat->execute();
    $res = $stmt_flat->get_result();
    if ($res) {
        $row = $res->fetch_assoc();
        $flatId = (int)($row['flat_id'] ?? 0);
    }
    $stmt_flat->close();
    $status = $action==='approve_visitor' ? 'approved' : 'denied';
    $uid = (int)$_SESSION['user']['id'];
    $stmt = $conn->prepare('UPDATE visitors_normal SET status=?, approved_by_user_id=?, approved_at=NOW() WHERE id=? AND flat_id=?');
    $stmt->bind_param('siii', $status, $uid, $id, $flatId);
    $stmt->execute(); $stmt->close();
    json_out(['ok'=>true]);

  case 'create_staff':
    ensure('supervisor');
    $name = trim((string)($_POST['name'] ?? ''));
    $role = trim((string)($_POST['role'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    if ($name===''||$role===''||$phone==='') json_out(['error'=>'missing'],400);
    $stmt = $conn->prepare('INSERT INTO staff (name, role, phone) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $role, $phone);
    $stmt->execute(); $id = $stmt->insert_id; $stmt->close();
    json_out(['ok'=>true,'id'=>(int)$id]);

  default:
    json_out(['error'=>'unknown action'], 400);
}


