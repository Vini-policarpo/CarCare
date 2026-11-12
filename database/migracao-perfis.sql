ALTER TABLE usuarios
    ADD COLUMN tipo VARCHAR(20) NOT NULL DEFAULT 'cliente' AFTER senha;

ALTER TABLE clientes
    ADD COLUMN usuario_id INT UNSIGNED NULL AFTER id;

ALTER TABLE funcionarios
    ADD COLUMN usuario_id INT UNSIGNED NULL AFTER id;

CREATE TABLE servicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255),
    valor DECIMAL(10,2) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1
);


ALTER TABLE manutencoes
    ADD COLUMN servico_id INT UNSIGNED NULL AFTER funcionario_id;

ALTER TABLE manutencoes
    MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'Solicitado';

UPDATE manutencoes SET status = 'Agendado' WHERE status = 'Agendada';
UPDATE manutencoes SET status = 'Em andamento' WHERE status = 'Em Andamento';
UPDATE manutencoes SET status = 'Concluido' WHERE status IN ('Concluído', 'Concluída');
UPDATE manutencoes SET status = 'Concluido' WHERE status = 'Concludo';
UPDATE manutencoes SET status = 'Cancelado' WHERE status = 'Cancelada';


UPDATE clientes
INNER JOIN usuarios
    ON clientes.email = usuarios.email
    OR (clientes.usuario_id IS NULL AND clientes.nome = usuarios.nome)
SET clientes.usuario_id = usuarios.id,
    clientes.email = COALESCE(clientes.email, usuarios.email)
WHERE clientes.usuario_id IS NULL
  AND usuarios.tipo = 'cliente';



INSERT INTO clientes (usuario_id, nome, email)
SELECT usuarios.id, usuarios.nome, usuarios.email
FROM usuarios
LEFT JOIN clientes ON clientes.usuario_id = usuarios.id
WHERE usuarios.tipo = 'cliente'
  AND clientes.id IS NULL;

ALTER TABLE clientes
    ADD UNIQUE KEY uq_clientes_usuario (usuario_id),
    ADD CONSTRAINT fk_clientes_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE;

ALTER TABLE funcionarios
    ADD UNIQUE KEY uq_funcionarios_usuario (usuario_id),
    ADD CONSTRAINT fk_funcionarios_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE;

ALTER TABLE manutencoes
    ADD CONSTRAINT fk_manutencoes_servico
        FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE SET NULL;

INSERT INTO servicos (nome, descricao, valor) VALUES
('Troca de óleo', 'Troca do óleo e verificação do filtro', 180.00),
('Revisão preventiva', 'Verificação geral dos itens principais do veículo', 250.00),
('Alinhamento e balanceamento', 'Ajuste de direção e balanceamento das rodas', 160.00);
