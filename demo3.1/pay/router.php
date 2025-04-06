

<?php


// Your existing logic for /payment goes here

session_start();
require_once 'PaymentVnpayClass.php'; // Required for payment handling
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
        // Serve the HTML with subscription cards
        header("Content-Type: text/html");
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
                    background-color: #1a1a1a; /* Dark background like the image */
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
                    background: linear-gradient(135deg, #ff5e62, #f7b733); /* Gradient for Basic */
                }
                .card-header.standard {
                    background: linear-gradient(135deg, #00c6ff, #00ffcc); /* Gradient for Standard */
                }
                .card-header.premium {
                    background: linear-gradient(135deg, #8e2de2, #4a00e0); /* Gradient for Premium */
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
                    <a href="http://localhost:8000/router.php/payment_form?price=50000" class="basic">BUY NOW</a>
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
<!--                    router.php/payment_form?price=120000-->
                    <a href="http://localhost:8000/router.php/payment_form?price=120000" class="standard">BUY NOW</a>
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
                    <a href="http://localhost:8000/router.php/payment_form?price=200000" class="premium">BUY NOW</a>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// 🔹 Serve Payment Form at /payment_form or /router.php/payment_form
if ($url === 'payment_form' || $url === 'router.php/payment_form') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payment = new Payment();
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
        $order_price = isset($_POST['order_price']) ? $_POST['order_price'] : null;

        // Store user info in session for later use
        $_SESSION['user_info'] = [
            'name' => isset($_POST['name']) ? $_POST['name'] : 'N/A',
            'email' => isset($_POST['email']) ? $_POST['email'] : 'N/A',
            'phone' => isset($_POST['phone']) ? $_POST['phone'] : 'N/A',
        ];

        if ($order_id && $order_price) {
            $payment->vnpay_payment($order_id, $order_price);
            exit; // Redirects to VNPay
        } else {
            echo "Error: Missing order_id or order_price";
            exit;
        }
    }

    // Serve the payment form with pre-filled price for GET requests
    $price = isset($_GET['price']) ? (int)$_GET['price'] : 0;
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
        <form action="/router.php/payment_form" method="post" class="payment-form">
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

// 🔹 Fallback for Invalid Routes
http_response_code(404);
die(json_encode(['message' => 'Route not found']));