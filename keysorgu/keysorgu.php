<?php


// Gelen JSON verisini al
$json = file_get_contents("php://input");
require_once 'baglan.php';

// JSON verisini diziye çevir
$data = json_decode($json, true);

// Anahtarın girildiği kontrol edilir
if (isset($data['key'])) {
    $key = $data['key'];

    // Anahtarı veritabanında kontrol et
    $sql = "SELECT Webhook FROM `api_keys` WHERE `Key` = '$key'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo $row["Webhook"];
    } else {
        echo "Anahtar bulunamadı.";
    }
} else {
    echo "Anahtar girilmedi.";
}

// Veritabanı bağlantısını kapat
mysqli_close($conn);
?>
