<p><b><?= $produto->produto->nome ?></b></p>
<br>
<p><b>Código Global:</b></p>
<p><?= $produto->produto->codigo_global ?></p>
<br>
<p><b> Aplicação: </b></p>
<?= $produto->produto->aplicacao ?>
<?= $produto->produto->aplicacao_complementar ?>
<br>
<?php 
    if ($produto->produto->codigo_similar != ""){
        echo "<p><b>Códigos Similares:</b></p><p>".$produto->produto->codigo_similar."</p><br>";
    }
?>
<p><b> Dicas: </b></p> 
<li> Lado Esquerdo é o do Motorista. </li>
<li> Lado Direito é o do Passageiro. </li>
<br>
<p><b> Garantia: </b></p>
<p>Garantia de 3 meses e contra Defeitos De FABRICAÇÃO.</p>
