CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS veiculos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NULL,
    nome_modelo VARCHAR(120) NOT NULL,
    placa VARCHAR(7) NOT NULL UNIQUE,
    ano INT NOT NULL,
    proprietario VARCHAR(120) NOT NULL,
    quilometragem INT NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS manutencoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT UNSIGNED NOT NULL,
    servico VARCHAR(160) NOT NULL,
    data_agendada DATE NOT NULL,
    quilometragem INT,
    status VARCHAR(30) NOT NULL DEFAULT 'Agendada',
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE
);

-- Insere o carro fictício somente se a placa ainda não existir.
INSERT INTO veiculos (usuario_id, nome_modelo, placa, ano, proprietario, quilometragem)
SELECT NULL, 'Volkswagen Gol 1.6 MSI', 'BRA2E19', 2020, 'Lucas Andrade Silva', 45820
WHERE NOT EXISTS (
    SELECT id FROM veiculos WHERE placa = 'BRA2E19'
);
