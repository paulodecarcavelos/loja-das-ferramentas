<?php

// Verifica se o utilizador está autenticado
require 'auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: ../views/login.php"); // Redireciona para a página de login se o utilizador não estiver autenticado
    exit(); // Termina o script, para evitar que o código continue a ser executado
}

require 'db.php';
// Verifica se o utilizador está autenticado e usa o método POST para adicionar um produto ao carrinho
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    // Verifica se o produto já está no carrinho e se sim, atualiza a quantidade 
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);
    // Verifica se a quantidade é válida (>0)
    $sql = $con->prepare("SELECT quantidade FROM Carrinho WHERE produtoId = ? AND userId = ?");
    // Prepara a consulta para verificar se o produto já existe no carrinho do utilizador
    $sql->bind_param("ii", $produto_id, $_SESSION['user']['id']);
    // Vincula os parâmetros e executa a consulta
    $sql->execute();
    // Obtém o resultado da consulta
    $result = $sql->get_result();
    if ($result->num_rows>0) {
        // Produto já existe no carrinho, atualizar a quantidade
        $row = $result->fetch_assoc();
        // Obtém a quantidade atual do produto no carrinho
        $nova_quantidade = $row['quantidade'] + $quantidade;
        // Verifica se a nova quantidade é válida (>0)
        $update_sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE produtoId = ? AND userId = ?");
        // Prepara a consulta para atualizar a quantidade do produto no carrinho
        $update_sql->bind_param("iii", $nova_quantidade, $produto_id, $_SESSION['user']['id']);
        // Vincula os parâmetros e executa a consulta de atualização
        $update_sql->execute();
    } else {
        // Produto não existe no carrinho, adicionar novo item
        $insert_sql = $con->prepare("INSERT INTO Carrinho (produtoId, userId, quantidade) VALUES (?, ?, ?)");
        // Prepara a consulta para inserir um novo produto no carrinho, usando o ID do produto, ID do utilizador e a quantidade 'iii'
        $insert_sql->bind_param("iii", $produto_id, $_SESSION['user']['id'], $quantidade);
        // Vincula os parâmetros e executa a consulta de inserção
        $insert_sql->execute();
    }
    
    header("Location: ../index.php");   // Redireciona para a página principal após adicionar o produto ao carrinho
}

?>