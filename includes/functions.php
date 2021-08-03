<?php

function addAction($pdo, $name, $price, $qtt, $code, $compte){

    $stmt = $pdo->prepare("SELECT * from actions WHERE code = :code");
    $stmt->execute([":code" => $code]);
    $r = $stmt->rowCount();
    if ($r > 0){
        return;
    }
        $stmt = $pdo->prepare("
                INSERT INTO actions (nom, prix, quant, compte, code) 
                VALUES (:name, :price, :qtt, :compte, :code)
                ");
        $stmt->execute([
            ":name" => $name,
            ":price" => $price,
            ":qtt" => $qtt,
            ":compte" => $compte,
            ":code" => $code
        ]);
}

function displayAction($pdo){
    $stmt = $pdo->prepare("SELECT * from actions");
    $stmt->execute([]);
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for($i = 0; $i <= count($dataArr)-1; $i++){
        echo "<h3>".$dataArr[$i]["nom"]."</h3>";
        echo "<span> Prix : ".$dataArr[$i]["prix"]."</span></br>";
        echo "<span> Quantité : ".$dataArr[$i]["quant"]."</span></br>";
        echo "<span> Sur le compte : ".$dataArr[$i]["compte"]."</span></br>";
        echo "<span> Code d'identification : ".$dataArr[$i]["code"]."</span></br>";
    }
}

function callApi($pdo){
    $allData = array();
    $stmt = $pdo->prepare("SELECT* FROM actions");
    $stmt->execute([]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($data as $key => $val){
        $dataCode = $data[$key]["code"];
        $name = $data[$key]["nom"];
        $compte = $data[$key]["compte"];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/v3/get-statistics?symbol=". $dataCode .".PA",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: apidojo-yahoo-finance-v1.p.rapidapi.com",
                "x-rapidapi-key: 12c6f506b5msh659a92c946d315bp1a46b0jsn627f99fc03fa"
            ],
        ]);
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            // var_dump ($response);    
            $cours = $response["price"]["regularMarketPrice"]["fmt"];
            $actualChange = $response["price"]["regularMarketChange"]["fmt"];
            $cout_de_revient = $data[$key]["prix"];
            $valorisation_initiale = $data[$key]["prix"]*$data[$key]["quant"];
            $valorisation = $data[$key]["quant"]*$cours;
            $variation = $response["price"]["regularMarketChangePercent"]["fmt"];
            $delta = ($cours - $cout_de_revient) * $data[$key]["quant"];
            $deltaP = ($cours - $cout_de_revient) / $cout_de_revient;
            $vals = array(
                $name, $cours, $actualChange, $variation, $cout_de_revient, $valorisation_initiale, $valorisation, $delta, $deltaP, $compte
            );
            array_push($allData, $vals);
        }
    }
return $allData;
}


function tab($vals){
    foreach($vals as $key => $val){
    echo "<tr>";
    echo "<td>". $vals[$key][0] ."</td>";
    echo "<td>". $vals[$key][1] ."</td>";
    echo "<td class='colorCh'>". $vals[$key][2] ."</td>";
    echo "<td>". $vals[$key][3] ."</td>";
    echo "<td>". $vals[$key][4] ."</td>";
    echo "<td>". $vals[$key][5] ."</td>";
    echo "<td>". $vals[$key][6] ."</td>";
    echo "<td>". $vals[$key][7] ."</td>";
    echo "<td>". $vals[$key][8] ."%</td>";
    echo "<td>". $vals[$key][9] ."</td>";
    echo "</tr>";
    }
}

// cout de revient = prix d'achat
// valoraisation initiale : formule
// variation : variation du cours à l'instant t (delta veille)
// cours : à 'linsntant t
//valoraisation : formule


function test(){
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://apidojo-yahoo-finance-v1.p.rapidapi.com/stock/v3/get-statistics?symbol=AB.PA",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "x-rapidapi-host: apidojo-yahoo-finance-v1.p.rapidapi.com",
            "x-rapidapi-key: 12c6f506b5msh659a92c946d315bp1a46b0jsn627f99fc03fa"
        ],
    ]);
    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    $response = json_decode($response, true);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        var_dump ($response);
    }
}
?>

