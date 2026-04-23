const corpoTabela = document.getElementById("corpoTabela");
const statusEl = document.getElementById("status");
const btnRecarregar = document.getElementById("btnRecarregar");

function setStatus(text, isError) {
	statusEl.textContent = text;
	statusEl.className = isError ? "erro" : "sucesso";
}

function escapeHtml(value) {
	return String(value ?? "")
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/\"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

function renderRows(items) {
	if (!items.length) {
		corpoTabela.innerHTML = "<tr><td colspan=\"13\">Nenhum veiculo encontrado.</td></tr>";
		return;
	}

	corpoTabela.innerHTML = items
		.map((v) => {
			return `
				<tr>
					<td>${escapeHtml(v.id)}</td>
					<td>${escapeHtml(v.placa)}</td>
					<td>${escapeHtml(v.marca)}</td>
					<td>${escapeHtml(v.modelo)}</td>
					<td>${escapeHtml(v.ano_fabricacao)}</td>
					<td>${escapeHtml(v.ano_modelo)}</td>
					<td>${escapeHtml(v.cor)}</td>
					<td>${escapeHtml(v.combustivel)}</td>
					<td>${escapeHtml(v.quilometragem)}</td>
					<td>${escapeHtml(v.chassi)}</td>
					<td>${escapeHtml(v.renavam)}</td>
					<td>${escapeHtml(v.data_cadastro)}</td>
					<td>${escapeHtml(v.observacoes)}</td>
				</tr>
			`;
		})
		.join("");
}

async function fetchWithTimeout(url, timeoutMs) {
	const controller = new AbortController();
	const timer = setTimeout(() => controller.abort(), timeoutMs);

	try {
		return await fetch(url, { signal: controller.signal });
	} finally {
		clearTimeout(timer);
	}
}

async function carregarVeiculos() {
	setStatus("Carregando...", false);
	btnRecarregar.disabled = true;

	try {
		const response = await fetchWithTimeout("php/listar.php", 10000);
		const data = await response.json();

		if (!response.ok || !data.success) {
			setStatus(data.message || "Falha ao carregar dados.", true);
			corpoTabela.innerHTML = "";
			return;
		}

		renderRows(Array.isArray(data.data) ? data.data : []);
		setStatus("Lista atualizada.", false);
	} catch (err) {
		if (err.name === "AbortError") {
			setStatus("Tempo limite excedido na consulta.", true);
		} else {
			setStatus("Erro de comunicacao com o servidor.", true);
		}
		corpoTabela.innerHTML = "";
	} finally {
		btnRecarregar.disabled = false;
	}
}

btnRecarregar.addEventListener("click", carregarVeiculos);
carregarVeiculos();
