<?php

session_start();

/* =========================
   VERIFICA LOGIN
========================= */

if (!isset($_SESSION['id_usuario'])) {

    header("Location: login.html");
    exit();
}

/* =========================
   CONEXÃO COM O BANCO
========================= */

$host = "localhost";
$user = "root";
$password = "";
$database = "spectrum";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* =========================
   DADOS DO USUÁRIO
========================= */

$id_usuario = (int) $_SESSION['id_usuario'];

/* =========================
   BUSCAR CERTIFICADOS
========================= */

$sql = "
SELECT 
    id_certificado,
    nome_usuario,
    nome_trilha,
    duracao,
    data_conclusao
FROM certificados
WHERE id_user = ?
ORDER BY data_conclusao DESC, id_certificado DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("i", $id_usuario);

$stmt->execute();

$result = $stmt->get_result();

?>