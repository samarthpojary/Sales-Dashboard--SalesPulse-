<?php
include('includes/auth.php');
require_login();
include('config/db.php');
include('includes/header.php');

// Fetch filter parameters
$region = isset($_GET['region']) ? $_GET['region'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';

// For display, build where clause for PHP queries when needed
$where = [];
$params = [];
$types = '';
if ($region !== '') { $where[] = 'region = ?'; $params[] = $region; $types .= 's'; }
if ($category !== '') { $where[] = 'category = ?'; $params[] = $category; $types .= 's'; }
if ($start !== '') { $where[] = 'order_date >= ?'; $params[] = $start; $types .= 's'; }
if ($end !== '') { $where[] = 'order_date <= ?'; $params[] = $end; $types .= 's'; }
$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// KPIs
$kpi_sql = "SELECT COUNT(*) AS orders, COALESCE(SUM(sales),0) AS total_sales, COALESCE(SUM(profit),0) AS total_profit FROM sales $where_sql";
$kpi_stmt = $conn->prepare($kpi_sql);
if ($types) $kpi_stmt->bind_param($types, ...$params);
$kpi_stmt->execute();
$kpi = $kpi_stmt->get_result()->fetch_assoc();
$kpi_stmt->close();

// Get distinct categories and regions for filter dropdowns
$catRes = $conn->query('SELECT DISTINCT category FROM sales');
$categories = []; while($r=$catRes->fetch_assoc()) $categories[]=$r['category'];
$regRes = $conn->query('SELECT DISTINCT region FROM sales');
$regions = []; while($r=$regRes->fetch_assoc()) $regions[]=$r['region'];
?>
<main class="container">
  <h2>Dashboard</h2>
  <form method="get" class="filters" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;">
    <div><label>Region</label><br>
      <select name="region"><option value="">All</option><?php foreach($regions as $r): ?><option value="<?php echo htmlspecialchars($r); ?>" <?php if($r===$region) echo 'selected'; ?>><?php echo htmlspecialchars($r); ?></option><?php endforeach; ?></select></div>
    <div><label>Category</label><br>
      <select name="category"><option value="">All</option><?php foreach($categories as $c): ?><option value="<?php echo htmlspecialchars($c); ?>" <?php if($c===$category) echo 'selected'; ?>><?php echo htmlspecialchars($c); ?></option><?php endforeach; ?></select></div>
    <div><label>Start</label><br><input type="date" name="start" value="<?php echo htmlspecialchars($start); ?>"></div>
    <div><label>End</label><br><input type="date" name="end" value="<?php echo htmlspecialchars($end); ?>"></div>
    <div><button type="submit">Apply</button> <a class="button" href="dashboard.php">Reset</a></div>
  </form>

  <div class="kpis" style="display:flex;gap:12px;margin-top:12px;">
    <div class="card"><div class="kpi-title">Total Sales</div><div class="kpi-value">₹<?php echo number_format($kpi['total_sales'],2); ?></div></div>
    <div class="card"><div class="kpi-title">Total Profit</div><div class="kpi-value">₹<?php echo number_format($kpi['total_profit'],2); ?></div></div>
    <div class="card"><div class="kpi-title">Orders</div><div class="kpi-value"><?php echo number_format($kpi['orders']); ?></div></div>
  </div>

  <div style="margin-top:18px;"><canvas id="monthlyChart" height="120"></canvas></div>
  <div style="display:flex;gap:20px;margin-top:18px;flex-wrap:wrap;">
    <div style="flex:1;min-width:300px;"><canvas id="categoryChart" height="250"></canvas></div>
    <div style="flex:1;min-width:300px;"><canvas id="regionChart" height="250"></canvas></div>
  </div>

  <h3 style="margin-top:18px;">Recent Orders</h3>
  <table class="data-table">
    <tr><th>Order</th><th>Product</th><th>Category</th><th>Region</th><th>Sales</th><th>Profit</th><th>Date</th></tr>
    <?php
    $listSql = "SELECT order_id, product_name, category, region, sales, profit, order_date FROM sales $where_sql ORDER BY order_date DESC LIMIT 100";
    $listStmt = $conn->prepare($listSql);
    if ($types) $listStmt->bind_param($types, ...$params);
    $listStmt->execute();
    $listRes = $listStmt->get_result();
    while ($row = $listRes->fetch_assoc()):
    ?>
    <tr>
      <td><?php echo htmlspecialchars($row['order_id']); ?></td>
      <td><?php echo htmlspecialchars($row['product_name']); ?></td>
      <td><?php echo htmlspecialchars($row['category']); ?></td>
      <td><?php echo htmlspecialchars($row['region']); ?></td>
      <td>₹<?php echo number_format($row['sales'],2); ?></td>
      <td>₹<?php echo number_format($row['profit'],2); ?></td>
      <td><?php echo htmlspecialchars($row['order_date']); ?></td>
    </tr>
    <?php endwhile; $listStmt->close(); ?>
  </table>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/charts.js"></script>
<?php include('includes/footer.php'); ?>
