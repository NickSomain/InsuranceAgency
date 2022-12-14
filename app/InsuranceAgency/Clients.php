<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
header('Content-Type: application/json');
$con = new mysqli("MYSQL", "root", "", "InsuranceAgency");
$answer = array();
switch ($requestMethod) {
    case 'GET':
        if (empty(isset($_GET['client_id']))) {
            $result = $con->query("SELECT * FROM Clients;");
            while ($row = $result->fetch_assoc()) {
                $answer[] = $row;
            }
        } else {
            $query_result = $con->query("SELECT * FROM Clients WHERE client_id = " . $_GET['client_id'] . ";");
            $result = $query_result->fetch_assoc();
            $answer = $result;
        }
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($answer);
        } else {
            http_response_code(204);
        }
        break;

    case 'POST':
        $json = file_get_contents('php://input');
        $client = json_decode($json);
        if (!empty($client->{'client_name'}) &&
            !empty($client->{'client_surname'}) &&
            !empty($client->{'client_phone'}) &&
            !empty($client->{'client_email'})) {
            $client_name = $client->{'client_name'};
            $client_surname = $client->{'client_surname'};
            $client_phone = $client->{'client_phone'};
            $client_email = $client->{'client_email'};
            $query_result = $con->query("SELECT * FROM Clients WHERE client_surname = '" . $client_surname . "'");
            if (!empty($result)) {
                http_response_code(409);
            } else {
                $stmt = $con->prepare("INSERT INTO Clients (client_name, client_surname, client_phone, client_email) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('ssss', $client_name, $client_surname, $client_phone, $client_email);
                $stmt->execute();
                http_response_code(201);
            }
        } else {
            http_response_code(422);
        }

        break;

    case 'PATCH':
        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        #break if no id
        if (empty(isset($_GET['client_id']))){
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        }
        else
        {
            $query_result = $con->query("SELECT * FROM Clients WHERE client_id='".$_GET['client_id']."'");
            $result = $query_result->fetch_row();

            if (!empty($result)){

                if(!empty($obj->{'client_name'}))
                    $con->query("UPDATE Clients SET client_name='".$obj->{'client_name'}."'WHERE client_id ='".$_GET['client_id']."'");

                if(!empty($obj->{'client_surname'}))
                    $con->query("UPDATE Clients SET client_surname ='".$obj->{'client_surname'}."'WHERE client_id='".$_GET['client_id']."'");

                if(!empty($obj->{'client_phone'}))
                    $con->query("UPDATE Clients SET client_phone ='".$obj->{'client_phone'}."' WHERE client_id='".$_GET['client_id']."'");

                if(!empty($obj->{'client_email'}))
                    $con->query("UPDATE Clients SET client_email ='".$obj->{'client_email'}."' WHERE client_id='".$_GET['client_id']."'");

                $answer["status"] = "Success. User updated.";
                http_response_code(200);

            } else {
                $answer["status"] = "Error. User not found.";
                http_response_code(404);
            }
        }
        echo json_encode($answer);
        break;

    case 'DELETE':
        if (empty(isset($_GET['client_id']))) {
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM clients WHERE client_id='" . $_GET['client_id'] . "'");
            $result = $query_result->fetch_row();
            if (!empty($result)) {
                $query_result = $con->query("DELETE FROM clients WHERE client_id='" . $_GET['client_id'] . "'");
                http_response_code(200);
            } else {
                http_response_code(204);
            }
        }
        break;
    default:
        http_response_code(405);
        break;
}
?>
