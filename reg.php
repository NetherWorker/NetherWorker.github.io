<?php
$client_id = 1440045455865282691;
$client_secret = 'dbOzdvT-0d11aYDU6ABRVU0IyEiys7mO';
$script_url = 'https://NetherWorker.github.io/reg.php'; // ссылка до вашего скрипта
// ссылки oauth и api discord
$oauth_url = 'https://discord.com/oauth2/authorize?client_id=1440045455865282691&response_type=code&redirect_uri=https%3A%2F%2FNetherWorker.github.io%2Flogin.php&scope=identify';
$token_url = 'https://discord.com/api/oauth2/token'; //сиськи
$revoke_url = 'https://discord.com/api/oauth2/token/revoke';
function api_request($type, $url, $data = array(), $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    if ($token != null) $headers[] = 'Authorization: Bearer '.$token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    return json_decode(curl_exec($ch), true);
}
if (isset($_GET['code'])) {
	// это условие сработает при редиректе от Discord после авторизации
	$code = htmlspecialchars($_GET['code']); // получаем код авторизации
	// готовим api запрос
	$data = [
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'authorization_code',
		'code' => $code,
		'redirect_uri' => $script_url,
	];
	$res = api_request('POST', $token_url, $data);
	$token = $res['access_token']; // получаем токен
	$user = api_request('GET', $api_user_url, [], $token); // запрашиваем данные о пользователе
	var_dump($user); // массив данных
	
	// например так мы получим user_id пользователя:
	$user_id = $user['id'];
	
	// далее вы можете записать этот user_id в БД, а также выполнять любые другие действия с данными
	
	// если вам нужно было только получить и сохранить user_id для бота, вы при желании можете отозвать токен авторизации:
	api_request('POST', $revoke_url, $data);
	
	// после всех действий вы можете перенаправить пользователя куда нужно, например в профиль:
	header('Location: /profile');
} else {
	// запускаем авторизацию через Discord
	$data = [
		'client_id' => $client_id,
		'redirect_uri' => $script_url,
		'response_type' => 'code',
		'scope' => 'identify'
	];
	header('Location: '.$oauth_url.'?'.http_build_query($data));
}