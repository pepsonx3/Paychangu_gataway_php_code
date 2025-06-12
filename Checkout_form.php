<?php 
// get Data from your database here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Processing</title>
    <style>
        body {
            background-color: #f7fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-container {
            display: flex;
            justify-content: center;
            padding: 2rem;
        }
        .payment-modal {
            background-color: white;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 32rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .paychangu-promo {
            background: #01b8fc;
            padding: 1.5rem;
            text-align: center;
            color: white;
        }
        .paychangu-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .paychangu-logo {
            height: 3.5rem;
            margin: 0.5rem auto; /* Centered horizontally */
        }
        .payment-content {
            padding: 2rem;
        }
        .form-label {
            display: block;
            color: #4a5568;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #01b8fc;
            box-shadow: 0 0 0 3px rgba(1, 184, 252, 0.2);
        }
        .helper-text {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.25rem;
        }
        .error-text {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .button {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .button-cancel {
            background-color: #edf2f7;
            color: #4a5568;
        }
        .button-cancel:hover {
            background-color: #e2e8f0;
        }
        .button-submit {
            background-color: #01b8fc;
            color: white;
        }
        .button-submit:hover {
            background-color: #0188fc;
        }
        .heading {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="payment-container">
    <div class='payment-modal'>
        <div class="paychangu-promo">
            <h1 class="heading">Pay Securely With</h1>
            <img src="../media/changu.png" alt="Paychangu" class="paychangu-logo mx-auto">
        </div>
        
        <div class="payment-content">
            <form action="../configs/update_status.php" method="POST" id="paymentForm" class="space-y-6">
                <input type="hidden" name="fname" value="<?= htmlspecialchars($loan_data['business_owner_name'] ?? $loan_data['full_name'] ?? $loan_data['fullName'] ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($loan_data['email'] ?? $loan_data['business_owner_email'] ?? '') ?>">
                <input type="hidden" name="table" value="<?= htmlspecialchars($loan_type ?? '') ?>">
                <input type="hidden" name="application_id" value="<?= htmlspecialchars($loan_data['application_id'] ?? '') ?>">
                
                <div>
                    <label for="payments_amount" class="form-label">Amount (MWK)</label>
                    <input type="number" id="payments_amount" name="amount" 
                           class="form-input" 
                           min="50" max="<?= $remaining_balance ?>" step="1000" required>
                    <div id="errorMessage" class="error-text"></div>
                    <p class="helper-text">Minimum: MWK 50,000 | Remaining Balance: MWK <?= number_format($remaining_balance) ?></p>
                </div>
                
                <div>
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select id="payment_method" name="payment_method" 
                            class="form-input" required>
                        <option value="" selected disabled>Select Payment Method</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Mobile Transfer">Mobile Transfer</option>
                    </select>
                </div>
                
                <div class="button-group">
                    <a href="home" type="button" 
                            class="button button-cancel">
                        Cancel
                    </a>
                    
                    <button onclick="makePayment()" type="button" id="submitBtn" class="button button-submit">
                        Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once 'footer.php' ?>

<!-- Paychangu Integration -->
<div id="wrapper"></div>
<script src="https://in.paychangu.com/js/popup.js"></script>
    
<script>
  function makePayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    const errorMessage = document.getElementById('errorMessage');
    const submitBtn = document.getElementById('submitBtn');

    errorMessage.textContent = '';
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';

    const amount = parseFloat(formData.get('amount'));
    const balance = <?= $remaining_balance ?>;

    if (isNaN(amount) || amount < 50) {
        errorMessage.textContent = 'Amount must be at least MWK 50,000';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Process Payment';
        return;
    } else if (amount > balance) {
        errorMessage.textContent = `Amount cannot exceed remaining balance of MWK ${balance.toLocaleString()}`;
        submitBtn.disabled = false;
        submitBtn.textContent = 'Process Payment';
        return;
    }

    // First save the payment details to get a tx_ref
    fetch('YOUR_FILE_THAT_PROCESS_THE_INPUTS', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Now initiate Paychangu with the received tx_ref
            PaychanguCheckout({
                "public_key": "YOUR_SECRET_KEY_HERE",
                "tx_ref": data.tx_ref,
                "amount": data.amount,
                "currency": "MWK",
                "callback_url": "YOUR_HOSTED_CALLBACK_URL_HERE(FILE)",
                "return_url": "YOUR_RETURN_URL_HERE",
                "customer": {
                    "email": formData.get('email'),
                    "first_name": formData.get('fname').split(' ')[0] || formData.get('fname'),
                    "last_name": formData.get('fname').split(' ').slice(1).join(' ') || formData.get('fname'),
                },
                "customization": {
                    "title": "Loan Payment",
                    "description": "Payment for <?= htmlspecialchars($loan_type ?? 'Loan') ?>",
                },
                "meta": {
                    "application_id": formData.get('application_id'),
                    "payment_method": formData.get('payment_method'),
                    "loan_type": formData.get('table')
                }
            });
        } else {
            throw new Error(data.message || 'Payment processing failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorMessage.textContent = 'Error: ' + error.message;
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Process Payment';
    });
}
</script>
</body>
</html>