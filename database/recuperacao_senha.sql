-- Execute no phpMyAdmin > maos_de_fada > SQL (uma única vez).
CREATE TABLE IF NOT EXISTS recuperacao_senha (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 usuario_id INT UNSIGNED NOT NULL,
 token_hash CHAR(64) NOT NULL UNIQUE,
 expira_em DATETIME NOT NULL,
 usado_em DATETIME DEFAULT NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_recuperacao_usuario (usuario_id),
 INDEX idx_recuperacao_expira (expira_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
