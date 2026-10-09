<?php

header("Content-Type: application/json");
error_reporting(0);

 $botToken = "8980625599:AAFlTEUunpK7tRzgVok3kS9yCeoMSQtpM-g";
$chatId = "7374169651";

$nama = trim($_POST["nama"] ?? "");
$pesan = trim($_POST["pesan"] ?? "");

if ($nama === "" || $pesan === "") {
    echo json_encode([
        "success" => false,
        "message" => "Nama dan pesan wajib diisi."
    ]);
    exit;
}

date_default_timezone_set("Asia/Jakarta");
$waktu = date("d-m-Y H:i:s");

$caption = "📩 PESAN BARU\n\n";
$caption .= "👤 Nama: " . $nama . "\n";
$caption .= "💬 Pesan: " . $pesan . "\n";
$caption .= "🕐 Waktu: " . $waktu . " WIB";

function kirimTelegram($url, $data)
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    // Untuk pengujian lokal AWebServer
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}

$baseUrl = "https://api.telegram.org/bot" . $botToken;

$berhasil = false;

/*
================================
KALAU ADA FOTO / VIDEO
================================
*/

if (
    isset($_FILES["media"]) &&
    $_FILES["media"]["error"] === UPLOAD_ERR_OK
) {

    $file = $_FILES["media"];

    $mime = mime_content_type($file["tmp_name"]);

    if (strpos($mime, "image/") === 0) {

        $url = $baseUrl . "/sendPhoto";

        $data = [
            "chat_id" => $chatId,
            "photo" => new CURLFile(
                $file["tmp_name"],
                $mime,
                $file["name"]
            ),
            "caption" => $caption
        ];

        $response = kirimTelegram($url, $data);

    } elseif (strpos($mime, "video/") === 0) {

        $url = $baseUrl . "/sendVideo";

        $data = [
            "chat_id" => $chatId,
            "video" => new CURLFile(
                $file["tmp_name"],
                $mime,
                $file["name"]
            ),
            "caption" => $caption
        ];

        $response = kirimTelegram($url, $data);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "File harus berupa foto atau video."
        ]);

        exit;
    }

} else {

    /*
    ================================
    KALAU TIDAK ADA FILE
    ================================
    */

    $url = $baseUrl . "/sendMessage";

    $data = [
        "chat_id" => $chatId,
        "text" => $caption
    ];

    $response = kirimTelegram($url, $data);
}


/*
================================
CEK HASIL TELEGRAM
================================
*/

$result = json_decode($response, true);

if (
    isset($result["ok"]) &&
    $result["ok"] === true
) {

    echo json_encode([
        "success" => true,
        "message" => "Pesan berhasil dikirim ke admin."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Telegram gagal menerima pesan."
    ]);
}

exit;
?>