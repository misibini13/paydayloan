<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') exit('Access denied');
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $amt = floatval($_POST['amount']);
  $borrowed = date('Y-m-d');
  $due = date('Y-m-d', strtotime('+14 days'));

  $stmt = $conn->prepare("INSERT INTO loans (user_id, principal, interest_rate, borrowed_on, due_date, status, balance)
                          VALUES (?, ?, 20.0, ?, ?, 'pending', ?)");
  $stmt->bind_param("idssd", $_SESSION['user_id'], $amt, $borrowed, $due, $amt);
  $stmt->execute();

  header('Location: client_dashboard.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><title>Apply Loan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<form method="POST" class="bg-white shadow p-4 rounded" style="width:320px;">
  <h3 class="text-center mb-4">Apply for Loan</h3>
  <input name="amount" type="number" class="form-control mb-3" placeholder="Amount (SBD)" min="1" required>
  <button class="btn btn-success w-100">Apply</button>
  <p class="text-center mt-3"><a href="client_dashboard.php">Cancel</a></p>
</form>
</body>
</html>
