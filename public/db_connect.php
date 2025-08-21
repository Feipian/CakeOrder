<?php
function getEnvValue(string $key, $default = null) {
	$val = getenv($key);
	if ($val !== false && $val !== '') return $val;
	if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
	if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
	return $default;
}

$isRunningInDocker = file_exists('/.dockerenv');

$host = getEnvValue('DB_HOST', $isRunningInDocker ? 'db' : 'localhost');
$db   = getEnvValue('DB_NAME', 'cake_shop'); // 你的資料庫名稱
$user = getEnvValue('DB_USER', 'root');      // 預設 XAMPP 帳號
$pass = getEnvValue('DB_PASSWORD', '');      // 預設 XAMPP 密碼為空
$port = (int) getEnvValue('DB_PORT', '3306');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
	PDO::ATTR_TIMEOUT            => 5,
];

$maxAttempts = (int) getEnvValue('DB_CONNECT_RETRIES', '15');
$attempt = 0;
while (true) {
	try {
		$pdo = new PDO($dsn, $user, $pass, $options);
		break;
	} catch (PDOException $e) {
		$attempt++;
		if ($attempt >= $maxAttempts) {
			die('Database connection failed: ' . $e->getMessage());
		}
		error_log("Waiting for database... attempt $attempt/$maxAttempts");
		sleep(1);
	}
}
?>