<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LoanApp - Fast & Easy Loans</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/d92630495d.js" crossorigin="anonymous"></script>

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .hero {
      background: #0d6efd;
      color: white;
      padding: 3rem 1rem;
      text-align: center;
      border-radius: 0 0 50% 50% / 20%;
      margin-bottom: 3rem;
    }
    .hero h1 {
      font-weight: 700;
      font-size: 3rem;
      margin-bottom: 0.5rem;
    }
    .hero p {
      font-size: 1.25rem;
      opacity: 0.9;
    }
    .feature-icon {
      font-size: 3rem;
      color: #0d6efd;
    }
    footer {
      background: #212529;
      color: white;
      padding: 2rem 1rem;
      text-align: center;
      margin-top: 5rem;
    }
    .navbar-brand img {
      height: 35px;
      margin-right: 10px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="image/logonw.png" alt="Logo"> Niuwave Realm MONEY HUB
    </a>
    <div>
      <a href="register.php" class="btn btn-outline-light me-2">Register</a>
      <a href="login.php" class="btn btn-outline-light">Login</a>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Get Your Loan Quickly & Easily</h1>
    <p>Apply for loans with simple steps and transparent fees. Fast approval and flexible repayment.</p>
  </div>
</section>

<!-- Main Container -->
<div class="container">

  <!-- Loan Calculator -->
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm p-4">
        <h4 class="card-title text-center mb-4 fw-bold">Loan Calculator</h4>
        <input id="loanAmount" type="number" class="form-control mb-3" placeholder="Enter amount (SBD)">
        <button onclick="calc()" class="btn btn-primary w-100 mb-3">Calculate</button>
        <p id="result" class="text-center fs-5 fw-semibold"></p>
      </div>
    </div>
  </div>

  <!-- Features Section -->
  <section class="text-center mt-5">
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="feature-icon mb-3">
          <i class="fa-solid fa-clock"></i>
        </div>
        <h5>Fast Approval</h5>
        <p>Get approved within minutes and receive your funds quickly.</p>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-icon mb-3">
          <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <h5>Flexible Amounts</h5>
        <p>Choose loan amounts that suit your needs, from small to large.</p>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-icon mb-3">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <h5>Easy Repayment</h5>
        <p>Repay conveniently over 14 days with simple terms.</p>
      </div>
    </div>
  </section>
  <!-- How It Works Section -->
  <center><section class="mt-5">
    <div class="card shadow-sm p-4">
      <h4 class="fw-bold mb-3 text-primary">How Our Payday Loan Service Works</h4>
      <p>
        Niuwave Money HUB offers short-term payday loans designed to help you manage unexpected financial needs.
        Our process is simple:
      </p>
      <ul class="list-group list-group-flush mb-3">
        <li class="list-group-item">📄 Apply online with basic details.</li>
        <li class="list-group-item">✅ Get approval within minutes.</li>
        <li class="list-group-item">💸 Receive funds (i) Cash (ii) directly to your Bank account.</li>
        <li class="list-group-item">📆 Repay the full amount or partial payment (including 20% interest) within 14 days.</li>
      </ul>
      <p class="mb-2">
        We are committed to transparency and fairness. All loan terms, fees, and repayment expectations are clearly stated before you confirm your loan.
      </p>
      <div class="alert alert-warning small">
        <strong>Please Note:</strong> By proceeding with a loan application, you confirm that you understand and accept our terms and repayment policy. Once agreed, complaints or disputes about interest or repayment terms will not be entertained.
      </div>
      <p class="text-muted small mb-0">
        Borrow responsibly and only what you can repay within the due time. We're here to help — not create additional stress.
      </p>
    </div>
  </section></center>
</div>

<!-- Footer -->
<footer>
  <div class="container">
    <p>&copy; 2025 Niuwave Money HUB. All rights reserved.</p>
    <p>Contact us: moneyhub@niuwave.com | +677 7829 673</p>
  </div>
</footer>

<!-- Loan Calculator Script -->
<script>
function calc() {
  const amt = parseFloat(document.getElementById('loanAmount').value);
  const res = document.getElementById('result');
  if (!amt || amt <= 0) {
    res.textContent = "Please enter a valid loan amount.";
    res.className = "text-danger";
    return;
  }
  res.textContent = `You will repay SBD$ ${(amt * 1.2).toFixed(2)} in 14 days.`;
  res.className = "text-success";
}
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
