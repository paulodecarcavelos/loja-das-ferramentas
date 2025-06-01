<?php
// Inicia a sessão
session_start();

// Inclui o ficheiro de ligação à base de dados.
require '../db.php';

// Inclui o ficheiro de autenticação
require '../auth.php';

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Verifica se todos os dados obrigatórios foram enviados via POST
if (!isset($_POST['id']) || !isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit();
}

// Verifica se o utilizador é administrador
if (!isAdmin()) {
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}
// Obtém os dados do produto a ser atualizado
$id=intval($_POST['id']);         // Converte o ID para inteiro para evitar injeções SQL
$nome=$_POST['nome'];             // Obtém o nome do produto
$descricao=$_POST['descricao'];   // Obtém a descrição do produto
$preco=$_POST['preco'];           // Obtém o preço do produto

// Verifica se uma nova imagem foi enviada
if (isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0) {
    // Lê o conteúdo da imagem
    $imagem=file_get_contents($_FILES['imagem']['tmp_name']);
    // Prepara a query para atualizar todos os campos, incluindo a imagem
    $sql=$con->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?");
    $sql->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);
    $sql->send_long_data(3, $imagem);
} else {
    // Prepara a query para atualizar sem alterar a imagem
    $sql=$con->prepare("UPDATE produto SET nome=?, descricao=?, preco=? WHERE id=?");
    $sql->bind_param("ssdi", $nome, $descricao, $preco, $id);
}

// Executa a query
$sql->execute();

// Verifica se algum registro foi alterado
if ($sql->affected_rows>0) {
    echo json_encode(array("status"=>"success", "message" => "Produto atualizado com sucesso"));
} else {
    echo json_encode(array("status"=>"error", "message" => "Nenhuma alteração feita ou erro ao atualizar produto"));
}

$sql->close();  // Fecha a consulta preparada
$con->close();  // Fecha a ligação à base de dados

?>