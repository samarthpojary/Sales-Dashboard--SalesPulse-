<?php
include('includes/auth.php');
require_admin();
include('config/db.php');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $f = $_FILES['file'];
    if ($f['error'] === UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'])) {
        $handle = fopen($f['tmp_name'],'r');
        if ($handle) {
            $stmt = $conn->prepare("INSERT INTO sales (order_id, product_name, category, region, sales, profit, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)"); 
            if (!$stmt) { $message = 'DB prepare failed: '.$conn->error; }
            else {
                $first = fgetcsv($handle);
                if ($first && isset($first[4]) && !is_numeric(str_replace(',','',$first[4]))) {
                    // header
                } else {
                    if ($first && count($first)>=7) {
                        $row = $first;
                        $order_id=$row[0]; $product_name=$row[1]; $category=$row[2]; $region=$row[3];
                        $sales = floatval(str_replace(',','',$row[4])); $profit = floatval(str_replace(',','',$row[5]));
                        $order_date = date('Y-m-d', strtotime($row[6]));
                        $stmt->bind_param('ssssdds', $order_id,$product_name,$category,$region,$sales,$profit,$order_date);
                        $stmt->execute();
                    }
                }
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row)<7) continue;
                    $order_id=$row[0]; $product_name=$row[1]; $category=$row[2]; $region=$row[3];
                    $sales = floatval(str_replace(',','',$row[4])); $profit = floatval(str_replace(',','',$row[5]));
                    $order_date = date('Y-m-d', strtotime($row[6]));
                    $stmt->bind_param('ssssdds', $order_id,$product_name,$category,$region,$sales,$profit,$order_date);
                    $stmt->execute();
                }
                $stmt->close();
                $message = 'CSV imported successfully.';
            }
            fclose($handle);
        } else { $message = 'Could not open file.'; }
    } else { $message = 'Upload error.'; }
}
include('includes/header.php');
?>
<main class="container">
  <h2>Upload CSV (admin)</h2>
  <?php if ($message): ?><div class="notice"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="file" accept=".csv" required>
    <button type="submit">Upload</button>
  </form>
  <h3>Sample CSV</h3>
  <pre><?php echo htmlspecialchars(file_get_contents('data/sample_sales.csv')); ?></pre>
</main>
<?php include('includes/footer.php'); ?>
