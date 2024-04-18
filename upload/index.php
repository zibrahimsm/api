<?php

// Upload endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // JSON data handling
        $json = isset($_POST['json']) ? $_POST['json'] : file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Extracting the key directly from the JSON data
        $key = isset($data['key']) ? $data['key'] : null;

        if (!$key) {
            http_response_code(400);
            echo json_encode(array("message" => "No key provided in the form data"));
            exit;
        }

        // Payload for the request to the external API
        $payload = array(
            'key' => $key // Assuming key is defined elsewhere in your code
            // You can add other payload data if needed
        );

        // API isteği için payload oluştur
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($payload)
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents('http://voxify258.0hi.me/keysorgu/keysorgu.php', false, $context);

        // API isteği sırasında hata oluşursa
        if ($response === false) {
            http_response_code(500);
            echo json_encode(array("message" => "Error getting webhook URL"));
            exit;
        }

        // Extracting the webhook URL from the API response and trimming
        $webhookURL = trim($response);
        
        // Checking if the webhook URL is available
        if (!$webhookURL) {
            http_response_code(500);
            echo json_encode(array("message" => "Error getting webhook URL"));
            exit;
        }

        // Checking if a file was uploaded
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(array("message" => "No file uploaded"));
            exit;
        }

        // Retrieving the uploaded file data
        $zipData = file_get_contents($_FILES['file']['tmp_name']);
        $zipName = $_FILES['file']['name'];

        // Creating the Vct folder if it doesn't exist
        $zipFolder = __DIR__ . '/Vct/';
        if (!file_exists($zipFolder)) {
            mkdir($zipFolder, 0777, true);
        }

        // Generating the full path for the zip file
        $randomString = bin2hex(random_bytes(2));
        $zipNameWithRandom = $randomString . '_' . $zipName;
        $zipPath = $zipFolder . $zipNameWithRandom . '.zip'; // Assuming the file is a zip file

        // Writing the zip file
        file_put_contents($zipPath, $zipData);

        // Constructing the URL
        $url = "http://voxify258.0hi.me/upload/Vct/$zipNameWithRandom.zip";

        // Constructing the embed data
        $embed = array(
            'title' => 'Download Link',
            'description' => "Download the file: [$zipNameWithRandom.zip]($url)",
            'color' => hexdec('FF0000') // You can change the color if desired
        );

        // Sending the embed to the webhook URL
        $postData = array(
            'embeds' => array($embed)
        );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($postData)
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($webhookURL, false, $context);

        // API isteği sırasında hata oluşursa
        if ($response === false) {
            http_response_code(500);
            echo json_encode(array("message" => "Error sending file to webhook"));
            exit;
        }

        echo json_encode(array("message" => "File uploaded and download link sent successfully"));
    } catch (Exception $e) {
        // Handling exceptions
        error_log('Error: ' . $e->getMessage());

        // Sending an HTTP response with error message
        http_response_code(500);
        echo json_encode(array("message" => "Internal server error"));
    }
}
?>
