<?php
require_once'header.php';
$tx_ref = $_GET['ref'] ?? null;
$amount = $_GET['amount'] ?? null; // Get the amount from URL parameters

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .status_contaner{
        display: flex;
        justify-content: center;
        padding: 2rem;
    }
</style>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
   <div class="status_contaner">
        <div class="animate__animated animate__fadeInUp w-full max-w-md">
        <div class="bg-white p-8 rounded-xl shadow-lg text-center overflow-hidden border border-gray-100">
            <!-- Animated Checkmark -->
            <div class="mb-6 animate__animated animate__bounceIn">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <h1 class="text-3xl font-bold text-gray-800 mb-3">Payment Successful!</h1>
            <p class="text-gray-600 mb-6">Your transaction has been completed successfully. A receipt has been sent to your email.</p>
            
            <!-- Transaction Details -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6 text-left">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Reference No:</span>
                    <span id="tx_ref" class="font-medium text-gray-800"><?php echo htmlspecialchars($tx_ref); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-medium text-gray-800">
                        <?php 
                        if ($amount) {
                            // Format the amount as currency
                            echo '₦' . number_format($amount, 2);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-green-600">Verified</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="home" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-300 transform hover:scale-105">
                    Back to Home
                </a>
                <a href="#" class="flex-1 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition duration-300">
                    View Receipt
                </a>
            </div>

            <!-- Help Text -->
            <p class="text-sm text-gray-500 mt-6">
                Need help? <a href="contact" class="text-blue-600 hover:underline">Contact support</a>
            </p>
        </div>
    </div>
   </div>
   <?php require_once'footer.php';?>

    <script>
        // (Optional) Display the paid amount from URL parameters (if passed)
        const urlParams = new URLSearchParams(window.location.search);
        const tx_ref = urlParams.get('ref');
        const amount = urlParams.get('amount');
        
        if (tx_ref) {
            document.getElementById('tx_ref').textContent = tx_ref;
        }
        
        // You could also update the amount via JavaScript if needed
        // But we're already handling it with PHP above
    </script>
</body>
</html>