<?php
// Veritabanı bağlantısı için gerekli bilgiler
$servername = "sql309.0hi.me";
$username = "0hi_36369826";
$password = "817c3b8ea2f5";
$database = "0hi_36369826_vcx";

// Veritabanına bağlanma
$conn = mysqli_connect($servername, $username, $password, $database);

// Bağlantıyı kontrol et
if (!$conn) {
    die(json_encode(["error" => "Veritabanına bağlanırken hata oluştu: " . mysqli_connect_error()]));
}
?>
