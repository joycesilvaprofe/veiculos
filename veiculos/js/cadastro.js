const form = document.getElementById("formVeiculo");
const mensagem = document.getElementById("mensagem");
const btnSalvar = document.getElementById("btnSalvar");

function showMessage(text, isError) {
	mensagem.textContent = text;
	mensagem.className = isError ? "erro" : "sucesso";
}

function normalizeInput(value) {
	return String(value || "").trim();
}

function toUpperNoSpaces(value) {
	return normalizeInput(value).toUpperCase().replace(/\s+/g, "");
}

function validateData(data) {
	const currentYear = new Date().getFullYear() + 1;

	if (!/^[A-Z0-9-]{7,8}$/.test(data.placa)) {
		return "Placa invalida. Use 7 ou 8 caracteres (A-Z, 0-9 e -).";
	}
	if (data.marca.length < 2 || data.modelo.length < 2) {
		return "Marca e modelo devem ter ao menos 2 caracteres.";
	}
	if (!Number.isInteger(data.ano_fabricacao) || data.ano_fabricacao < 1900 || data.ano_fabricacao > currentYear) {
		return "Ano de fabricacao invalido.";
	}
	if (!Number.isInteger(data.ano_modelo) || data.ano_modelo < 1900 || data.ano_modelo > currentYear + 1) {
		return "Ano de modelo invalido.";
	}
	if (data.ano_modelo < data.ano_fabricacao - 1) {
		return "Ano de modelo inconsistente com ano de fabricacao.";
	}
	if (!Number.isInteger(data.quilometragem) || data.quilometragem < 0) {
		return "Quilometragem invalida.";
	}
	if (!/^[A-HJ-NPR-Z0-9]{17}$/.test(data.chassi)) {
		return "Chassi invalido. Deve ter 17 caracteres sem I, O e Q.";
	}
	if (!/^\d{11}$/.test(data.renavam)) {
		return "Renavam invalido. Deve conter 11 digitos.";
	}
	if (!/^\d{4}-\d{2}-\d{2}$/.test(data.data_cadastro)) {
		return "Data de cadastro invalida.";
	}

	return null;
}

async function postWithTimeout(url, payload, timeoutMs) {
	const controller = new AbortController();
	const timer = setTimeout(() => controller.abort(), timeoutMs);

	try {
		const response = await fetch(url, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify(payload),
			signal: controller.signal
		});
		return response;
	} finally {
		clearTimeout(timer);
	}
}

form.addEventListener("submit", async (event) => {
	event.preventDefault();
	showMessage("Enviando...", false);
	btnSalvar.disabled = true;

	const formData = new FormData(form);
	const payload = {
		placa: toUpperNoSpaces(formData.get("placa")),
		marca: normalizeInput(formData.get("marca")),
		modelo: normalizeInput(formData.get("modelo")),
		ano_fabricacao: Number.parseInt(formData.get("ano_fabricacao"), 10),
		ano_modelo: Number.parseInt(formData.get("ano_modelo"), 10),
		cor: normalizeInput(formData.get("cor")),
		combustivel: normalizeInput(formData.get("combustivel")),
		quilometragem: Number.parseInt(formData.get("quilometragem"), 10),
		chassi: toUpperNoSpaces(formData.get("chassi")),
		renavam: normalizeInput(formData.get("renavam")).replace(/\D+/g, ""),
		data_cadastro: normalizeInput(formData.get("data_cadastro")),
		observacoes: normalizeInput(formData.get("observacoes"))
	};

	const error = validateData(payload);
	if (error) {
		showMessage(error, true);
		btnSalvar.disabled = false;
		return;
	}

	try {
		const response = await postWithTimeout("php/inserir.php", payload, 10000);
		const data = await response.json();

		if (!response.ok || !data.success) {
			showMessage(data.message || "Falha ao salvar veiculo.", true);
			return;
		}

		showMessage("Veiculo salvo com sucesso.", false);
		form.reset();
	} catch (err) {
		if (err.name === "AbortError") {
			showMessage("Tempo limite excedido. Tente novamente.", true);
		} else {
			showMessage("Erro de comunicacao com o servidor.", true);
		}
	} finally {
		btnSalvar.disabled = false;
	}
});
