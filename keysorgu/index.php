<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Key Sorgu Formu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: calc(100% - 22px);
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 8px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #result {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Anahtar Sorgulama Formu</h2>
        <form id="keyForm">
            <label for="key">Anahtar:</label>
            <input type="text" id="key" name="key" required><br><br>
            <button type="button" onclick="submitForm()">Gönder</button>
        </form>
        <div id="result"></div>
    </div>

    <script>
        function submitForm() {
            var key = document.getElementById("key").value;
            var data = { "key": key };

            fetch('keysorgu.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("result").innerHTML = data;
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
