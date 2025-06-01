<?php

session_start();        // Inicia a sessão
require '../db.php';    // Inclui ficheiro de ligação à base de dados
require '../auth.php';  // Inclui ficheiro de autenticação

// Verificar se o utilizador é administrador
if(!isAdmin()){
    echo json_encode(array("status"=>"error", "message"=>"Acesso negado"));
    exit();
}

// Verifica se o ID do produto foi fornecido
if(!isset($_GET['id'])) {
    echo json_encode(array("status" => "error", "message" => "ID do produto não fornecido"));
    exit();
}

// Obtém o ID do produto a eliminar
$id=$_GET['id'];

// Prepara a query para eliminar o produto
$sql=$con->prepare("DELETE FROM produto WHERE id=?");
$sql->bind_param("i", $id);
$sql->execute();

// Verifica se o produto foi eliminado com sucesso
if($sql->affected_rows>0){
    echo json_encode(array("status"=>"success", "message"=>"Produto eliminado com sucesso"));
}else{
    echo json_encode(array("status"=>"error", "message"=>"Erro ao eliminar produto"));
}

// Fecha a ligação à base de dados
$sql->close();  
$con->close();

?>
