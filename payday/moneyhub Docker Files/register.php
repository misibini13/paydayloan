<?php
require 'db_connection.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = trim($_POST['first_name']);
  $lastName = trim($_POST['last_name']);
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email address.";
  } elseif (!preg_match('/^[0-9+\s()-]+$/', $phone)) {
    $error = "Invalid phone number.";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, phone, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $username, $email, $phone, $address, $password);

    if ($stmt->execute()) {
      $success = "Account created! <a href='login.php'>Login here</a>";
    } else {
      $error = "Username or email already exists.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- MDB CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<section>
  <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: hsl(0, 0%, 96%)">
    <div class="container">
      <div class="row gx-lg-5 align-items-center">

        <!-- Left Content -->
        <div class="col-lg-6 mb-5 mb-lg-0">
          <h1 class="my-5 display-3 fw-bold ls-tight">
            Niuwave Realm <br />
            <span class="text-primary">Money Hub</span>
          </h1>
          <p style="color: hsl(217, 10%, 50.8%)">
            Welcome to our Payday Borrow application portal. Create your account to access financial services designed for you.
          </p>
        </div>

        <!-- Right Form -->
        <div class="col-lg-6 mb-5 mb-lg-0">
          <div class="card">
            <div class="card-body py-5 px-md-5">
              <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
              <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

              <form method="POST">
                <div class="row">
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <input type="text" name="first_name" id="first_name" class="form-control" required />
                      <label class="form-label" for="first_name">First name</label>
                    </div>
                  </div>
                  <div class="col-md-6 mb-4">
                    <div class="form-outline">
                      <input type="text" name="last_name" id="last_name" class="form-control" required />
                      <label class="form-label" for="last_name">Last name</label>
                    </div>
                  </div>
                </div>

                <div class="form-outline mb-4">
                  <input type="text" name="username" id="username" class="form-control" required />
                  <label class="form-label" for="username">Username</label>
                </div>

                <div class="form-outline mb-4">
                  <input type="email" name="email" id="email" class="form-control" required />
                  <label class="form-label" for="email">Email address</label>
                </div>

                <div class="form-outline mb-4">
                  <input type="tel" name="phone" id="phone" class="form-control" required />
                  <label class="form-label" for="phone">Phone number</label>
                </div>

                <div class="form-outline mb-4">
                  <input type="text" name="address" id="address" class="form-control" required />
                  <label class="form-label" for="address">Address</label>
                </div>

                <div class="form-outline mb-4">
                  <input type="password" name="password" id="password" class="form-control" required />
                  <label class="form-label" for="password">Password</label>
                </div>

      

                <button type="submit" class="btn btn-primary btn-block mb-4">
                  Sign up
                </button>
               
                <p class="text-center mt-3"><a href="login.php">Already have an account? Login</a></p>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- MDB JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
</body>
</html>
