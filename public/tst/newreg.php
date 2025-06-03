<?php
include 'routes.php'; 
function curlPost($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return $response;
}

// توليد رقم هاتف عشوائي (مثلاً يبدأ بـ 05 ثم 8 أرقام عشوائية)
function generateRandomPhoneNumber() {
    $phone = "05";
    for ($i = 0; $i < 8; $i++) {
        $phone .= rand(0,9);
    }
    return $phone;
}

// بيانات التسجيل
$phone_number = generateRandomPhoneNumber();

$registerData = [
    "phone_number" => $phone_number,
    // ممكن تضيف بيانات أخرى إذا مطلوبه مثل الاسم أو البريد حسب الـ API
];

// رابط API الأساسي
$baseUrl = 'https://sportz.azsystems.tech/api/';

// 1. تسجيل مستخدم جديد
echo "Registering user with phone: $phone_number\n";
$registerResponse = curlPost($baseUrl . 'register', $registerData);
if (!$registerResponse) {
    die("Failed to get response from register API\n");
}

$registerResult = json_decode($registerResponse, true);
if (isset($registerResult['error'])) {
    echo "Register error:\n";
    print_r($registerResult['error']);
    exit;
}

echo "Register response:\n";
print_r($registerResult);

// 2. تحقق OTP
// هنا نفترض أن الـ OTP المرسل في الرد أو ثابت مثلاً "1234"
$otp_code = $registerResult['otp_code'] ?? '1234'; // استخدم الكود المرسل أو 1234 افتراضياً

$verifyData = [
    "phone_number" => $phone_number,
    "otp_code" => $otp_code,
];

echo "Verifying OTP code: $otp_code\n";

$verifyResponse = curlPost($baseUrl . 'verify-otp', $verifyData);
if (!$verifyResponse) {
    die("Failed to get response from verify-otp API\n");
}

$verifyResult = json_decode($verifyResponse, true);

if (isset($verifyResult['error'])) {
    echo "Verify OTP error:\n";
    print_r($verifyResult['error']);
    exit;
}

echo "Verify OTP response:\n";
print_r($verifyResult);

if (isset($verifyResult['token'])) {
    $_SESSION['user_token'] = $verifyResult['token'];
    $token = $_SESSION['user_token'];
    echo '<h3>تم حفظ التوكن في الجلسة:</h3>';
    echo '<textarea rows="6" cols="80" readonly>' . htmlspecialchars($token) . '</textarea>';
} else {
    echo "No token received.";
}
