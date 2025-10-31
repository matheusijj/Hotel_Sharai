<?php
class QuartoController {
  private Quarto $quarto;
  public function __construct(PDO $pdo) { $this->quarto = new Quarto($pdo); }
  public function listarPublico() {
    $lista = $this->quarto->listarPublico();
    header('Content-Type: application/json');
    echo json_encode($lista);
  }
}
