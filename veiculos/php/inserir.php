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

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	respond(405, ['success' => false, 'message' => 'Metodo nao permitido.']);
}

$rawBody = file_get_contents('php://input');
$json = json_decode($rawBody ?: '', true);
$input = is_array($json) ? $json : $_POST;

$requiredFields = [
	'placa',
	'marca',
	'modelo',
	'ano_fabricacao',
	'ano_modelo',
	'cor',
	'combustivel',
	'quilometragem',
	'chassi',
	'renavam',
	'data_cadastro'
];

foreach ($requiredFields as $field) {
	if (!isset($input[$field]) || trim((string) $input[$field]) === '') {
		respond(422, ['success' => false, 'message' => "Campo obrigatorio ausente: {$field}."]);
	}
}

$placa = strtoupper(preg_replace('/\s+/', '', (string) $input['placa']));
$marca = trim((string) $input['marca']);
$modelo = trim((string) $input['modelo']);
$anoFabricacao = filter_var($input['ano_fabricacao'], FILTER_VALIDATE_INT);
$anoModelo = filter_var($input['ano_modelo'], FILTER_VALIDATE_INT);
$cor = trim((string) $input['cor']);
$combustivel = trim((string) $input['combustivel']);
$quilometragem = filter_var($input['quilometragem'], FILTER_VALIDATE_INT);
$chassi = strtoupper(preg_replace('/\s+/', '', (string) $input['chassi']));
$renavam = preg_replace('/\D+/', '', (string) $input['renavam']);
$dataCadastro = trim((string) $input['data_cadastro']);
$observacoes = trim((string) ($input['observacoes'] ?? ''));

$anoLimite = (int) date('Y') + 1;

if (!preg_match('/^[A-Z0-9-]{7,8}$/', $placa)) {
	respond(422, ['success' => false, 'message' => 'Placa invalida.']);
}
if ($anoFabricacao === false || $anoFabricacao < 1900 || $anoFabricacao > $anoLimite) {
	respond(422, ['success' => false, 'message' => 'Ano de fabricacao invalido.']);
}
if ($anoModelo === false || $anoModelo < 1900 || $anoModelo > ($anoLimite + 1)) {
	respond(422, ['success' => false, 'message' => 'Ano de modelo invalido.']);
}
if ($quilometragem === false || $quilometragem < 0) {
	respond(422, ['success' => false, 'message' => 'Quilometragem invalida.']);
}
if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $chassi)) {
	respond(422, ['success' => false, 'message' => 'Chassi invalido.']);
}
if (!preg_match('/^\d{11}$/', $renavam)) {
	respond(422, ['success' => false, 'message' => 'Renavam invalido.']);
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataCadastro)) {
	respond(422, ['success' => false, 'message' => 'Data de cadastro invalida.']);
}

try {
	$pdo = getConnection();

	$stmt = $pdo->prepare(
		'INSERT INTO veiculos (
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
		) VALUES (
			:placa,
			:marca,
			:modelo,
			:ano_fabricacao,
			:ano_modelo,
			:cor,
			:combustivel,
			:quilometragem,
			:chassi,
			:renavam,
			:data_cadastro,
			:observacoes
		)'
	);

	$stmt->execute([
		':placa' => $placa,
		':marca' => $marca,
		':modelo' => $modelo,
		':ano_fabricacao' => $anoFabricacao,
		':ano_modelo' => $anoModelo,
		':cor' => $cor,
		':combustivel' => $combustivel,
		':quilometragem' => $quilometragem,
		':chassi' => $chassi,
		':renavam' => $renavam,
		':data_cadastro' => $dataCadastro,
		':observacoes' => $observacoes
	]);

	respond(201, [
		'success' => true,
		'message' => 'Veiculo cadastrado com sucesso.',
		'id' => (int) $pdo->lastInsertId()
	]);
} catch (PDOException $e) {
	if ((int) $e->getCode() === 23000 || stripos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
		respond(409, ['success' => false, 'message' => 'Placa, chassi ou renavam ja cadastrado.']);
	}

	respond(500, ['success' => false, 'message' => 'Erro interno ao cadastrar veiculo.']);
} catch (Throwable $e) {
	respond(500, ['success' => false, 'message' => 'Erro inesperado no servidor.']);
}
