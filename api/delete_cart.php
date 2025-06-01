<?php
require '../api/auth.php';  // Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.

session_start();    // Inicia a sessão para aceder às variáveis de sessão.

if(!isset($_SESSION["user"])){  // Verifica se a variável de sessão "user" não está definida (ou seja, o utilizador não está autenticado).
    header("Location: views/login.php");    // Se não estiver autenticado, redireciona para a página de login e termina a execução do script.
    exit(); // Termina a execução do script após o redirecionamento
}

require '../api/db.php';    // Inclui o ficheiro de ligação à base de dados.

// Verifica se o utilizador está autenticado e usa o método POST para remover um produto do carrinho
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["produtoId"])){

    // Verifica se o utilizador está autenticado
    $userId=$_SESSION["user"]["id"];

    $produtoId=$con->real_escape_string($_POST["produtoId"]);   //
    $sql=$con->prepare("DELETE FROM Carrinho WHERE userId=? AND produtoId=?");  // Prepara a consulta para remover o produto do carrinho
    $sql->bind_param("ii", $userId, $produtoId);    // Vincula os parâmetros userId e produtoId à consulta

    if($sql->execute()){
        header("Location: ../views/cart.php");  // Após remover o produto, redireciona para a página do carrinho
        exit();
    } else {
        echo "Erro ao remover produto do carrinho.";
    }
}

?>