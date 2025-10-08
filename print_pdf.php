<?php
include('includes/auth.php');
require_login();
include('config/db.php');

// reuse filters
$where = []; $params = []; $types = '';
if (isset($_GET['region']) && $_GET['region'] !== '') { $where[]='region=?'; $params[]=$_GET['region']; $types.='s'; }
if (isset($_GET['category']) && $_GET['category'] !== '') { $where[]='category=?'; $params[]=$_GET['category']; $types.='s'; }
if (isset($_GET['start']) && $_GET['start'] !== '') { $where[]='order_date>=?'; $params[]=$_GET['start']; $types.='s'; }
if (isset($_GET['end']) && $_GET['end'] !== '') { $where[]='order_date<=?'; $params[]=$_GET['end']; $types.='s'; }
$where_sql = count($where)?'WHERE '.implode(' AND ',$where):'';

$sql = "SELECT order_id,product_name,category,region,sales,profit,order_date FROM sales $where_sql ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
include('includes/header.php');
?>
<main class="container">
  <h2>Sales Report (Print)</h2>
  <p>Filters applied: Region=<?php echo htmlspecialchars($_GET['region'] ?? 'All'); ?>, Category=<?php echo htmlspecialchars($_GET['category'] ?? 'All'); ?>, From=<?php echo htmlspecialchars($_GET['start'] ?? 'Any'); ?> To=<?php echo htmlspecialchars($_GET['end'] ?? 'Any'); ?></p>
  <button onclick="window.print()" class="no-print">Print / Save as PDF</button>
  <table class="table-print">
    <tr><th>Order</th><th>Product</th><th>Category</th><th>Region</th><th>Sales</th><th>Profit</th><th>Date</th></tr>
    <?php while($row=$res->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['order_id']); ?></td>
      <td><?php echo htmlspecialchars($row['product_name']); ?></td>
      <td><?php echo htmlspecialchars($row['category']); ?></td>
      <td><?php echo htmlspecialchars($row['region']); ?></td>
      <td>₹<?php echo number_format($row['sales'],2); ?></td>
      <td>₹<?php echo number_format($row['profit'],2); ?></td>
      <td><?php echo htmlspecialchars($row['order_date']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</main>
<?php include('includes/footer.php'); ?>
