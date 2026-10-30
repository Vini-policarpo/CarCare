-- CREATE TABLE IF NOT EXISTS exemplos (
--     id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     titulo VARCHAR(120) NOT NULL,
--     criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
-- );

CREATE DATABASE carcare;

USE carcare;

-- 1. Tabela de Clientes/Proprietários
CREATE TABLE clientes (
	id          INT  AUTO_INCREMENT  PRIMARY  KEY,
	nome		VARCHAR (100)  NOT NULL,
	telefone	VARCHAR(20),
	email		VARCHAR(100)
);

-- 2. Tabela de Funcionários
CREATE TABLE funcionarios (
	id          INT AUTO_INCREMENT  PRIMARY  KEY,
	nome		VARCHAR(100)  NOT  NULL,
	cargo		VARCHAR(50)
);

-- 3. Tabela de Veículos (Registro de veículos e quilometragem - paiva vai ajudar)
CREATE TABLE veiculos (
    id              INT  AUTO_INCREMENT  PRIMARY  KEY,
    cliente_id		INT NOT NULL,
    placa			VARCHAR(7) NOT NULL UNIQUE,
    marca			VARCHAR(50),
    modelo		    VARCHAR(50) NOT NULL,
    ano			    INT,
    quilometragem	INT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- 4. Tabela de Peças
CREATE TABLE pecas (
	id          INT  AUTO_INCREMENT  PRIMARY  KEY,
	nome		VARCHAR(100) NOT NULL,
	valor		DECIMAL(10,2) NOT NULL,
    estoque	    INT
);

-- 5. Tabela de Serviços/Manutenções (Agendamentos e Histórico)
CREATE TABLE manutencoes (
	id                      INT  AUTO_INCREMENT  PRIMARY  KEY,
	veiculo_id			    INT NOT NULL,
	funcionario_id		    INT,
	data_agendamento	    DATETIME NOT NULL,
	data_conclusao		    DATETIME,
	status 		            ENUM('Agendado', 'Em Andamento', 'Concluído', 'Cancelado') DEFAULT 'Agendado',
	descricao			    VARCHAR(390),
	proxima_manutencao_data	DATE,
	proxima_manutencao_km	INT,
	FOREIGN KEY (veiculo_id) REFERENCES veiculos(id),
	FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

-- 6. Tabela de Peças Utilizadas na Manutenção
CREATE TABLE manutencao_pecas (
	id              INT  AUTO_INCREMENT  PRIMARY  KEY,
	manutencao_id	INT NOT NULL,
	peca_id		    INT NOT NULL,
	quantidade		INT,
	FOREIGN KEY (manutencao_id) REFERENCES manutencoes(id),
	FOREIGN KEY (peca_id) REFERENCES pecas(id)
);