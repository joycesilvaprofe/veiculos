<?php
declare(strict_types=1);

function loadEnv(string $filePath): void
{
	if (!file_exists($filePath)) {
		return;
	}

	$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		if (str_starts_with(trim($line), '#')) {
			continue;
		}

		if (!str_contains($line, '=')) {
			continue;
		}

		[$key, $value] = explode('=', $line, 2);
		$key = trim($key);
		$value = trim($value);

		if (!isset($_ENV[$key])) {
			$_ENV[$key] = $value;
		}
	}
}

loadEnv(__DIR__ . '/../../.env');

function getConnection(): PDO
{
	static $pdo = null;

	if ($pdo instanceof PDO) {
		return $pdo;
	}

	$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
	$dbPort = (int) ($_ENV['DB_PORT'] ?? 3306);
	$dbUser = $_ENV['DB_USER'] ?? 'root';
	$dbPass = $_ENV['DB_PASSWORD'] ?? '';
	$dbName = $_ENV['DB_NAME'] ?? 'veiculos';

	$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

	try {
		$pdo = new PDO($dsn, $dbUser, $dbPass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		$pdo->exec(
			'CREATE TABLE IF NOT EXISTS veiculos (
				id INT AUTO_INCREMENT PRIMARY KEY,
				placa VARCHAR(8) NOT NULL UNIQUE,
				marca VARCHAR(60) NOT NULL,
				modelo VARCHAR(60) NOT NULL,
				ano_fabricacao INT NOT NULL,
				ano_modelo INT NOT NULL,
				cor VARCHAR(30) NOT NULL,
				combustivel VARCHAR(20) NOT NULL,
				quilometragem INT NOT NULL,
				chassi VARCHAR(17) NOT NULL UNIQUE,
				renavam VARCHAR(11) NOT NULL UNIQUE,
				data_cadastro DATE NOT NULL,
				observacoes TEXT DEFAULT NULL,
				criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				INDEX idx_placa (placa),
				INDEX idx_chassi (chassi)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
		);

		return $pdo;
	} catch (PDOException $e) {
		die(json_encode([
			'success' => false,
			'message' => 'Falha ao conectar ao banco de dados: ' . $e->getMessage()
		]));
	}
}
