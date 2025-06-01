<?php

require 'db.php';
require 'email.php';

/**
 * Login de um utilizador
 * 
 * @param string $username -> Nome de utilizador ou email
 * @param string $password -> Password do utilizador
 * @return bool -> true se o login foi bem sucedido, false caso contrário
 */
function login($userinput,$password){  
    global $con;
    $sql = $con->prepare("SELECT * FROM Utilizador WHERE (username = ? OR email = ?) AND active = 1");
    $sql->bind_param('ss',$userinput,$userinput);
    $sql->execute();
    $result = $sql->get_result();
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $_SESSION["user"] = $row;
        if( password_verify($password,$row["password"]) ){
            return true;
        }
    }
    return false;
}

/** 
 * Registo de um novo utilizador
 * 
 * @param string $email     -> Email do utilizador
 * @param string $username  -> Nome de utilizador
 * @param string $password  -> Password do utilizador
 * @param string $telemovel -> Número de telemóvel
 * @param string $nif       -> Número de Identificação Fiscal
 * @return bool -> true se o registo foi bem sucedido, false caso contrário
 */
function registo($email,$username,$password,$telemovel,$nif){
        global $con;
        //1º - Criar e preparar a query de insert
        $sql = $con->prepare('INSERT INTO Utilizador(email,username,password,telemovel,nif,token,RoleID) VALUES (?,?,?,?,?,?,2)');
        //2º - Gerar o token aletório
        $token = bin2hex(random_bytes(16));
        //3º - Encriptar a password
        $password = password_hash($password, PASSWORD_DEFAULT);
        //4º - Colocar os dados na query e executar a mesma e ver se deu certo
        $sql->bind_param('ssssss',$email,$username,$password,$telemovel,$nif,$token);
        $sql->execute();
        if($sql->affected_rows > 0){
            //5º - Enviar o email com o token para ativar a conta
            send_email($email,'Ativar a conta',"<a href='localhost/24198_Loja/views/ativarconta.php?email=$email&token=$token'> Ative a sua conta</a>");
            return true;
        }else{
            //O registo falhou
            return false;
        }
}

/**
 * Ativar a conta do utilizador
 * 
 * @param string $email -> Email do utilizador
 * @param string $token -> Token de ativação
 * @return bool -> true se a ativação foi bem sucedida, false caso contrário
 */
function ativarConta($email,$token){
    global $con;    // Usa a ligação global à base de dados (db.php)
    // Prepara um comando SQL para ativar a conta com o email e token fornecidos
    $sql = $con->prepare("UPDATE Utilizador SET active = 1 WHERE email = ? AND token = ?");
    // Liga os parâmetros à query SQL que são passados para evitar a SQL Injection, no email e token, ambos strings 'ss'
    $sql->bind_param('ss',$email,$token);
    // Executa a query SQL ou statement
    $sql->execute();

    // Verifica se a query afetou alguma linha, ou seja, se houve sucesso na ativação
    if($sql->affected_rows>0){
        return true;    // Conta ativada com sucesso
    }else{
        return false;   // Nenhuma linha afetada, ou seja, falha na ativação (token ou email inválidos)
    }
}

function logout(){
}

function apagarConta(){
}

function isAdmin(){
    // Verifica se o utilizador autenticado é um administrador
    if($_SESSION["user"]["RoleID"] == 1){
        return true;    // Retorna true se for administrador
    }else{
        return false;   // Retorna false se não for administrador
    }
}