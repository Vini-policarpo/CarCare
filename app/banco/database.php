<?php

// Dados recebidos do arquivo .env pelo Docker.
$host = getenv('DB_HOST') ?: 'db';
$porta = getenv('DB_PORT') ?: '3306';
$nomeBanco = getenv('DB_DATABASE') ?: 'integrador';
$usuarioBanco = getenv('DB_USERNAME') ?: 'integrador';
$senhaBanco = getenv('DB_PASSWORD');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$nomeBanco;charset=utf8mb4",
        $usuarioBanco,
        $senhaBanco
    );

    // Faz o PDO mostrar os erros do banco durante o desenvolvimento.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    exit('Não foi possível conectar ao banco de dados.');
}
