-- Contas
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- guardar as infos
CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NULL UNIQUE,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(190),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- funcionario mexer no status do cliente
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NULL UNIQUE,
    nome VARCHAR(120) NOT NULL,
    cargo VARCHAR(50),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS veiculos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT UNSIGNED NOT NULL,
    placa VARCHAR(7) NOT NULL UNIQUE,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(120) NOT NULL,
    ano INT NOT NULL,
    quilometragem INT NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE IF NOT EXISTS servicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255),
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS pecas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    estoque INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS manutencoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    veiculo_id INT UNSIGNED NOT NULL,
    funcionario_id INT UNSIGNED NULL,
    servico_id INT UNSIGNED NULL,
    data_agendamento DATETIME NOT NULL,
    data_conclusao DATETIME NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Solicitado',
    descricao VARCHAR(390) NOT NULL,
    quilometragem INT NOT NULL DEFAULT 0,
    observacoes TEXT,
    proxima_manutencao_data DATE NULL,
    proxima_manutencao_km INT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (veiculo_id) REFERENCES veiculos(id) ON DELETE CASCADE,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE SET NULL,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS manutencao_pecas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    manutencao_id INT UNSIGNED NOT NULL,
    peca_id INT UNSIGNED NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    FOREIGN KEY (manutencao_id) REFERENCES manutencoes(id) ON DELETE CASCADE,
    FOREIGN KEY (peca_id) REFERENCES pecas(id)
);

-- aqui a gente criou um carro ficticio inicial pra testar auto completar
INSERT INTO clientes (nome, telefone, email)
SELECT 'Lucas Andrade Silva', '(11) 99999-0000', 'lucas.exemplo@carcare.local'
WHERE NOT EXISTS (
    SELECT id FROM clientes WHERE email = 'lucas.exemplo@carcare.local'
);

INSERT INTO veiculos (cliente_id, placa, marca, modelo, ano, quilometragem)
SELECT clientes.id, 'BRA2E19', 'Volkswagen', 'Gol 1.6 MSI', 2020, 45820
FROM clientes
WHERE clientes.email = 'lucas.exemplo@carcare.local'
  AND NOT EXISTS (SELECT id FROM veiculos WHERE placa = 'BRA2E19')
LIMIT 1;


INSERT INTO servicos (nome, descricao, valor)
SELECT 'Troca de óleo', 'Troca do óleo e verificação do filtro', 180.00
WHERE NOT EXISTS (SELECT id FROM servicos WHERE nome = 'Troca de óleo');

INSERT INTO servicos (nome, descricao, valor)
SELECT 'Revisão preventiva', 'Verificação geral dos itens principais do veículo', 250.00
WHERE NOT EXISTS (SELECT id FROM servicos WHERE nome = 'Revisão preventiva');

INSERT INTO servicos (nome, descricao, valor)
SELECT 'Alinhamento e balanceamento', 'Ajuste de direção e balanceamento das rodas', 160.00
WHERE NOT EXISTS (SELECT id FROM servicos WHERE nome = 'Alinhamento e balanceamento');
