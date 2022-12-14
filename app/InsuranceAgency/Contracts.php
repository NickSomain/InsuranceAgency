<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
header('Content-Type: application/json');
$con = new mysqli("MYSQL", "root", "", "InsuranceAgency");
$answer = array();
switch ($requestMethod) {
    case 'GET':
        if (empty(isset($_GET['contract_id']))) {
            $result = $con->query("SELECT * FROM Contracts;");
            while ($row = $result->fetch_assoc()) {
                $answer[] = $row;
            }
        } else {
            $query_result = $con->query("SELECT * FROM Contracts WHERE contract_id = " . $_GET['contract_id'] . ";");
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
        $contract = json_decode($json);
        if (!empty($contract->{'client_id'}) &&
            !empty($contract->{'insurance_id'})) {
            $client_id = $contract->{'client_id'};
            $insurance_id = $contract->{'insurance_id'};
            $query_result = $con->query("SELECT * FROM Contracts WHERE client_id = '" . $client_id . "'");
            if (!empty($result)) {
                http_response_code(409);
            } else {
                $stmt = $con->prepare("INSERT INTO Contracts (client_id, insurance_id) VALUES (?, ?)");
                $stmt->bind_param('ii', $client_id, $insurance_id);
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
        if (empty(isset($_GET['contract_id']))){
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        }
        else
        {
            $query_result = $con->query("SELECT * FROM Contracts WHERE contract_id='".$_GET['contract_id']."'");
            $result = $query_result->fetch_row();

            if (!empty($result)){

                if(!empty($obj->{'client_id'}))
                    $con->query("UPDATE Contracts SET client_id='".$obj->{'client_id'}."'WHERE contract_id ='".$_GET['contract_id']."'");

                if(!empty($obj->{'insurance_id'}))
                    $con->query("UPDATE Contracts SET insurance_id ='".$obj->{'insurance_id'}."'WHERE contract_id='".$_GET['contract_id']."'");

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
        if (empty(isset($_GET['contract_id']))) {
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM Contracts WHERE contract_id='" . $_GET['contract_id'] . "'");
            $result = $query_result->fetch_row();
            if (!empty($result)) {
                $query_result = $con->query("DELETE FROM Contracts WHERE contract_id='" . $_GET['contract_id'] . "'");
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
