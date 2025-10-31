<?php
class User {
  private PDO $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }

  public function findByEmail(string $email): ?array {
    $stmt = $this->pdo->prepare('SELECT id, nome, email, senha_hash, ativo FROM usuarios WHERE LOWER(email) = LOWER(?) LIMIT 1');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function getById(int $id): ?array {
    $stmt = $this->pdo->prepare('SELECT id, nome, email, ativo FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function listAll(): array {
    $stmt = $this->pdo->query('SELECT id, nome, email, ativo FROM usuarios ORDER BY id DESC');
    return $stmt->fetchAll();
  }

  public function create(array $data): int {
    $sql = 'INSERT INTO usuarios (nome, email, senha_hash, ativo) VALUES (?,?,?,?)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $data['nome'],
      $data['email'],
      password_hash($data['senha'], PASSWORD_DEFAULT),
      isset($data['ativo']) ? (int)$data['ativo'] : 1,
    ]);
    return (int)$this->pdo->lastInsertId();
  }

  public function update(int $id, array $data): bool {
    if (!empty($data['senha'])) {
      $sql = 'UPDATE usuarios SET nome=?, email=?, senha_hash=?, ativo=? WHERE id=?';
      $params = [ $data['nome'], $data['email'], password_hash($data['senha'], PASSWORD_DEFAULT), (int)$data['ativo'], $id ];
    } else {
      $sql = 'UPDATE usuarios SET nome=?, email=?, ativo=? WHERE id=?';
      $params = [ $data['nome'], $data['email'], (int)$data['ativo'], $id ];
    }
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
  }

  public function delete(int $id): bool {
    $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id=?');
    return $stmt->execute([$id]);
  }
}
