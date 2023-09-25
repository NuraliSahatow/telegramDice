<?php


$update = json_decode(file_get_contents("php://input"), TRUE);
$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$token = "6329575854:AAEWTNYX_Lz-yKFRb3Q22o-tmUauXCA0f_M";

// Массив для хранения активных комнат для игры

if ($message == "/start") {
    $username = $update["message"]["from"]["username"];
    $firstName = $update["message"]["from"]["first_name"];
    $lastName = $update["message"]["from"]["last_name"];
    $userId = $update["message"]["from"]["id"]; // Добавлено получение ID пользователя
    $mysqli = new mysqli("sql310.byethost24.com", "b24_35100357", "dicebot", "b24_35100357_dicebot");

    // Проверка подключения к базе данных
    if ($mysqli->connect_error) {
        die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
    }

    // SQL-запрос для вставки данных
    $sql = "INSERT INTO users (username, first_name, last_name, balance, chat_id ) VALUES ('$username', '$firstName', '$lastName', 0.0,$userId )";    // Выполняем запрос
    $mysqli->query($sql);

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