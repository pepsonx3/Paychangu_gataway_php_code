<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/payment_errors.log');

ob_start();
header('Content-Type: application/json');

require_once 'connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $required = ['fname', 'email', 'table', 'application_id', 'amount', 'payment_method'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Sanitize inputs
    $fname = $conn->real_escape_string($_POST['fname']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }
    
    $table = $conn->real_escape_string($_POST['table']);
    $application_id = $conn->real_escape_string($_POST['application_id']);
    $amount = (float)$_POST['amount'];
    if ($amount <= 0) {
        throw new Exception("Invalid amount");
    }
    
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $allowed_methods = ['Bank Transfer', 'Mobile Transfer'];
    if (!in_array($payment_method, $allowed_methods)) {
        throw new Exception("Invalid payment method");
    }

    // Generate transaction reference
    $tx_ref = 'TX' . time() . rand(1000, 9999);

    // Prepare SQL statement
    $sql = "INSERT INTO payments 
            (application_id, user_token, payment_method, payment_amount, status, transaction_id, created_at) 
            VALUES 
            (?, ?, ?, ?, 'pending', ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    // Bind parameters
    $user_token = $_SESSION['user_token'] ?? null;
    $stmt->bind_param('sssds', 
        $application_id, 
        $user_token,
        $payment_method, 
        $amount, 
        $tx_ref
    );

    // Execute statement
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    ob_end_clean();
    echo json_encode([
        'status' => 'success', 
        'tx_ref' => $tx_ref,
        'amount' => $amount
    ]);
    exit;
    
} catch (Exception $e) {
    error_log('Payment Error: ' . $e->getMessage());
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment processing failed. Please try again.'
    ]);
    exit;
}
?>