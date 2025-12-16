<?php
// Simple registration (use once to create admin)
include('config/db.php');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $hash, $role);
        if ($stmt->execute()) {
            $message = 'User created. You can now login.';
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
include('includes/header.php');
?>
<main class="center">
  <h2>Register (create-admin)</h2>
  <?php if ($message): ?><div class="notice"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="Username" required>
    <input name="password" placeholder="Password" type="password" required>
    <select name="role"><option value="admin">admin</option><option value="user">user</option></select>
    <button type="submit">Create</button>
  </form>
  <p style="font-size:13px;color:#555;">After creating admin, delete or protect this file. yOU nOW can delete this file FTER ADDING THE DATA</p>
</main>
<?php include('includes/footer.php'); ?>
