<?php

function displayFilter($filterArr){
    echo "<form>";
    foreach($filterArr as $key => $val){
        echo "<input type='checkbox' id='". $val[0] ."' name='". $val[1] ."'>";
        echo "<label class='filter_label' for='". $val[1] ."'>". $val[2] ."</label> </br>";
    }
    echo "<label class='search_key' for='filter_comm'> Mots clés : </label>";
	echo "<input type='text' name='filter_comm' id='filtre_c'> </br>";

    echo "<input type='submit' name='search' value='Filtrer'>";
    echo "</form>";
}

function getFilters(){
    $arr = [];
    $adresse = $_SERVER['PHP_SELF'];
    $i = 0;
    foreach($_GET as $cle => $valeur){
        $adresse .= ($i == 0 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
        $i++;
    }
    $filters = substr($adresse, 21);
    $filtersArr = explode("&", $filters);
    foreach($filtersArr as $key => $val){
        $val = str_replace("=on", "", $val);
        $val = str_replace("_", " ", $val);
        array_push($arr, $val);
    }
    array_pop($arr);
    return $arr;
}

// $variation_final = explode("</span>",$variation_init[1]);


function addAction($pdo, $name, $price, $qtt, $code, $compte, $comment){

    $stmt = $pdo->prepare("SELECT * from actions WHERE code = :code");
    $stmt->execute([":code" => $code]);
    
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($dataArr as $val ){    
        if ($val["compte"] === $compte){
            $qtt = intval($qtt) + intval($dataArr[0]["quant"]);

            $stmt = $pdo->prepare("UPDATE actions SET quant =  '". $qtt ."' WHERE code = :code && compte = :compte");
            $stmt->execute([
                ":code" => $code,
                ":compte" => $compte,
            ]);

            return;
        }
    }
    
    $r = $stmt->rowCount();
    // if ($r > 0){
        //     return;
        // }
        $stmt = $pdo->prepare("
        INSERT INTO actions (nom, prix, quant, compte, code, state, commentaires) 
        VALUES (:name, :price, :qtt, :compte, :code, :state, :comment)
        ");
        $stmt->execute([
            ":name" => $name,
            ":price" => $price,
            ":qtt" => $qtt,
            ":compte" => $compte,
            ":code" => $code,
            ":state" => "yes",
            ":comment" => $comment,
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

function displayActionSell($pdo){
    $stmt = $pdo->prepare("SELECT * from vendus");
    $stmt->execute([]);
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($dataArr as $key => $val){
            echo "<tr>";
            echo "<td>". $dataArr[$key]["nom"] ."</td>";
            echo "<td>". $dataArr[$key]["prix"] ."</td>";
            echo "<td>". $dataArr[$key]["quant"] ."</td>";
            echo "<td>". $dataArr[$key]["compte"] ."</td>";
            echo "<td>". $dataArr[$key]["code"] ."</td>";
            echo "<td>". $dataArr[$key]["cours"] ."</td>";
            echo "<td>". $dataArr[$key]["variation"] ."</td>";
            echo "<td>". $dataArr[$key]["valo_ini"] ."</td>";
            echo "<td>". $dataArr[$key]["valo"] ."</td>";
            echo "<td class='colorCh'>". $dataArr[$key]["gain"] ."</td>";
            echo "</tr>";
    }



}

function callApi($pdo, $filtre, $mot){
    $resource = curl_init();
    $url = "https://www.zonebourse.com/recherche/?add_mots=";
    $arrCompte = [];
    $arrMot =[];
    // var_dump($filtre);

    foreach($filtre as $key => $val){
        if (strpos($val, "Philippe") || strpos($val, "Sandrine")){
            array_push($arrCompte, $val);
        } else if(strpos($val, "=")){
            $newVal = str_replace("filter comm=", "", $val);
            $newVal = explode(", ", $newVal);
            foreach($newVal as $value){
                array_push($arrMot, $value);
            }
        } else if(strstr($val, "Mode")){
            $mode = $val;
        } else{
            $gain = $val;
        }
    }
    $text = "SELECT * from actions WHERE (id = 'lol' ";
    if (count($arrCompte) > 0){
        foreach($arrCompte as $val){
            $text = substr_replace($text, "OR compte like '%". $val ."%' ", strlen($text));
        }
        $text = substr_replace($text, ") ", strlen($text));
        if (count($arrMot) > 0){
            $text = substr_replace($text, "AND (id = 'lol' ", strlen($text));
            foreach($arrMot as $val){
                $text = substr_replace($text, "OR commentaires like '%". $val ."%' ", strlen($text));
            }
            $text = substr_replace($text, ") ", strlen($text));
        }
    }else if (count($arrMot) > 0){
        foreach($arrMot as $val){
            $text = substr_replace($text, "OR commentaires like '%". $val ."%' ", strlen($text));
        }
        $text = substr_replace($text, ") ", strlen($text));
    } else{
        $text = "SELECT * from actions";
    }
    $stmt = $pdo->prepare($text);
    $stmt->execute([]);
    $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $valsTab = [];
    for($i = 0; $i <= count($dataArr)-1; $i++){
        // configuration
        curl_setopt( $resource, CURLOPT_URL, 'https://fr.finance.yahoo.com/quote/'.$dataArr[$i]["code"]);
        curl_setopt( $resource, CURLOPT_RETURNTRANSFER, true );
        
        // récupération du fichier
        $page = curl_exec( $resource );

        if (strpos($page, '<span class="Trsdu(0.3s) Fw(500) Pstart(10px) Fz(24px) C($positiveColor)" data-reactid="32">')){
            $variation_init = explode('<span class="Trsdu(0.3s) Fw(500) Pstart(10px) Fz(24px) C($positiveColor)" data-reactid="32">', $page);
        } else if(strpos($page, '<span class="Trsdu(0.3s) Fw(500) Pstart(10px) Fz(24px) C($negativeColor)" data-reactid="32">')){
            $variation_init = explode('<span class="Trsdu(0.3s) Fw(500) Pstart(10px) Fz(24px) C($negativeColor)" data-reactid="32">', $page);
        } else{
            $variation_init = explode('<span class="Trsdu(0.3s) Fw(500) Pstart(10px) Fz(24px)">', $page); // A FAIRE QUAND MISE A JOUR
        }
        $variation_final = explode("</span>",$variation_init[1]);
        $variation_final2 = explode("(",$variation_final[0]);
        $variation_final3 = explode(")",$variation_final2[1]);
        
        echo "</br>";
        $valeur_init = explode('<span class="Trsdu(0.3s) Fw(b) Fz(36px) Mb(-4px) D(ib)" data-reactid="31">', $page);
        $valeur_final = explode("</span>",$valeur_init[1]);
        // libération de la ressource
        $newTab = [];


        
        array_push($newTab, $dataArr[$i]["nom"]);
        array_push($newTab, $dataArr[$i]["code"]);
        array_push($newTab, $dataArr[$i]["quant"]);
        array_push($newTab, $valeur_final[0]);
        array_push($newTab, $variation_final3[0]);
        array_push($newTab, $dataArr[$i]["prix"]);
        $valoInit = $dataArr[$i]["prix"] * $dataArr[$i]["quant"];
        array_push($newTab, $valoInit);
        $valoActual = (int)$valeur_final[0] * $dataArr[$i]["quant"];
        array_push($newTab, $valoActual);
        $gain = ((int)$valeur_final[0] - $dataArr[$i]["prix"]) * $dataArr[$i]["quant"];
        array_push($newTab, $gain);
        array_push($newTab, $dataArr[$i]["compte"]);
        array_push($newTab, $dataArr[$i]["commentaires"]);
        $name = $dataArr[$i]["nom"];
        $name = strtolower($name);
        $name = str_replace(" ", "+", "$name");
        $final_url = substr_replace($url, $name."&type_recherche_forum=0", strlen($url));
        array_push($newTab, $final_url);
        if(intval($dataArr[$i]["quant"]) > 0){
            array_push($valsTab, $newTab);
        }
    };
    curl_close( $resource );
    return $valsTab;
}

function fillTab($valsTab, $mode){
    if ($mode === "alpha"){
        array_multisort($valsTab, SORT_ASC, $valsTab);
    } else if ($mode === "croissant"){
        foreach ($valsTab as $key => $val){
            $num[$key] = $val[7];
        }
        array_multisort($num, SORT_ASC, $valsTab);
    } else if ($mode === "décroissant"){
        foreach ($valsTab as $key => $val){
            $num[$key] = $val[7];
        }
        array_multisort($num, SORT_DESC, $valsTab);
    }
    foreach ($valsTab as $key => $val){
        echo "<tr>";
        echo "<td>". $valsTab[$key][0] ."</td>";
        echo "<td>". $valsTab[$key][1] ."</td>";
        echo "<td>". $valsTab[$key][2] ."</td>";
        echo "<td>". $valsTab[$key][3] ."</td>";
        if (intval($valsTab[$key][4]) >= 0){
            $a = "⬆️";
        } else{
            $a = "⬇️";
        }
        echo "<td class='colorCh'>". $valsTab[$key][4] . $a ."</td>";
        echo "<td>". $valsTab[$key][5] ."</td>";
        echo "<td>". $valsTab[$key][6] ."</td>";
        echo "<td>". $valsTab[$key][7] ."</td>";
        if (intval($valsTab[$key][8]) >= 0){
            $a = "⬆️";
        } else{
            $a = "⬇️";
        }
        echo "<td class='colorCh'>". $valsTab[$key][8] . $a ."</td>";
        echo "<td>". $valsTab[$key][9] ."</td>";
        echo "<td>". $valsTab[$key][10] ."</td>";
        echo "<td> <a href='". $valsTab[$key][11] ."'>News 1</a> </td>";
        echo "</tr>";
    }
}

// cout de revient = prix d'achat
// valoraisation initiale : formule
// variation : variation du cours à l'instant t (delta veille)
// cours : à 'linsntant t
//valoraisation : formule

function sellAction($pdo, $sell_code, $sell_qt, $sell_compte, $valsTab){
    
    $stmm = $pdo->prepare("SELECT * from actions WHERE code = :code && compte = :compte");
    $stmm->execute([
        ":code" => $sell_code,
        ":compte" => $sell_compte
    ]);
    $dataArr = $stmm->fetchAll(PDO::FETCH_ASSOC);
    $initQt = $dataArr[0]["quant"];

    $newQt = intval($initQt) - intval($sell_qt);
    if ($newQt < 0){
        echo " </br> Vous ne possédez pas assez d'actions.";
        return;
    }
    $stmt = $pdo->prepare("UPDATE actions SET quant = ". $newQt ." WHERE code = :code && compte = :compte");
    $stmt->execute([
        ":code" => $sell_code,
        ":compte" => $sell_compte
    ]);

    foreach($valsTab as $key => $val){
        if ($val[1] === $sell_code){
            $stmv = $pdo->prepare("
            INSERT INTO vendus (nom, prix, quant, compte, code, commentaires, cours, variation, valo_ini, valo, gain)
            VALUES (:name, :price, :qtt, :compte, :code, :comment, :cours, :variation, :valo_ini, :valo, :gain)");
            $stmv->execute([
                ":name" => strval($val[0]),
                ":price" => strval($val[5]),
                ":qtt" => strval($sell_qt),
                ":compte" => strval($sell_compte),
                ":code" => strval($sell_code),
                ":comment" => strval($val[10]),
                ":cours" => strval($val[3]),
                ":variation" => strval($val[4]),
                ":valo_ini" => strval($val[6]),
                ":valo" => strval($val[7]),
                ":gain" => strval($val[8]),
            ]);
        }
    }
    
}

function search($pdo, $mot){
    $stringArr = [];
    if (strstr($mot, ",")){
        $stringArr = explode (",", $mot);
        foreach($stringArr as $key => $value){
            $stringArr[$key] = ltrim($stringArr[$key]);
        }
        return $stringArr;
    } else{
        array_push($stringArr, $mot);
        return $stringArr;
    }
    // $stmt = $pdo->prepare("SELECT * from actions");
    // $stmt->execute([]);
    // $dataArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // foreach($dataArr as $key => $val){
    //     $newString = explode(",", $dataArr[$key]["commentaires"]);
    //     foreach ($newString as $key => $value){
    //         $newString[$key] = ltrim($newString[$key]);
    //     }
    //     array_push($stringArr, $newString);
    //     var_dump($stringArr);
    // }
    // foreach($stringArr as $key => $value){
    //     foreach($value as $val){
    //         if ($mot === $val){
    //             array_push($aucArr, $dataArr[$key]["code"]);
    //         }
    //     }
    // }
    // return $aucArr;
}

?>
