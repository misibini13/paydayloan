<?php
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Access denied');
}
require 'db_connection.php';

/* -----------------------
   LOAN ACTIONS
------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loan_id = (int)$_POST['loan_id'];

    // Approve (set 20% interest and due in 14 days from today)
    if (isset($_POST['approve'])) {
        $stmt = $conn->prepare("
            UPDATE loans
            SET status='approved',
                balance = principal * 1.2,
                borrowed_on = CURDATE(),
                due_date = DATE_ADD(CURDATE(), INTERVAL 14 DAY)
            WHERE id = ?
        ");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();

    // Reject
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("UPDATE loans SET status='rejected' WHERE id=?");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();

    // Record repayment (interest first, then principal)
    } elseif (isset($_POST['update_repayment']) && isset($_POST['paid'])) {
        $paid = floatval($_POST['paid']);

        // Get current principal
        $loan = $conn->query("SELECT principal FROM loans WHERE id = {$loan_id}")->fetch_assoc();
        $principal = floatval($loan['principal']);

        // Interest on current cycle is 20% of current principal
        $interest = $principal * 0.20;

        // Apply payment to interest first
        if ($paid >= $interest) {
            $paid -= $interest;                 // interest cleared
            $new_principal = max(0, $principal - $paid);  // remaining reduces principal
        } else {
            // Paid less than interest → principal unchanged
            $new_principal = $principal;
        }

        $today = date('Y-m-d');

        if ($new_principal <= 0) {
            // Fully repaid — close the loan
            $stmt = $conn->prepare("
                UPDATE loans
                SET principal = 0,
                    balance = 0,
                    status = 'repaid',
                    payment_date = ?,
                    due_date = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ssi", $today, $today, $loan_id);
            $stmt->execute();
        } else {
            // Partial repayment → start a NEW cycle from payment date
            $new_balance  = $new_principal * 1.20; // principal + 20%
            $new_due_date = date('Y-m-d', strtotime($today . ' +14 days'));

            $stmt = $conn->prepare("
                UPDATE loans
                SET principal = ?,
                    balance = ?,
                    borrowed_on = ?,   -- reset cycle to payment date
                    due_date = ?,      -- 14 days from payment
                    payment_date = ?,  -- when the payment happened
                    status = 'approved'
                WHERE id = ?
            ");
            $stmt->bind_param("ddsssi", $new_principal, $new_balance, $today, $new_due_date, $today, $loan_id);
            $stmt->execute();
        }

    // Delete loan
    } elseif (isset($_POST['delete_loan'])) {
        $stmt = $conn->prepare("DELETE FROM loans WHERE id=?");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
    }
}

/* -----------------------
   USER MANAGEMENT
------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'approve_user':
            if (isset($_POST['user_id'])) {
                $stmt = $conn->prepare("UPDATE users SET is_approved=1 WHERE id=?");
                $stmt->bind_param("i", $_POST['user_id']);
                $stmt->execute();
            }
            break;
        case 'create_user':
            if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['role']) && !empty($_POST['email']) && !empty($_POST['phone'])) {
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role, is_approved, email, phone) VALUES (?, ?, ?, 1, ?, ?)");
                $stmt->bind_param("sssss", $_POST['username'], $hash, $_POST['role'], $_POST['email'], $_POST['phone']);
                $stmt->execute();
            }
            break;
        case 'reset_password':
            if (isset($_POST['reset_user_id']) && !empty($_POST['new_password'])) {
                $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $hash, $_POST['reset_user_id']);
                $stmt->execute();
            }
            break;
        case 'delete_user':
            if (isset($_POST['delete_user_id'])) {
                $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
                $stmt->bind_param("i", $_POST['delete_user_id']);
                $stmt->execute();
            }
            break;
    }
}

/* -----------------------
   FETCH DATA FOR VIEW
------------------------ */
$loans = $conn->query("
    SELECT l.*, u.username
    FROM loans l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.id DESC
");

$users = $conn->query("SELECT id, username, role, is_approved, email, phone FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    nav.navbar {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1030;
    }
    main.content {
      margin-left: 240px;
      padding: 5rem 2rem 2rem;
    }
    aside.sidebar {
      width: 240px;
      position: fixed;
      top: 56px; /* navbar height */
      left: 0;
      height: calc(100% - 56px);
      background-color: #343a40;
      padding-top: 1rem;
      color: white;
    }
    aside.sidebar .nav-link {
      color: rgba(255,255,255,0.8);
      font-weight: 500;
    }
    aside.sidebar .nav-link.active {
      background-color: #495057;
      color: white;
    }
    .table th, .table td {
      vertical-align: middle;
      text-align: center;
      white-space: nowrap;
    }
    .form-control-sm {
      max-width: 150px;
      display: inline-block;
    }
    .user-actions form {
      display: inline-block;
      margin-left: 0.25rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">LoanApp Admin</a>
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>

<aside class="sidebar d-flex flex-column">
  <nav class="nav flex-column">
    <a class="nav-link active" href="#loans" data-bs-toggle="tab">Loan Applications</a>
    <a class="nav-link" href="#users" data-bs-toggle="tab">User Management</a>
  </nav>
</aside>

<main class="content">
  <div class="tab-content">
    <!-- Loans Tab -->
    <section id="loans" class="tab-pane fade show active">
      <h2 class="mb-4">Loan Applications</h2>
      <div class="table-responsive mb-5">
        <table class="table table-striped table-hover bg-white shadow-sm rounded">
          <thead class="table-dark">
            <tr>
              <th>User</th>
              <th>Principal (SBD)</th>
              <th>Balance (SBD)</th>
              <th>Status</th>
              <th>Borrowed</th>
              <th>Due Date</th>
              <th>Payment Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($r = $loans->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['username']) ?></td>
              <td><?= number_format($r['principal'], 2) ?></td>
              <td><?= number_format($r['balance'], 2) ?></td>
              <td>
                <span class="badge
                  <?= $r['status']=='approved'?'bg-warning':
                     ($r['status']=='repaid'?'bg-success':
                     ($r['status']=='rejected'?'bg-danger':'bg-secondary')) ?>">
                  <?= htmlspecialchars($r['status']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($r['borrowed_on']) ?></td>
              <td><?= $r['due_date'] && $r['due_date']!=='0000-00-00' ? htmlspecialchars($r['due_date']) : '—' ?></td>
              <td><?= $r['payment_date'] && $r['payment_date']!=='0000-00-00' ? htmlspecialchars($r['payment_date']) : '—' ?></td>
              <td>
                <form method="POST" class="d-flex flex-wrap gap-1 justify-content-center">
                  <input type="hidden" name="loan_id" value="<?= $r['id'] ?>">
                  <?php if ($r['status']==='pending'): ?>
                    <button name="approve" class="btn btn-sm btn-primary">Approve</button>
                    <button name="reject" class="btn btn-sm btn-danger">Reject</button>
                  <?php elseif ($r['status']==='approved'): ?>
                    <input type="number" name="paid" step="0.01" placeholder="Paid" class="form-control form-control-sm" required>
                    <button name="update_repayment" class="btn btn-sm btn-success">Record Payment</button>
                  <?php endif; ?>
                  <button name="delete_loan" onclick="return confirm('Delete this loan?')" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Users Tab -->
    <section id="users" class="tab-pane fade">
      <h2 class="mb-4">User Management</h2>

      <h5>Create New User</h5>
      <form method="POST" class="row g-3 mb-5">
        <input type="hidden" name="action" value="create_user">
        <div class="col-md-2"><input name="username" class="form-control" placeholder="Username" required></div>
        <div class="col-md-2"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
        <div class="col-md-2"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
        <div class="col-md-2"><input name="phone" class="form-control" placeholder="Phone" required></div>
        <div class="col-md-2">
          <select name="role" class="form-select" required>
            <option value="client">Client</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success w-100">Create</button>
        </div>
      </form>

      <h5>Existing Users</h5>
      <div class="table-responsive">
        <table class="table table-striped bg-white shadow-sm rounded">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Approved?</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
              <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['is_approved'] ? 'Yes' : 'No' ?></td>
                <td class="user-actions">
                  <?php if (!$user['is_approved']): ?>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="approve_user">
                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                      <button class="btn btn-sm btn-primary" type="submit">Approve</button>
                    </form>
                  <?php endif; ?>

                  <!-- Reset Password -->
                  <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#resetPassword<?= $user['id'] ?>" aria-expanded="false" aria-controls="resetPassword<?= $user['id'] ?>">
                    Reset Password
                  </button>
                  <div class="collapse mt-2" id="resetPassword<?= $user['id'] ?>">
                    <form method="POST" class="d-flex gap-1">
                      <input type="hidden" name="action" value="reset_password">
                      <input type="hidden" name="reset_user_id" value="<?= $user['id'] ?>">
                      <input type="password" name="new_password" class="form-control form-control-sm" placeholder="New Password" required>
                      <button class="btn btn-sm btn-success" type="submit">Save</button>
                    </form>
                  </div>

                  <form method="POST" style="display:inline;" onsubmit="return confirm('Delete user <?= htmlspecialchars($user['username']) ?>?')">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Handle tab navigation
  const tabLinks = document.querySelectorAll('aside.sidebar .nav-link');
  const tabPanes = document.querySelectorAll('main.content .tab-pane');

  tabLinks.forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      tabLinks.forEach(l => l.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('show', 'active'));

      link.classList.add('active');
      const target = link.getAttribute('href');
      document.querySelector(target).classList.add('show', 'active');
    });
  });
</script>

</body>
</html>
