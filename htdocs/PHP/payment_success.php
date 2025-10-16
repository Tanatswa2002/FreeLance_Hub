<?php
session_start();

// Simulate getting payment info from query
$amount = $_GET['amount'] ?? '0.00';
$ref = $_GET['ref'] ?? 'N/A';

// Optional: You could also log this mock payment in a 'payments' table if needed.
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mock Payment Gateway</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .payment-container {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }

    h1 {
      color: #0b3e82;
      margin-bottom: 20px;
    }

    .amount {
      font-size: 1.5rem;
      margin-bottom: 20px;
    }

    button {
      padding: 12px 24px;
      background-color: #0b3e82;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
    }

    button:hover {
      background-color: #093169;
    }

    .ref {
      margin-top: 15px;
      color: #666;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <div class="payment-container">
    <h1>Mock Payment</h1>
    <p class="amount">Amount: R<?= htmlspecialchars($amount) ?></p>
    <form action="payment_success.php" method="GET">
      <input type="hidden" name="ref" value="<?= htmlspecialchars($ref) ?>">
      <button type="submit">Pay Now</button>
    </form>
    <div class="ref">Reference: <?= htmlspecialchars($ref) ?></div>
  </div>
</body>
</html>
