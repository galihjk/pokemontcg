<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pokemon - Back</title>
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
        .card-container {
            
        }

        .card {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card h3 {
            margin: 0;
            font-size: 14px;
        }

        .card p {
            margin: 5px 0;
        }
        
        .pokeball {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(to bottom, #ff0000 50%, #fff 50%);
            border: 4px solid black;
            overflow: hidden;
        }

        .pokeball::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: black;
            transform: translateY(-50%);
        }

        .pokeball::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            background-color: white;
            border: 5px solid black;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
    
    </style>
</head>
<body>

<div id="cards-container"></div>
<?php
$dataPokemon = [];
for ($i=0; $i < 16; $i++) { 
    $dataPokemon[] = $i;
}
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
        const cardDivContainer = document.createElement('div');
        cardDivContainer.className = 'card-container';
        const cardDiv = document.createElement('div');
        cardDiv.className = 'card';
        cardDiv.innerHTML = `<div class="pokeball"></div>`;
        cardDivContainer.appendChild(cardDiv);
        return cardDivContainer;
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