<?php
  // Ativa o relatório de erros do MySQLi (útil para debugging durante o desenvolvimento)
  mysqli_report(  MYSQLI_REPORT_ERROR);
  // Cria uma nova ligação à base de dados MySQL usando o MySQLi     
  $con = new mysqli("localhost","root","","24198_Loja");
  // Verifica se a ligação foi bem sucedida
  if ($con->connect_error)
  {
    // Se a ligação falhar, mostra uma mensagem de erro e termina o script  
    die("connection failed: " . $con->connect_error);
  }

?>