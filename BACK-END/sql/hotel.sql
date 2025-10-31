CREATE DATABASE IF NOT EXISTS Hotel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Hotel;

CREATE TABLE IF NOT EXISTS quartos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(50) NOT NULL,
  tipo VARCHAR(100) NOT NULL,
  preco_noite DECIMAL(10,2) NULL,
  descricao TEXT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quarto_id INT NOT NULL,
  data_entrada DATE NOT NULL,
  data_saida DATE NOT NULL,
  nome_completo VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL,
  cpf VARCHAR(20) NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reservas_quarto FOREIGN KEY (quarto_id) REFERENCES quartos(id)
) ENGINE=InnoDB;

INSERT INTO quartos (numero, tipo, status) VALUES
  ('101', 'Standard', 1),
  ('102', 'Luxo', 1),
  ('201', 'Suíte', 1)
ON DUPLICATE KEY UPDATE numero=VALUES(numero), tipo=VALUES(tipo), status=VALUES(status);

-- Usuários (admin)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuário admin padrão (email: admin@hotel.com / senha: admin123) - altere em produção
INSERT INTO usuarios (nome, email, senha_hash, ativo)
VALUES ('Administrador', 'admin@hotel.com', '$2y$10$5C7D9V7h2rYq5sG0U4i5VeM1yJx8m3m7Cw2V7bLQe9Qn3q1uY0m1W', 1)
ON DUPLICATE KEY UPDATE nome=VALUES(nome);
