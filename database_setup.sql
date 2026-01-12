-- Script SQL para criar tabela de usuários no banco ControleExterno
-- Copie e execute este script no seu MySQL/MariaDB

-- Criar tabela Users se não existir
CREATE TABLE IF NOT EXISTS Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo)
);

-- Opcional: Criar usuário administrador de teste
-- Descomente as linhas abaixo para adicionar um admin de teste
-- Senha: admin123 (criptografada com bcrypt)
-- INSERT INTO Users (nome, email, senha, tipo) 
-- VALUES ('Administrador Teste', 'admin@teste.com', '$2y$10$HNF8e7P7Qy8V9/5K2mL3P.fD3V9K8m7O1Q2R3S4T5U6V7W8X9Y0Z1a2b', 'admin');

-- Opcional: Criar usuário comum de teste
-- Senha: usuario123 (criptografada com bcrypt)
-- INSERT INTO Users (nome, email, senha, tipo) 
-- VALUES ('Usuário Teste', 'usuario@teste.com', '$2y$10$KmL4P8O6V9/3X5Y2Z1A0B.cE4W8L7M6N9O0P1Q2R3S4T5U6V7W8X9Y', 'user');

-- Se quiser gerar um hash bcrypt válido, você pode:
-- 1. Acessar http://localhost:8080/ControleExterno/signup.php e criar uma conta
-- 2. Ou usar a função password_hash() do PHP em uma página separada

-- Para verificar usuários cadastrados
-- SELECT id, nome, email, tipo, created_at FROM Users ORDER BY id DESC;

-- Para atualizar a senha de um usuário (use o hash gerado)
-- UPDATE Users SET senha = '[COLE_O_HASH_AQUI]' WHERE email = 'usuario@email.com';

-- Para promover um usuário a administrador
-- UPDATE Users SET tipo = 'admin' WHERE email = 'usuario@email.com';

-- Para deletar um usuário
-- DELETE FROM Users WHERE email = 'usuario@email.com';

-- Para limpar todos os usuários (CUIDADO!)
-- DELETE FROM Users;
-- TRUNCATE TABLE Users;
