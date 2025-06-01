<?php
// Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.
require '../api/auth.php';
// Inicia a sessão para aceder às variáveis de sessão.
session_start();
// Verifica se a variável de sessão "user" não está definida (ou seja, o utilizador não está autenticado).
if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit(); // Se não estiver autenticado, redireciona para a página de login e termina a execução do script.
}

require '../api/db.php';    // Inclui o ficheiro de ligação à base de dados.
// Ternário para capturar o parâmetro de pesquisa da URL (GET).
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["produtoId"]) && isset($_POST["quantidade"])){
    // Verifica se o método de requisição é POST e se os campos produtoId e quantidade estão definidos.
    $userId = $_SESSION["user"]["id"];
    $produtoId = $con->real_escape_string($_POST["produtoId"]);
    $quantidade = $con->real_escape_string($_POST["quantidade"]);
    // Condição para a quantidade: se for menor ou igual a zero, remove o item do carrinho; caso contrário, atualiza a quantidade.
    if($quantidade<=0){
        $sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ? AND produtoId = ?");    // Prepara a query para remover o item do carrinho.
        $sql->bind_param("ii", $userId, $produtoId);    // Liga os parâmetros do tipo integer 'ii' (userId e produtoId) à query.
    } else {
        $sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE userId = ? AND produtoId = ?");  // Prepara a query para atualizar a quantidade do item no carrinho.
        // Liga os parâmetros do tipo integer 'iii' (quantidade, userId e produtoId) à query.
        $sql->bind_param("iii", $quantidade, $userId, $produtoId);
    }
    // Executa a query e verifica se foi bem-sucedida.
    if($sql->execute()){
        header("Location: ../views/cart.php");  // Redireciona para a página do carrinho após atualizar ou remover o item.
        exit();
    } else {
        echo "Erro ao atualizar carrinho.";
    }
}

?>