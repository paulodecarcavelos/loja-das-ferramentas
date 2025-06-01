<?php 

session_start();        // Inicia a sessão para aceder às variáveis de sessão.
require '../db.php';    // Inclui o ficheiro de ligação à base de dados.  
require '../auth.php';  // Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.

// Verifica se os dados obrigatórios foram enviados via POST.
if(!isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    // Se faltar algum dado obrigatório, retorna um erro em formato JSON e termina a execução.
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit(); // Termina a execução do script.
}
// Verifica se o ficheiro de imagem foi enviado e se não houve erro no upload.
if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !==0) {
    // Se não houver imagem ou se houver erro no upload, retorna um erro em formato JSON e termina a execução.    
    echo json_encode(array("status" => "error", "message" => "Erro no upload da imagem"));
    exit(); // Termina a execução do script.
}
// Verifica se o utilizador é administrador.
if(!isAdmin()){ 
    echo json_encode(array("status" => "error", "message" => "Acesso negado")); // Se o utilizador não for administrador, retorna um erro em formato JSON.
    exit(); // Termina a execução do script.
}

$imagem = file_get_contents($_FILES['imagem']['tmp_name']); //

// Prepara a query SQL para inserir um novo produto na tabela 'produto' com os campos nome, descricao, preco e imagem.
$sql = $con->prepare("INSERT INTO produto (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)");
// Liga os parâmetros do tipo string 'ss' (nome e descricao), double 'd' (preco) e blob 'b' (imagem) à query.
$sql->bind_param("ssdb", $_POST['nome'], $_POST['descricao'], $_POST['preco'], $imagem);
// Envia os dados da imagem como um blob para a query preparada.
$sql->send_long_data(3, $imagem);   
$sql->execute();    // Executa a query para inserir o produto na base de dados.

// Verifica se a query afetou alguma linha (ou seja, se o produto foi inserido com sucesso).
if($sql->affected_rows>0){
    // Se a inserção foi bem-sucedida, retorna um sucesso em formato JSON.
    echo json_encode(array("status" => "success", "message" => "Produto inserido com sucesso"));
}else{
    // Se houve algum erro na inserção, retorna um erro em formato JSON com a mensagem de erro.
    echo json_encode(array(
        "status" => "error",    // Define o status como "error".
        "message" => "Erro ao inserir produto: " . $sql->error  // Retorna a mensagem de erro da query SQL.
    ));
}

$sql->close();  // Fecha a query preparada para liberar recursos.
$con->close();  // Fecha a ligação à base de dados para liberar recursos.

?>