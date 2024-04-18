<?php
// JSON formatında isteği al
$data = json_decode(file_get_contents('php://input'), true);

// Veritabanı bağlantısı için ayrı bir dosya çağırılıyor
require_once 'baglan.php';

// POST isteğini işleme
function handleRequest($conn, $data) {
    // POST isteğinden verileri al
    $key = isset($data['key']) ? $data['key'] : '';
    $webhook = isset($data['webhook']) ? $data['webhook'] : '';

    // Eğer key veya webhook boş ise işlem yapma
    if (empty($key) || empty($webhook)) {
        echo json_encode(["error" => "Hata: Anahtar veya Webhook boş olamaz"]);
        return;
    }

    // Webhook'u kontrol et
    $sql_webhook = "SELECT * FROM `api_keys` WHERE `Webhook` = '$webhook'";
    $result_webhook = mysqli_query($conn, $sql_webhook);

    // Webhook kontrolü
    if (mysqli_num_rows($result_webhook) > 0) {
        // Eşleşen anahtarın bilgisini al
        $row = mysqli_fetch_assoc($result_webhook);
        $matched_key = $row['Key'];
        echo json_encode(["status" => "false", "message" => "Bu webhook zaten '$matched_key' anahtarıyla eşleştirilmiş.", "apidev" => "Redrose"], JSON_UNESCAPED_UNICODE);
    } else {
        // Yeni veriyi veritabanına ekle
        $sql_insert = "INSERT INTO `api_keys` (`Key`, `Webhook`) VALUES ('$key', '$webhook')";
        if (mysqli_query($conn, $sql_insert)) {
            echo json_encode(["status" => "success", "message" => "Anahtar ve Webhook başarıyla eklendi", "apidev" => "Redrose"], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["error" => "Hata: " . mysqli_error($conn), "apidev" => "Redrose"], JSON_UNESCAPED_UNICODE);
        }
    }
}

// İsteği işle
handleRequest($conn, $data);

// Veritabanı bağlantısını kapat
mysqli_close($conn);
?>
