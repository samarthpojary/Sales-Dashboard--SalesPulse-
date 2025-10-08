<?php
include('config/db.php');

// Read filters from GET and safely build WHERE
$where = [];
$params = [];
$types = '';
if (isset($_GET['region']) && $_GET['region'] !== '') { $where[] = 'region = ?'; $params[] = $_GET['region']; $types .= 's'; }
if (isset($_GET['category']) && $_GET['category'] !== '') { $where[] = 'category = ?'; $params[] = $_GET['category']; $types .= 's'; }
if (isset($_GET['start']) && $_GET['start'] !== '') { $where[] = 'order_date >= ?'; $params[] = $_GET['start']; $types .= 's'; }
if (isset($_GET['end']) && $_GET['end'] !== '') { $where[] = 'order_date <= ?'; $params[] = $_GET['end']; $types .= 's'; }
$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Category
$cat_sql = "SELECT category, SUM(sales) AS total FROM sales $where_sql GROUP BY category ORDER BY total DESC";
$cat_stmt = $conn->prepare($cat_sql);
if ($types) $cat_stmt->bind_param($types, ...$params);
$cat_stmt->execute();
$cat_res = $cat_stmt->get_result();
$category = ['labels'=>[],'values'=>[]];
while($r=$cat_res->fetch_assoc()){ $category['labels'][]=$r['category']; $category['values'][]=(float)$r['total']; }
$cat_stmt->close();

// Region
$reg_sql = "SELECT region, SUM(sales) AS total FROM sales $where_sql GROUP BY region ORDER BY total DESC";
$reg_stmt = $conn->prepare($reg_sql);
if ($types) $reg_stmt->bind_param($types, ...$params);
$reg_stmt->execute();
$reg_res = $reg_stmt->get_result();
$region = ['labels'=>[],'values'=>[]];
while($r=$reg_res->fetch_assoc()){ $region['labels'][]=$r['region']; $region['values'][]=(float)$r['total']; }
$reg_stmt->close();

// Monthly
$mon_sql = "SELECT DATE_FORMAT(order_date, '%b %Y') AS m, SUM(sales) AS total FROM sales $where_sql GROUP BY m ORDER BY MIN(order_date) ASC";
$mon_stmt = $conn->prepare($mon_sql);
if ($types) $mon_stmt->bind_param($types, ...$params);
$mon_stmt->execute();
$mon_res = $mon_stmt->get_result();
$monthly = ['labels'=>[],'values'=>[]];
while($r=$mon_res->fetch_assoc()){ $monthly['labels'][]=$r['m']; $monthly['values'][]=(float)$r['total']; }
$mon_stmt->close();

header('Content-Type: application/json');
echo json_encode(['category'=>$category,'region'=>$region,'monthly'=>$monthly]);
?>