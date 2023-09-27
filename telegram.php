<?php


$update = json_decode(file_get_contents("php://input"), TRUE);
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$token = "6329575854:AAEWTNYX_Lz-yKFRb3Q22o-tmUauXCA0f_M";
$dbHost = "localhost";
$dbUser = "id21302815_dicebot";
$dbPassword = "Dice_Bot1";
$dbName = "id21302815_dicebotdb";
$balance = 0.0;
$mysqli = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
if ($message == "/start") {

// User data
$username = $update["message"]["from"]["username"];
$firstName = $update["message"]["from"]["first_name"];
$lastName = $update["message"]["from"]["last_name"];
$userId = $update["message"]["from"]["id"];
$balance = 0.0; // Default balance

// SQL statement with placeholders
$sql = "INSERT INTO users (username, first_name, last_name, balance, chat_id) VALUES (?, ?, ?, ?, ?)";

// Create a prepared statement
$stmt = $mysqli->prepare($sql);

// Bind parameters to the statement
$stmt->bind_param("ssdii", $username, $firstName, $lastName, $balance, $userId);

$stmt->execute();
        $keyboard = [
            "inline_keyboard" => [
                [
                    ["text" => "🎲Играть", "callback_data" => "play"],
                    ["text" => "💰Финансы", "callback_data" => "finances"]
                ],
                [
                    ["text" => "🐞Нашел Баг?", "callback_data" => "bug"],
                    ["text" => "📞Поддержка", "callback_data" => "support"]
                ],
                [
                    ["text" => "⚙️Настройки", "callback_data" => "settings"]
                ]
            ]
        ];

    $replyMarkup = json_encode($keyboard);

    $response = [
        "chat_id" => $chatId,
        "text" => "Привет, $firstName! Выберите одну из опций:",
        "reply_markup" => $replyMarkup
    ];

    sendMessage($token, $response);
} else {
    // Обрабатываем нажатия на inline-кнопки
    $callbackData = $update["callback_query"]["data"];
    $callbackChatId = $update["callback_query"]["message"]["chat"]["id"];

    switch ($callbackData) {
        case "play":
            $keyboard = [
                "inline_keyboard" => [
                    [
                        ["text" => "⚔️Дуэль", "callback_data" => "duel"],
                        ["text" => "📊Статистика", "callback_data" => "stats"]
                    ],
                    [
                        ["text" => "🏠Главное Меню", "callback_data" => "menu"]
                    ]
                ]
            ];
        
            $replyMarkup = json_encode($keyboard);
        
            $response = [
                "chat_id" => $callbackChatId,
                "text" => "Выберите игру:",
                "reply_markup" => $replyMarkup
            ];
        
            sendMessage($token, $response);
            break;

        case "finances":
            $text = "Вы выбрали Финансы 💰";
            $response = [
                "chat_id" => $callbackChatId,
                "text" => $text
            ];
        
            sendMessage($token, $response);
            break;
        case "bug":
            $text = "Вы выбрали Нашел Баг? 🐞";
            break;
        case "support":
            $text = "Вы выбрали Поддержка 📞";
            break;
        case "settings":
            $text = "Вы выбрали Настройки ⚙️";
            break;
        default:
            $text = "Неизвестная команда";
            break;
        
    }


}

function sendDice($token, $chatId) {
    $url = "https://api.telegram.org/bot" . $token . "/sendDice";
    $data = [
        "chat_id" => $chatId,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function sendMessage($token, $response) {
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>
