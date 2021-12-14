<?php 
    use common\models\ValorProdutoMenorMaior;
    use yii\helpers\Html;
    use console\controllers\actions\omie\Omie;
?>

<?php
    //echo "<pre>"; print_r($model); echo "</pre>"; 
?>

<br><br>ID: <?= $model["id"]?> nome: <?= $model["nome"]?> codigo_global: <?= $model["codigo_global"]?> codigo_fabricante: <?= $model["codigo_fabricante"]?> marca: <?= $model["marca"]?>

<table border>
	


<?php 

    $omie = new Omie(1, 1);
    
    $body = [
        "call" => "ConsultarProduto",
        "app_key" => '468080198586',
        "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
        "param" => [
            "codigo_produto_integracao" => "PA".$model["id"]
        ]
    ];
    $response_produto = $omie->consulta("/api/v1/geral/produtos/?JSON=",$body);
    //echo $response_produto["body"]["codigo_produto"]; die;
    
    if($response_produto["httpCode"] < 300){
    
        $body = [
            "call" => "MovimentoEstoque",
            "app_key" => '468080198586',
            "app_secret" => '7b3fb2b3bae35eca3b051b825b6d9f43',
            "param" => [
                "id_prod"       => $response_produto["body"]["codigo_produto"],
                "dataInicial"   => "01/01/2000",
                "dataFinal"     => "23/02/2021"
            ]
        ];
        $response = $omie->consulta("/api/v1/estoque/consulta/?JSON=",$body);
        
        //echo "<pre>"; print_r($response); echo "</pre>"; 

        if(!empty($response["body"]["movProduto"] )){
            echo "<tr><th>Data</th><th>Origem</th><th>Devolução</th><th>Tipo</th><th>Quantidade</th><th>Tipo</th><th>Quantidade</th><th>Tipo</th><th>Quantidade</th><th>Tipo</th><th>Quantidade</th></tr>";
        }
        
        
        foreach($response["body"]["movProduto"] as $movimentacao){
            //print_r($movimentacao);
            echo "<tr><td>".$movimentacao["dtMov"]."</td><td>".$movimentacao["desOrigem"]."</td><td>".$movimentacao["devolucao"]."</td><td>".$movimentacao["movPeriodo"][0]["tipo"].'</td><td style="text-align:right">'.$movimentacao["movPeriodo"][0]["qtde"]."</td><td>".$movimentacao["movPeriodo"][1]["tipo"].'</td><td style="text-align:right">'.$movimentacao["movPeriodo"][1]["qtde"]."</td><td>".$movimentacao["movPeriodo"][2]["tipo"].'</td><td style="text-align:right">'.$movimentacao["movPeriodo"][2]["qtde"]."</td><td>".$movimentacao["movPeriodo"][3]["tipo"].'</td><td style="text-align:right">'.$movimentacao["movPeriodo"][3]["qtde"]."</td><tr>";
        }
    }

?>

</table>

