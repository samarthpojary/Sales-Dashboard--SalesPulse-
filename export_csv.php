<?php
include('includes/auth.php');
require_login();
include('config/db.php');

// Build same filters as dashboard
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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=sales_export.csv');
$out = fopen('php://output','w');
fputcsv($out, ['order_id','product_name','category','region','sales','profit','order_date']);
while($row = $res->fetch_assoc()) fputcsv($out, $row);
fclose($out);
exit;
?>