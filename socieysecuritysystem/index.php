<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
start_app_session();


if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}


$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username and password are required';
    } else {
        $conn = db();
        $stmt = $conn->prepare('SELECT id, username, password_hash, role, resident_id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        if ($user && password_verify($password, (string)$user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => (int)$user['id'],
                'username' => (string)$user['username'],
                'role' => (string)$user['role'],
                'resident_id' => isset($user['resident_id']) ? (int)$user['resident_id'] : null,
            ];
            if ($user['role'] === 'admin') { header('Location: admin.php'); exit; }
            if ($user['role'] === 'supervisor') { header('Location: supervisor.php'); exit; }
            header('Location: resident.php'); exit;
        } else {
            $error = 'Invalid credentials';
        }
    }
}


if (isset($_SESSION['user'])) {
    $r = $_SESSION['user']['role'];
    if ($r === 'admin') { header('Location: admin.php'); exit; }
    if ($r === 'supervisor') { header('Location: supervisor.php'); exit; }
    header('Location: resident.php'); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Society Security - Login</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .login-card { max-width: 360px; margin: 10vh auto; background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; }
    .title { font-weight:700; margin-bottom:12px; }
    .input { width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; margin-bottom:10px; }
    .btn { width:100%; padding:10px; background:#2563eb; color:#fff; border:0; border-radius:8px; cursor:pointer; font-weight:600; }
    .error { color:#b91c1c; background:#fee2e2; padding:8px; border-radius:8px; margin-bottom:10px; }
  </style>
  </head>
<body>
  <div class="login-card">
    <div class="title">Society Security Login</div>
    <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="POST">
      <input class="input" type="text" name="username" placeholder="Username" required>
      <input class="input" type="password" name="password" placeholder="Password" required>
      <button class="btn" type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>


