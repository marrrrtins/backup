<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" href="https://intranet.eusoujoaomartins.com/avatar-big.png" type="image/x-icon">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>

<body>

<?php

$secretKey = '0x9C84002D3ec11dEbd5cD6B65BbB68420F05fF287'; 

date_default_timezone_set('Europe/Lisbon');

require_once('src/PHPMailer.php');
require_once('src/SMTP.php');
require_once('src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if((isset($_POST['email']) && !empty(trim($_POST['email']))) && (isset($_POST['mensagem']) && !empty(trim($_POST['mensagem']))) && !empty($_POST['h-captcha-response'])) {

	$nome = !empty($_POST['nome']) ? $_POST['nome'] : 'Não informado';
	$email = $_POST['email'];
	$assunto = !empty($_POST['assunto']) ? utf8_decode($_POST['assunto']) : 'Não informado';
	$mensagem = $_POST['mensagem'];
	$date = date('d/m/Y H:i:s');
    $verifyURL = 'https://hcaptcha.com/siteverify'; 
             
            // Retrieve token from post data with key 'h-captcha-response' 
    $token = $_POST['h-captcha-response']; 
             
            // Build payload with secret key and token 
    $data = array( 
                'secret' => $secretKey, 
                'response' => $token, 
                'remoteip' => $_SERVER['REMOTE_ADDR'] 
    ); 

    $curlConfig = array( 
                CURLOPT_URL => $verifyURL, 
                CURLOPT_POST => true, 
                CURLOPT_RETURNTRANSFER => true, 
                CURLOPT_POSTFIELDS => $data 
            ); 
            $ch = curl_init(); 
            curl_setopt_array($ch, $curlConfig); 
            $response = curl_exec($ch); 
            curl_close($ch); 

    $responseData = json_decode($response); 

	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'smtp-relay.sendinblue.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'joaofranciscogmartins@gmail.com';
	$mail->Password = 'sFEQvIH1t3WVbCr6';
	$mail->Port = 587; //587
    $mail->CharSet = 'UTF-8';

	$mail->setFrom('contato@eusoujoaomartins.com', 'João Martins'); // de
	$mail->addAddress('josecarvalhomanueljoaquim@gmail.com');
    $mail->addReplyTo($email, $nome);

	$mail->isHTML(true);
	$mail->Subject = $assunto.' - Site João Martins';
    
	$mail->Body = "Nome: {$nome}<br>
				   Email: {$email}<br>
				   Mensagem: {$mensagem}<br>
				   Data/hora: {$date}";

    // $mail->addAttachment('images/phpmailer_mini.png')

	if($mail->send() && $responseData->success) {
		echo "Email enviado com sucesso."; // gren
	} else {
		echo 'A verificação do robô falhou, tente novamente.'; // red
	}
} else {
	echo 'Não enviado: preencher obrigatoriamente o email, a mensagem e o recaptcha.'; // blue
}




