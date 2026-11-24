<?php
session_start();
unset($_SESSION['funcionario_id'], $_SESSION['funcionario_nome']);
header('Location: ../paginas/conta/login_funcionario.php');
exit;
