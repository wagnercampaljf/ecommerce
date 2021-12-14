<b><?= $produto->produto->nome ?></b>
<br>
<br>
<?= $produto->produto->codigo_global ?>
<br>
<br>
<b> Especificações: </b><br>
Peso 			- <?= $produto->produto->peso ?><br>
Altura			- <?= $produto->produto->altura ?><br>
Largura			- <?= $produto->produto->largura ?><br>
Profundidade	- <?= $produto->produto->profundidade ?><br>
<br>
<b> Aplicação: </b>
<?= $produto->produto->aplicacao ?>
<?= $produto->produto->aplicacao_complementar ?>
<?php 
    if ($produto->produto->codigo_similar != ""){
        echo "<b>Códigos Similares:</b><br>".$produto->produto->codigo_similar;
    }
?>
<br>
<br>
<b> DICAS: </b>
<li> Lado Esquerdo é o do Motorista. </li>
<li> Lado Direito é o do Passageiro. </li>
<br>
<b> GARANTIA: </b>
Garantia de 3 meses e contra DEFEITOS DE FABRICAÇÃO.
