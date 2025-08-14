<?php
session_start();
if ($_SESSION['role'] !== 'client') exit('Access denied');
require 'db_connection.php';
$loans = $conn->query("SELECT * FROM loans WHERE user_id={$_SESSION['user_id']} ORDER BY borrowed_on DESC");
$totalLoans = $loans->num_rows;
$totalBalance = $conn->query("SELECT SUM(balance) FROM loans WHERE user_id={$_SESSION['user_id']}")->fetch_row()[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Client Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">LoanApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto"><li class="nav-item"><a href="apply_loan.php" class="btn btn-success me-2">Apply Loan</a></li><li class="nav-item"><a href="logout.php" class="btn btn-outline-light">Logout</a></li></ul>
    </div>
  </div>
</nav>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">Total Loans</h6>
          <h3><?= $totalLoans ?></h3>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm text-center">
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">Total Balance (SBD)</h6>
          <h3><?= number_format($totalBalance, 2) ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white rounded p-3 mt-4">
    <canvas id="loanChart" height="120"></canvas>
  </div>

  <div class="table-responsive mt-4">
    <table class="table table-hover bg-white">
      <thead class="table-light"><tr><th>Principal</th><th>Balance</th><th>Status</th><th>Borrowed On</th><th>Due Date</th></tr></thead>
      <tbody>
        <?php while ($l = $loans->fetch_assoc()): ?>
        <tr>
          <td><?= number_format($l['principal'],2) ?></td>
          <td><?= number_format($l['balance'],2) ?></td>
          <td><span class="badge <?= $l['status']=='approved'?'bg-warning':($l['status']=='repaid'?'bg-success':'bg-secondary') ?>"><?= htmlspecialchars($l['status']) ?></span></td>
          <td><?= $l['borrowed_on'] ?></td>
          <td><?= $l['due_date'] !== '0000-00-00' ? htmlspecialchars($l['due_date']) : '—' ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<script>
const ctx = document.getElementById('loanChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [<?php foreach ($loans as $row) echo "'{$row['borrowed_on']}',"; ?>],
    datasets: [{
      label: 'Balance Over Time',
      data: [<?php foreach ($loans as $row) echo $row['balance'].',';?>],
      backgroundColor: 'rgba(54, 162, 235, 0.6)'
    }]
  },
  options: { responsive:true, maintainAspectRatio:false }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
