<?php
session_start();
require 'db_connection.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = $_POST['username'];
  $p = $_POST['password'];

  $stmt = $conn->prepare("
    SELECT id, password, role, is_approved
    FROM users
    WHERE username = ?
  ");
  $stmt->bind_param("s", $u);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    if (!password_verify($p, $row['password'])) {
      $error = 'Invalid password.';
    } elseif (!$row['is_approved']) {
      $error = 'Account pending admin approval.';
    } else {
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['role'] = $row['role'];
      header($row['role'] === 'admin' ? 'Location: admin_dashboard.php' : 'Location: client_dashboard.php');
      exit;
    }
  } else {
    $error = 'User not found.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .gradient-custom-2 {
      /* fallback for old browsers */
      background: #fccb90;

      /* Chrome 10-25, Safari 5.1-6 */
      background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

      /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
      background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
    }

    @media (min-width: 768px) {
      .gradient-form {
        height: 100vh !important;
      }
    }

    @media (min-width: 769px) {
      .gradient-custom-2 {
        border-top-right-radius: .3rem;
        border-bottom-right-radius: .3rem;
      }
    }
  </style>
</head>

<body>
  <section class="h-100 gradient-form" style="background-color: #eee;">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-xl-10">
          <div class="card rounded-3 text-black">
            <div class="row g-0">
              <div class="col-lg-6">
                <div class="card-body p-md-5 mx-md-4">

                  <div class="text-center">
                    <img src="image/logonw.png" style="width: 100px;" alt="logo">
                    <h4 class="mt-1 mb-5 pb-1">Niuwave Money HUB</h4>
                  </div>

                  <form method="POST" action="">

                    <p>Please login to your account</p>

                    <?php if (!empty($error)) : ?>
                      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <div data-mdb-input-init class="form-outline mb-4">
					  <label class="form-label" for="form2Example11">Username</label>
                      <input type="text" id="form2Example11" name="username" class="form-control" placeholder="username" required />
                      
                    </div>

                    <div data-mdb-input-init class="form-outline mb-4">
					  <label class="form-label" for="form2Example22">Password</label>
                      <input type="password" id="form2Example22" name="password" class="form-control" required />
                      
                    </div>

                    <div class="text-center pt-1 mb-5 pb-1">
                      <button type="submit" data-mdb-button-init data-mdb-ripple-init
                        class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Log in</button>
                      
                    </div>

                    <div class="d-flex align-items-center justify-content-center pb-4">
                      <p class="mb-0 me-2">Don't have an account?</p>
                      <a href="register.php" class="btn btn-outline-danger">Create new</a>
                    </div>

                  </form>

                </div>
              </div>
              <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                  <h4 class="mb-4">WELCOME TO OUR HOME</h4>
                  <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                    exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>
