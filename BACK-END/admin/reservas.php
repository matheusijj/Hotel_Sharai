<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
if (!isset($lista)) {
  $reservaModel = new Reserva($pdo);
  $lista = $reservaModel->listAll();
}
?>
<h2 class="my-4">Gerenciar Reservas</h2>
<div class="d-flex justify-content-end mb-3">
  <a href="reserva_form.php" class="btn btn-primary">Nova Reserva</a>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Quarto</th>
            <th>Entrada</th>
            <th>Saída</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th style="width:160px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lista as $r): ?>
          <tr>
            <td><?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['numero_quarto']); ?></td>
            <td><?php echo htmlspecialchars($r['data_entrada']); ?></td>
            <td><?php echo htmlspecialchars($r['data_saida']); ?></td>
            <td><?php echo htmlspecialchars($r['nome_completo']); ?></td>
            <td><?php echo htmlspecialchars($r['email']); ?></td>
            <td><?php echo htmlspecialchars($r['telefone']); ?></td>
            <td>
              <a href="reserva_form.php?id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
              <a href="index.php?c=reservas&a=delete&id=<?php echo (int)$r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir esta reserva?');">Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
