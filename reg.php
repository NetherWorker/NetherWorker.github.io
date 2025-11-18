<?php
$client_id = '1440045455865282691';
$client_secret = 'xIywv4gktoSKqmnabNnuLdT8k87EDTTj';
$script_url = 'https://NetherWorker.github.io/login.php'; // Ссылка на ваш скрипт
$oauth_url = 'https://discord.com/api/oauth2/authorize';
$token_url = 'https://discord.com/api/oauth2/token';
$api_user_url = 'https://discord.com/api/users/@me';
$revoke_url = 'https://discord.com/api/oauth2/token/revoke';

function api_request($type, $url, $data = array(), $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    if ($token != null) $headers[] = 'Authorization: Bearer ' . $token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    return json_decode(curl_exec($ch), true);
}

if (isset($_GET['code'])) {
    // Обработка авторизации через Discord
    $code = htmlspecialchars($_GET['code']);
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $script_url,
    ];
    $res = api_request('POST', $token_url, $data);
    $token = $res['access_token'];
    $user = api_request('GET', $api_user_url, [], $token);
    var_dump($user); // Печать информации о пользователе

    // Сохраните user_id и другие данные в базу данных, если нужно
    $user_id = $user['id'];

    // Отзыв токена (опционально)
    api_request('POST', $revoke_url, $data);

    // Перенаправление на другую страницу, например, профиль
    header('Location: /profile');
} else {
    // Перенаправление для авторизации
    $data = [
        'client_id' => $client_id,
        'redirect_uri' => $script_url,
        'response_type' => 'code',
        'scope' => 'identify'
    ];
    header('Location: ' . $oauth_url . '?' . http_build_query($data));
}
?>
