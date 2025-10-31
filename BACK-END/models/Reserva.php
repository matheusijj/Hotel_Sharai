<?php
class Reserva {
  private PDO $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }

  public function verificarDisponibilidade(int $quartoId, string $entrada, string $saida): bool {
    $sql = 'SELECT COUNT(*) AS c FROM reservas WHERE quarto_id = ? AND NOT (data_saida <= ? OR data_entrada >= ?)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$quartoId, $entrada, $saida]);
    $row = $stmt->fetch();
    return (int)$row['c'] === 0;
  }

  public function criar(array $dados): int {
    $sql = 'INSERT INTO reservas (quarto_id, data_entrada, data_saida, nome_completo, email, cpf, telefone) VALUES (?,?,?,?,?,?,?)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      (int)$dados['quarto_id'],
      $dados['data_entrada'],
      $dados['data_saida'],
      $dados['nome_completo'],
      $dados['email'],
      $dados['cpf'],
      $dados['telefone'],
    ]);
    return (int)$this->pdo->lastInsertId();
  }

  // Admin CRUD helpers
  public function listAll(): array {
    $sql = 'SELECT r.id, r.quarto_id, q.numero AS numero_quarto, r.data_entrada, r.data_saida, r.nome_completo, r.email, r.cpf, r.telefone, r.created_at
            FROM reservas r JOIN quartos q ON q.id = r.quarto_id ORDER BY r.id DESC';
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll();
  }

  public function getById(int $id): ?array {
    $stmt = $this->pdo->prepare('SELECT id, quarto_id, data_entrada, data_saida, nome_completo, email, cpf, telefone FROM reservas WHERE id=?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public function update(int $id, array $dados): bool {
    $sql = 'UPDATE reservas SET quarto_id=?, data_entrada=?, data_saida=?, nome_completo=?, email=?, cpf=?, telefone=? WHERE id=?';
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
      (int)$dados['quarto_id'],
      $dados['data_entrada'],
      $dados['data_saida'],
      $dados['nome_completo'],
      $dados['email'],
      $dados['cpf'],
      $dados['telefone'],
      $id,
    ]);
  }

  public function delete(int $id): bool {
    $stmt = $this->pdo->prepare('DELETE FROM reservas WHERE id=?');
    return $stmt->execute([$id]);
  }

  public function existeConflitoDatas(int $quartoId, string $entrada, string $saida, ?int $ignorarId = null): bool {
    $sql = 'SELECT COUNT(*) AS c FROM reservas WHERE quarto_id = ? AND NOT (data_saida <= ? OR data_entrada >= ?)';
    $params = [$quartoId, $entrada, $saida];
    if ($ignorarId) {
      $sql .= ' AND id <> ?';
      $params[] = $ignorarId;
    }
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (int)$row['c'] > 0;
  }
}
