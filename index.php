<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(empty($_GET['page']) or intval($_GET['page']) < 1){
    ?>
    <form>
        page <input type="number" name="page" />
        <input type="submit"/>
        <br/><br/><input type="number" name="skip" placeholder="skip"/>
    </form>
    <?php
    exit();
}
$csvFile = 'data.csv';
$dataPokemon = [];
// Open the file for reading
if (($handle = fopen($csvFile, 'r')) !== FALSE) {
    
    // Loop through each row in the CSV
    $fetching_page = 1;
    $fetching_card = 0;
    $skip = empty($_GET['skip']) ? 0 : intval($_GET['skip']);
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // $data is an array of the CSV values in the row
        // You can access each column by index, e.g., $data[0], $data[1], etc.
        // echo '<pre>';
        // print_r($data);  // Display the data
        // echo '</pre>';
        if($data[0] == "No.") continue;

        if(!empty($skip)){
            $skip--;
            continue;
        }

        $fetching_card++;
        if($fetching_card > 16){
            $fetching_card = 1;
            $fetching_page ++;
        }

        if($fetching_page != $_GET['page']) continue;

        $img = "";
        if(strpos($data[0],"_") !== false){
            $api1 = json_decode(file_get_contents("https://pokeapi.co/api/v2/pokemon-species/".((int)$data[0])));
            foreach($api1->varieties as $item){
                $explode = explode("/",$item->pokemon->url);
                if(substr($data[1],0,5) == "Mega "){
                    if(substr($data[1],-2) == " X" and substr($item->pokemon->name,-7) == "-mega-x"){
                        $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                        break;
                    }
                    elseif(substr($data[1],-2) == " Y" and substr($item->pokemon->name,-7) == "-mega-y"){
                        $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                        break;
                    }
                    elseif(substr($item->pokemon->name,-5) == "-mega"){
                        $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                        break;
                    }
                }
                elseif(substr($data[1],0,11) == "Gigantamax " and substr($item->pokemon->name,-5) == "-gmax"){
                    $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                    break;
                }
                elseif(substr($data[1],0,7) == "Alolan " and substr($item->pokemon->name,-6) == "-alola"){
                    $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                    break;
                }
                elseif(strpos($data[1],"(") !== false){
                    $explode_1 = explode("(",$data[1]);
                    $explode_2 = explode(")",$explode_1[1]);
                    $form = strtolower(preg_replace('/[^0-9a-zA-Z-]/',"",str_replace(" ","-",$explode_2[0])));
                    // if(substr($data[1],0,11) == "Gigantamax "){
                    //     die($data[1] . $form );
                    // }
                    if(substr($data[1],0,11) == "Gigantamax " and substr($item->pokemon->name,-strlen("-$form-gmax")) == "-$form-gmax"){
                        $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                        break;
                    }
                    elseif(substr($item->pokemon->name,-strlen($form)-1) == "-$form"){
                        $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.$explode[count($explode)-2].'.png';
                        break;
                    }
                }
            }
        }
        else{
            $img = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/'.((int)$data[0]).'.png';
        }
        $dataPokemon[] = [
            "id"=>$data[0],
            "name"=>$data[1],
            "type_of_pokemon"=>$data[7],
            "attack"=>$data[17],
            "defense"=>$data[18],
            "description"=>$data[11],
            "double_dmg"=>$data[8],
            "halved_dmg"=>$data[9],
            "cost"=>$data[14],
            "img"=>$img,
            "from"=>$data[4],
        ];
        // if(count($dataPokemon) == 16) break;
    }

    // Close the file
    fclose($handle);
} else {
    echo "Error: Unable to open the file.";
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pokemon - Page <?=$_GET['page']?></title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
            }
        }

        body {
            font-family: Arial, sans-serif;
        }

        .page {
            width: 210mm;
            height: 297mm;
            margin: auto;
            page-break-after: always;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 2px;
            padding: 10mm;
            box-sizing: border-box;
        }

        .card {
            border: 2px solid gray;
            border-radius: 10px;
            padding-top: 5px;
            padding-left: 5px;
            padding-right: 5px;
            padding-bottom: 0;
            box-sizing: border-box;
            text-align: center;
            font-size: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-position: center; /* Pusatkan gambar */
            background-repeat: no-repeat; /* Hindari pengulangan gambar */
            color: black; /* Warna teks utama */
            text-shadow: 
                -0.4px -0.4px 0 #FFFFFF, /* Bayangan ke kiri atas */
                0.4px -0.4px 0 #FFFFFF,  /* Bayangan ke kanan atas */
                -0.4px 0.4px 0 #FFFFFF,  /* Bayangan ke kiri bawah */
                0.4px 0.4px 0 #FFFFFF;   /* Bayangan ke kanan bawah */
        }

        .card h3 {
            margin: 0;
            font-size: 14px;
        }

        .card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<div id="cards-container"></div>
<?php
$json_decode = json_encode($dataPokemon);
if(!$json_decode){
    echo "ERROR<pre>";
    print_r($dataPokemon);
    die();
}
?>
<script>
    const cardsData = <?=$json_decode?>;
    function createCardElement(card) {
        const cardDiv = document.createElement('div');
        cardDiv.className = 'card';
        cardDiv.innerHTML = `
            <table>
                <tr>
                    <td width="40%" style="text-align: left"><strong>ATK:</strong>${card.attack}</td>
                    <td width="20%" style="text-align: center; font-weight: bold" colspan="2">[${card.cost}]</td>
                    <td width="40%" style="text-align: right"><strong>DEF:</strong>${card.defense}</td>
                </tr>
                <tr style="vertical-align: top;">
                    <td width="50%" style="text-align: left" colspan="2"><strong style="font-size: 9px;">2*DMG:</strong><br/><small style="color: darkblue">${card.double_dmg}</small></td>
                    <td width="50%" style="text-align: right" colspan="2"><strong style="font-size: 9px;">0.5*DMG:</strong><br/><small style="color: darkblue">${card.halved_dmg}</small></td>
                </tr>
            </table>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>${(card.from == "-" ? "" : " <small style='color:red; float: left'>"+card.from+"-&gt;<br></small>")} <strong style="font-size: 13px">${card.name}</strong> <span style="color: darkblue">[${card.type_of_pokemon}]</span><br/><small style="font-style: italic; font-size: 9px;">${card.description}</small></p>
        `;
        cardDiv.style = `background-image: url('${card.img}'); background-size: ${30+10*card.cost}%;`
        return cardDiv;
    }

    function loadCards() {
        const container = document.getElementById('cards-container');
        let pageDiv = null;

        cardsData.forEach((card, index) => {
            if (index % 16 === 0) {
                // Create a new page every 16 cards
                pageDiv = document.createElement('div');
                pageDiv.className = 'page';
                container.appendChild(pageDiv);
            }

            const cardElement = createCardElement(card);
            pageDiv.appendChild(cardElement);
        });
    }

    window.onload = loadCards;
</script>

</body>
</html>
