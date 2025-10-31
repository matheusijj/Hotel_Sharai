<?php
class Quarto {
  private PDO $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }
  public function listarPublico(): array {
    $stmt = $this->pdo->query('SELECT id, numero, tipo FROM quartos WHERE status=1 ORDER BY numero');
    return $stmt->fetchAll();
  }
  public function buscarPorId(int $id): ?array {
    $stmt = $this->pdo->prepare('SELECT id, numero, tipo, preco_noite, descricao, status FROM quartos WHERE id=?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  // Admin
  public function listAll(): array {
    $stmt = $this->pdo->query('SELECT id, numero, tipo, IFNULL(preco_noite,0) AS preco_noite, IFNULL(descricao, "") AS descricao, status FROM quartos ORDER BY id DESC');
    return $stmt->fetchAll();
  }

  public function create(array $data): int {
    $sql = 'INSERT INTO quartos (numero, tipo, preco_noite, descricao, status) VALUES (?,?,?,?,?)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      $data['numero'],
      $data['tipo'],
      isset($data['preco_noite']) ? $data['preco_noite'] : null,
      isset($data['descricao']) ? $data['descricao'] : null,
      isset($data['status']) ? (int)$data['status'] : 1,
    ]);
    return (int)$this->pdo->lastInsertId();
  }

  public function update(int $id, array $data): bool {
    $sql = 'UPDATE quartos SET numero=?, tipo=?, preco_noite=?, descricao=?, status=? WHERE id=?';
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
      $data['numero'],
      $data['tipo'],
      isset($data['preco_noite']) ? $data['preco_noite'] : null,
      isset($data['descricao']) ? $data['descricao'] : null,
      isset($data['status']) ? (int)$data['status'] : 1,
      $id,
    ]);
  }

  public function delete(int $id): array {
    try {
      $stmt = $this->pdo->prepare('DELETE FROM quartos WHERE id=?');
      $ok = $stmt->execute([$id]);
      return ['success' => (bool)$ok, 'soft' => false];
    } catch (\PDOException $e) {
      if ($e->getCode() === '23000') {
        $stmt = $this->pdo->prepare('UPDATE quartos SET status=0 WHERE id=?');
        $ok = $stmt->execute([$id]);
        return ['success' => (bool)$ok, 'soft' => true];
      }
      return ['success' => false, 'soft' => false, 'error' => $e->getMessage()];
    }
  }
}
