<?php
// Login page
include('config/db.php');
session_start();
if (isset($_SESSION['user'])) header('Location: dashboard.php');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        // Use password_verify for hashed passwords
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Invalid credentials';
        }
    } else {
        $message = 'Invalid credentials';
    }
    $stmt->close();
}
include('includes/header.php');
?>
<main class="center">
  <h2>Login</h2>
  <?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="Username" required>
    <input name="password" placeholder="Password" type="password" required>
    <button type="submit">Login</button>
  </form>
  
</main>
<?php include('includes/footer.php'); ?>
