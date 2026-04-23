<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/conexao.php';

function respond(int $statusCode, array $payload): void
{
	http_response_code($statusCode);
	echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
	respond(405, ['success' => false, 'message' => 'Metodo nao permitido.']);
}

try {
	$pdo = getConnection();
	$stmt = $pdo->query(
		'SELECT
			id,
			placa,
			marca,
			modelo,
			ano_fabricacao,
			ano_modelo,
			cor,
			combustivel,
			quilometragem,
			chassi,
			renavam,
			data_cadastro,
			observacoes
		FROM veiculos
		ORDER BY id DESC
		LIMIT 500'
	);

	$data = $stmt->fetchAll();

	respond(200, [
		'success' => true,
		'message' => 'Consulta realizada com sucesso.',
		'data' => $data
	]);
} catch (Throwable $e) {
	respond(500, ['success' => false, 'message' => 'Erro ao consultar veiculos.']);
}
