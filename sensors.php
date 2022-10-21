<?php
require_once "connection.php";
require_once "jwt.php";

if ($_SERVER ["REQUEST_METHOD"]=="OPTINS")exit(0);
$jwt = apache_request_headers()["Authorization"];

if (strstr($jwt, "Bearer")) {
    $jwt = substr($jwt, 7);
}

if (JWT::verify($jwt, "12345678")) {
    header("HTTP/1.1 400 Unauthorized");
    exit();
}


$metodo = $_SERVER["REQUEST_METHOD"];

switch ($metodo) {
    case 'GET':
        //consulta
        $c = connection();
       if(isset($_GET['id'])){
        $s = $c->prepare("SELECT * FROM sensors WHERE id=:pid");
        $s->bindValue(":pid",$id);
        $s->execute();
        $s->setFetchMode(PDO::FETCH_ASSOC);
        $r = $s->fetch();
       }else{
        $s = $c->prepare("SELECT * FROM sensors");
        $s->execute();
        $s->setFetchMode(PDO::FETCH_ASSOC);
        $r = $s->fetchAll();
       }
       echo json_encode($r);
        break;
    case "POST":
        //insertar
        if (!isset($_POST['type']) || !isset($_POST['value'])) {
            header("HTTP/1.1 400 BAd Request");
            return;
        }
        $c = connection();
        $s = $c ->prepare("INSERT INTO sensors(user,type,value,date) VALUES(:u, :t, :v, :d)");
        $s->bindValue(":u", "admin");
        $s->bindValue(":t", $_POST['type']);
        $s->bindValue(":v", $_POST['value']);
        $s->bindValue(":d", date("Y-m-d H:i:s"));
        $s->execute();
        //if ($s->rowCount()==0) {
            //header("HTTP/1.1 400 Bad Request");
            //return;
        //}

        echo json_encode(["status"=>"ok", "id"=>$c->lastInsertId()]);
        break;

    case 'PUT':
            //Actualizar
            if (!isset($_GET['type']) || !isset($_GET['value']) || !isset($_GET['id'])) {
                header("HTTP/1.1 400 BAd Request");
                return;
            }
            $c = connection();
            $s = $c ->prepare("UPDATE sensors SET type=:t, value=:v WHERE id=id");
            $s->bindValue(":id", $_GET['id']);
            $s->bindValue(":t", $_GET['type']);
            $s->bindValue(":v", $_GET['value']);
            $s->execute();

            echo json_encode(["status"=>"ok"]);
        break;

    case 'DELETE':
        //Eliminar
        if (!isset($_GET['id'])) {
            header("HTTP/1.1 400 BAd Request");
            return;
        }
        $c = connection();
        $s = $c ->prepare("DELETE FROM sensors WHERE id=id");
        $s->bindValue(":id", $_GET['id']);
        $s->execute();

        echo json_encode(["status"=>"ok"]);
        break;
    
}