<?php
declare(strict_types=1);

function getConnection(): PDO
{
	static $pdo = null;

	if ($pdo instanceof PDO) {
		return $pdo;
	}

	$databasePath = __DIR__ . '/veiculos.sqlite';
	$dsn = 'sqlite:' . $databasePath;

	$pdo = new PDO($dsn);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$pdo->exec('PRAGMA foreign_keys = ON');

	$pdo->exec(
		'CREATE TABLE IF NOT EXISTS veiculos (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			placa TEXT NOT NULL UNIQUE,
			marca TEXT NOT NULL,
			modelo TEXT NOT NULL,
			ano_fabricacao INTEGER NOT NULL,
			ano_modelo INTEGER NOT NULL,
			cor TEXT NOT NULL,
			combustivel TEXT NOT NULL,
			quilometragem INTEGER NOT NULL,
			chassi TEXT NOT NULL UNIQUE,
			renavam TEXT NOT NULL UNIQUE,
			data_cadastro TEXT NOT NULL,
			observacoes TEXT DEFAULT "",
			criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
		)'
	);

	return $pdo;
}
