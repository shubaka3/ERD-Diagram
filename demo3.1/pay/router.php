

<?php


// Your existing logic for /payment goes here

session_start();
require_once 'PaymentVnpayClass.php'; // Required for payment handling
require_once 'PaymentMoMoClass.php';
// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Return CORS headers and exit
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(200);
    exit;
}

// Rest of your PHP script (e.g., handling /payment)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

// Get the request URI and extract the path (ignore query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = trim($uri, '/');
$urlParts = explode('/', $url);

// Ensure the request is coming from the correct port (8090)
if ($_SERVER['SERVER_PORT'] != '8000') {
    http_response_code(403);
    die(json_encode(['message' => 'Invalid port access']));
}

// 🔹 Serve Subscription Cards at /payment or /router.php/payment
if ($url === 'payment' || $url === 'router.php/payment') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Serve the HTML with subscription cards or payment method selection
        header("Content-Type: text/html");

        // Check if a price has been selected (i.e., user has chosen a card)
        $price = isset($_GET['price']) ? (int)$_GET['price'] : 0;
        $formatted_price = number_format($price, 0, ',', '.') . 'đ';

        // If price is set, show payment method selection; otherwise, show subscription cards
        if ($price > 0) {
            // Display payment method selection page
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Choose Payment Method</title>
                <style>
                    body {
                        width: 100vw;
                        height: 100vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background-color: #1a1a1a;
                        font-family: Arial, sans-serif;
                        margin: 0;
                    }
                    .payment-method-container {
                        background: white;
                        border-radius: 20px;
                        padding: 30px;
                        text-align: center;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                        width: 400px;
                    }
                    .payment-method-container h2 {
                        color: #333;
                        margin-bottom: 20px;
                    }
                    .payment-method-container p {
                        font-size: 1.2em;
                        margin-bottom: 30px;
                        color: #555;
                    }
                    .payment-methods {
                        display: flex;
                        justify-content: center;
                        gap: 20px;
                    }
                    .payment-method {
                        text-decoration: none;
                        padding: 15px 30px;
                        border-radius: 25px;
                        font-size: 1em;
                        font-weight: bold;
                        color: white;
                        transition: transform 0.2s;
                    }
                    .payment-method.vnpay {
                        background: linear-gradient(135deg, #005bea, #00c6fb);
                    }
                    .payment-method.momo {
                        background: linear-gradient(135deg, #a2006d, #ff4d91);
                    }
                    .payment-method:hover {
                        transform: scale(1.05);
                    }
                </style>
            </head>
            <body>
            <div class="payment-method-container">
                <h2>Choose Your Payment Method</h2>
                <p>You are subscribing for: <?php echo htmlspecialchars($formatted_price); ?></p>
                <div class="payment-methods">
                    <a href="http://localhost:8000/router.php/payment_form?price=<?php echo $price; ?>&method=vnpay" class="payment-method vnpay">VNPay</a>
                    <a href="http://localhost:8000/router.php/payment_form?price=<?php echo $price; ?>&method=momo" class="payment-method momo">MoMo</a>
                </div>
            </div>
            </body>
            </html>
            <?php
            exit;
        } else {
            // Display subscription cards if no price is selected
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Subscription - VNPay</title>
                <style>
                    body {
                        width: 100vw;
                        height: 100vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        background-color: #1a1a1a;
                        font-family: Arial, sans-serif;
                        margin: 0;
                    }
                    .subscription-options {
                        display: flex;
                        gap: 20px;
                        justify-content: center;
                        flex-wrap: wrap;
                    }
                    .subscription-card {
                        background: white;
                        border-radius: 20px;
                        width: 250px;
                        text-align: center;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                        overflow: hidden;
                        position: relative;
                    }
                    .card-header {
                        padding: 20px;
                        color: white;
                        font-size: 1.5em;
                        font-weight: bold;
                        position: relative;
                    }
                    .card-header.basic {
                        background: linear-gradient(135deg, #ff5e62, #f7b733);
                    }
                    .card-header.standard {
                        background: linear-gradient(135deg, #00c6ff, #00ffcc);
                    }
                    .card-header.premium {
                        background: linear-gradient(135deg, #8e2de2, #4a00e0);
                    }
                    .card-header::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><circle cx="5" cy="5" r="2" fill="rgba(255,255,255,0.3)"/></svg>') repeat;
                        opacity: 0.3;
                    }
                    .card-header .price {
                        display: block;
                        font-size: 2em;
                        margin-top: 10px;
                    }
                    .card-body {
                        padding: 20px;
                    }
                    .features {
                        list-style: none;
                        padding: 0;
                        margin: 0 0 20px;
                        text-align: left;
                    }
                    .features li {
                        margin: 10px 0;
                        font-size: 0.9em;
                        color: #333;
                        display: flex;
                        align-items: center;
                    }
                    .features li::before {
                        content: '✔';
                        color: green;
                        margin-right: 10px;
                    }
                    .features li.unavailable::before {
                        content: '✘';
                        color: red;
                    }
                    .subscription-card a {
                        text-decoration: none;
                        display: inline-block;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 25px;
                        font-size: 1em;
                        font-weight: bold;
                        cursor: pointer;
                        transition: transform 0.2s;
                        margin-bottom: 20px;
                    }
                    .subscription-card a.basic {
                        background: linear-gradient(135deg, #ff5e62, #f7b733);
                        color: white;
                    }
                    .subscription-card a.standard {
                        background: linear-gradient(135deg, #00c6ff, #00ffcc);
                        color: white;
                    }
                    .subscription-card a.premium {
                        background: linear-gradient(135deg, #8e2de2, #4a00e0);
                        color: white;
                    }
                    .subscription-card a:hover {
                        transform: scale(1.05);
                    }
                </style>
            </head>
            <body>
            <div class="subscription-options">
                <div class="subscription-card">
                    <div class="card-header basic">
                        BASIC
                        <span class="price">50.000<sup>đ</sup></span>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li>1 Tháng Đăng Ký</li>
                            <li class="unavailable">Hỗ Trợ Ưu Tiên</li>
                            <li>Truy Cập Nội Dung Cơ Bản</li>
                            <li class="unavailable">Tính Năng Nâng Cao</li>
                        </ul>
                        <a href="http://localhost:8000/router.php/payment?price=50000" class="basic">BUY NOW</a>
                    </div>
                </div>
                <div class="subscription-card">
                    <div class="card-header standard">
                        STANDARD
                        <span class="price">120.000<sup>đ</sup></span>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li>3 Tháng Đăng Ký</li>
                            <li>Hỗ Trợ Ưu Tiên</li>
                            <li>Truy Cập Nội Dung Cơ Bản</li>
                            <li class="unavailable">Tính Năng Nâng Cao</li>
                        </ul>
                        <a href="http://localhost:8000/router.php/payment?price=120000" class="standard">BUY NOW</a>
                    </div>
                </div>
                <div class="subscription-card">
                    <div class="card-header premium">
                        PREMIUM
                        <span class="price">200.000<sup>đ</sup></span>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li>6 Tháng Đăng Ký</li>
                            <li>Hỗ Trợ Ưu Tiên</li>
                            <li>Truy Cập Nội Dung Cơ Bản</li>
                            <li>Tính Năng Nâng Cao</li>
                        </ul>
                        <a href="http://localhost:8000/router.php/payment?price=200000" class="premium">BUY NOW</a>
                    </div>
                </div>
            </div>
            </body>
            </html>
            <?php
            exit;
        }
    }
}
// 🔹 Serve Payment Form at /payment_form or /router.php/payment_form
if ($url === 'payment_form' || $url === 'router.php/payment_form') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the payment method from the form (or query parameter if passed earlier)
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
        if (!$payment_method && isset($_GET['method'])) {
            $payment_method = $_GET['method'];
        }

        // Get order details
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
        $order_price = isset($_POST['order_price']) ? $_POST['order_price'] : null;

        // Store user info in session for later use
        $_SESSION['user_info'] = [
            'name' => isset($_POST['name']) ? $_POST['name'] : 'N/A',
            'email' => isset($_POST['email']) ? $_POST['email'] : 'N/A',
            'phone' => isset($_POST['phone']) ? $_POST['phone'] : 'N/A',
        ];

        // Validate order details and payment method
        if (!$order_id || !$order_price) {
            echo "Error: Missing order_id or order_price";
            exit;
        }
        if (!$payment_method || !in_array($payment_method, ['vnpay', 'momo'])) {
            echo "Error: Invalid or missing payment method";
            exit;
        }

        // Process payment based on the selected method
        if ($payment_method === 'vnpay') {
            $payment = new Payment();
            $payment->vnpay_payment($order_id, $order_price);
            exit; // Redirects to VNPay
        } elseif ($payment_method === 'momo') {
            $payment_momo = new payment_momo($order_id, $order_price);
            exit; // Redirects to MoMo (handled in payment_momo constructor)
        }
    }

    // Serve the payment form with pre-filled price for GET requests (existing code unchanged)
    $price = isset($_GET['price']) ? (int)$_GET['price'] : 0;
    $method = isset($_GET['method']) ? $_GET['method'] : '';
    $formatted_price = number_format($price, 0, ',', '.') . 'đ';

    header("Content-Type: text/html");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Info - VNPay</title>
        <style>
            body {
                width: 100vw;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f4f4f4;
                font-family: Arial, sans-serif;
                margin: 0;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 500px;
                text-align: center;
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .payment-form {
                text-align: left;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
                color: #555;
            }
            .form-group input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 1em;
                box-sizing: border-box;
            }
            .form-group input:disabled {
                background-color: #e9ecef;
            }
            button {
                text-decoration: none;
                display: inline-block;
                padding: 10px 20px;
                background-color: cadetblue;
                color: whitesmoke;
                border: none;
                border-radius: 5px;
                font-size: 1em;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            button:hover {
                background-color: #3d7e83;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Thông Tin Thanh Toán</h1>
        <form action="/router.php/payment_form?method=<?php echo htmlspecialchars($method); ?>" method="post" class="payment-form">
            <div class="form-group">
                <label for="name">Họ và Tên (Full Name):</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Số Điện Thoại (Phone Number):</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="price">Giá Gói (Plan Price):</label>
                <input type="text" id="price" value="<?php echo htmlspecialchars($formatted_price); ?>" disabled>
                <input type="hidden" name="order_price" value="<?php echo $price; ?>">
            </div>
            <input type="hidden" name="order_id" value="<?php echo time(); ?>">
            <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($method); ?>">
            <button name="redirect" type="submit">Thanh Toán</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}


// 🔹 Handle VNPay Return URL at /success.php or /router.php/success.php
if ($url === 'success.php' || $url === 'router.php/success.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['vnp_ResponseCode']) && $_GET['vnp_ResponseCode'] == '00') {
            // Pass the GET parameters to success_result.php
            $queryString = http_build_query($_GET);
            header('Location: router.php/success_result.php?' . $queryString);
            exit;
        } else if(isset($_GET['resultCode']) && $_GET['resultCode'] == '00') {
            $queryString = http_build_query($_GET);
            header('Location: /router.php/momo_success_result.php?' . $queryString);
            exit;
        } else {
            echo 'Mẩu khẩu là: còn lâu mới nói';
            exit;
        }
    }
    exit;
}

// 🔹 Serve Success Page at /success_result.php or /router.php/success_result.php
if ($url === 'success_result.php' || $url === 'router.php/success_result.php') {
    // Extract payment info from GET parameters
    $vnp_Amount = isset($_GET['vnp_Amount']) ? number_format($_GET['vnp_Amount'] / 100, 0, ',', '.') . 'đ' : 'N/A';
    $vnp_BankCode = isset($_GET['vnp_BankCode']) ? $_GET['vnp_BankCode'] : 'N/A';
    $vnp_BankTranNo = isset($_GET['vnp_BankTranNo']) ? $_GET['vnp_BankTranNo'] : 'N/A';
    $vnp_CardType = isset($_GET['vnp_CardType']) ? $_GET['vnp_CardType'] : 'N/A';
    $vnp_OrderInfo = isset($_GET['vnp_OrderInfo']) ? $_GET['vnp_OrderInfo'] : 'N/A';
    $vnp_PayDate = isset($_GET['vnp_PayDate']) ? date('d/m/Y H:i:s', strtotime($_GET['vnp_PayDate'])) : 'N/A';
    $vnp_TransactionNo = isset($_GET['vnp_TransactionNo']) ? $_GET['vnp_TransactionNo'] : 'N/A';
    $vnp_TxnRef = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : 'N/A';

    // Get user info from session
    $user_name = isset($_SESSION['user_info']['name']) ? $_SESSION['user_info']['name'] : 'N/A';
    $user_email = isset($_SESSION['user_info']['email']) ? $_SESSION['user_info']['email'] : 'N/A';
    $user_phone = isset($_SESSION['user_info']['phone']) ? $_SESSION['user_info']['phone'] : 'N/A';

    header("Content-Type: text/html");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Success - VNPay</title>
        <style>
            body {
                width: 100vw;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f4f4f4;
                font-family: Arial, sans-serif;
                margin: 0;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 500px;
                text-align: center;
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .payment-info {
                text-align: left;
                margin-bottom: 20px;
            }
            .payment-info label {
                display: block;
                margin: 10px 0 5px;
                font-weight: bold;
                color: #555;
            }
            .payment-info input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background-color: #f9f9f9;
                color: #333;
                font-size: 1em;
                box-sizing: border-box;
            }
            .payment-info input:disabled {
                background-color: #e9ecef;
            }
            a {
                text-decoration: none;
                display: inline-block;
                padding: 10px 20px;
                background-color: cadetblue;
                color: whitesmoke;
                border-radius: 5px;
                font-size: 1em;
                transition: background-color 0.2s;
            }
            a:hover {
                background-color: #3d7e83;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Thanh Toán Thành Công</h1>
        <div class="payment-info">
            <label>Họ và Tên (Full Name):</label>
            <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" disabled>

            <label>Email:</label>
            <input type="text" value="<?php echo htmlspecialchars($user_email); ?>" disabled>

            <label>Số Điện Thoại (Phone Number):</label>
            <input type="text" value="<?php echo htmlspecialchars($user_phone); ?>" disabled>

            <label>Số Tiền (Amount):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_Amount); ?>" disabled>

            <label>Ngân Hàng (Bank Code):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_BankCode); ?>" disabled>

            <label>Mã Giao Dịch Ngân Hàng (Bank Transaction No):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_BankTranNo); ?>" disabled>

            <label>Loại Thẻ (Card Type):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_CardType); ?>" disabled>

            <label>Thông Tin Đơn Hàng (Order Info):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_OrderInfo); ?>" disabled>

            <label>Ngày Thanh Toán (Pay Date):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_PayDate); ?>" disabled>

            <label>Mã Giao Dịch VNPay (Transaction No):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_TransactionNo); ?>" disabled>

            <label>Mã Đơn Hàng (Transaction Ref):</label>
            <input type="text" value="<?php echo htmlspecialchars($vnp_TxnRef); ?>" disabled>
        </div>
        <a href="http://localhost:63342/ERD-Pay/DB/index.html?_ijt=5j9erdl877ukjjus92ckslrt1m">Quay Về</a>
    </div>
    </body>
    </html>
    <?php
    exit;
}
if ($url === 'momo_success_result' || $url === 'router.php/momo_success_result' || $url === 'router.php/momo_success_result.php') {
    // Extract payment info from GET parameters (MoMo response)
    $momo_amount = isset($_GET['amount']) ? number_format($_GET['amount'], 0, ',', '.') . 'đ' : 'N/A';
    $momo_order_id = isset($_GET['orderId']) ? $_GET['orderId'] : 'N/A';
    $momo_request_id = isset($_GET['requestId']) ? $_GET['requestId'] : 'N/A';
    $momo_order_info = isset($_GET['orderInfo']) ? $_GET['orderInfo'] : 'N/A';
    $momo_order_type = isset($_GET['orderType']) ? $_GET['orderType'] : 'N/A';
    $momo_trans_id = isset($_GET['transId']) ? $_GET['transId'] : 'N/A';
    $momo_result_code = isset($_GET['resultCode']) ? $_GET['resultCode'] : 'N/A';
    $momo_message = isset($_GET['message']) ? urldecode($_GET['message']) : 'N/A';
    $momo_pay_type = isset($_GET['payType']) ? $_GET['payType'] : 'N/A';
    $momo_response_time = isset($_GET['responseTime']) ? date('d/m/Y H:i:s', $_GET['responseTime'] / 1000) : 'N/A';

    // Get user info from session
    $user_name = isset($_SESSION['user_info']['name']) ? $_SESSION['user_info']['name'] : 'N/A';
    $user_email = isset($_SESSION['user_info']['email']) ? $_SESSION['user_info']['email'] : 'N/A';
    $user_phone = isset($_SESSION['user_info']['phone']) ? $_SESSION['user_info']['phone'] : 'N/A';

    header("Content-Type: text/html");
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Success - MoMo</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                width: 100vw;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background: linear-gradient(135deg, #f4f4f4, #e0e0e0);
                font-family: 'Segoe UI', Arial, sans-serif;
                padding: 20px;
            }

            .container {
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
                width: 100%;
                max-width: 600px;
                text-align: center;
                border: 1px solid #e0e0e0;
            }

            h1 {
                color: #2c3e50;
                font-size: 2em;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 1px;
                background: linear-gradient(90deg, #ff5e62, #f7b733);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .payment-info {
                margin-bottom: 30px;
                text-align: left;
                background: #f9f9f9;
                padding: 20px;
                border-radius: 10px;
                border: 1px solid #ddd;
            }

            .payment-info .info-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }

            .payment-info .info-row:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }

            .payment-info .label {
                font-weight: bold;
                color: #555;
                font-size: 1em;
                flex: 1;
                min-width: 150px;
            }

            .payment-info .value {
                color: #333;
                font-size: 1em;
                flex: 2;
                word-wrap: break-word;
                word-break: break-all;
                text-align: right;
            }

            .back-button {
                display: inline-block;
                padding: 12px 30px;
                background: linear-gradient(135deg, #00c6ff, #00ffcc);
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-size: 1.1em;
                font-weight: bold;
                transition: transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .back-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
                background: linear-gradient(135deg, #00b4db, #00e0bb);
            }

            @media (max-width: 600px) {
                .container {
                    padding: 20px;
                    margin: 10px;
                }

                h1 {
                    font-size: 1.5em;
                }

                .payment-info .info-row {
                    flex-direction: column;
                    gap: 5px;
                }

                .payment-info .label,
                .payment-info .value {
                    text-align: left;
                    flex: none;
                    min-width: 100%;
                }

                .back-button {
                    padding: 10px 20px;
                    font-size: 1em;
                }
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Thanh Toán Thành Công - MoMo</h1>
        <div class="payment-info">
            <div class="info-row">
                <span class="label">Họ và Tên (Full Name):</span>
                <span class="value"><?php echo htmlspecialchars($user_name); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value"><?php echo htmlspecialchars($user_email); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Số Tiền (Amount):</span>
                <span class="value"><?php echo htmlspecialchars($momo_amount); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Mã Đơn Hàng (Order ID):</span>
                <span class="value"><?php echo htmlspecialchars($momo_order_id); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Mã Yêu Cầu (Request ID):</span>
                <span class="value"><?php echo htmlspecialchars($momo_request_id); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Thông Tin Đơn Hàng (Order Info):</span>
                <span class="value"><?php echo htmlspecialchars($momo_order_info); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Loại Đơn Hàng (Order Type):</span>
                <span class="value"><?php echo htmlspecialchars($momo_order_type); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Mã Giao Dịch MoMo (Transaction ID):</span>
                <span class="value"><?php echo htmlspecialchars($momo_trans_id); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Thông Báo (Message):</span>
                <span class="value"><?php echo htmlspecialchars($momo_message); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Loại Thanh Toán (Pay Type):</span>
                <span class="value"><?php echo htmlspecialchars($momo_pay_type); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Thời Gian Phản Hồi (Response Time):</span>
                <span class="value"><?php echo htmlspecialchars($momo_response_time); ?></span>
            </div>
        </div>
        <a href="http://localhost:63342/ERD-Pay/DB/index.html?_ijt=5j9erdl877ukjjus92ckslrt1m" class="back-button">Quay Về</a>
    </div>
    </body>
    </html>
    <?php
    exit;
}
// 🔹 Fallback for Invalid Routes
http_response_code(404);
die(json_encode(['message' => 'Route not found']));