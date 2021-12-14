<p>PEÇA AGORA, O SHOPPING ONLINE DO SEU VEÍCULO</p>
<p>
    ATENÇÃO!!! Prezado Cliente, caso ainda tenha dúvidas de que este é o produto correto para você, ANTES DE COMPRAR nos informe o chassi do seu veículo nas perguntas para
    termos certeza que esta é a peça certa. Dessa forma evitamos transtornos de trocas e devoluções.
</p>
<BR>
<?= $produto->produto->nome ?>
<br>
<?= $produto->produto->codigo_global ?>
<br>
<br>
APLICAÇÃO:
<BR>
<?= $produto->produto->aplicacao ?>
<br>
<?= $produto->produto->aplicacao_complementar ?>
<br>
<?php
if (!empty($produto->produto->subcategoria->descricao)) { ?>
    <BR>DESCRIÇÃO:<BR>
    <?= $produto->produto->subcategoria->descricao . "<BR>";
} ?>
<br>
CÓDIGOS SIMILARES:
<br>
<?= $produto->produto->codigo_similar ?>
<br>
<br>
DICAS:
<br>
<br>
* Lado Esquerdo é o do Motorista.<br>
* Lado Direito é o do Passageiro.
<br>
<br>
GARANTIA: Garantia de DEFEITOS DE FABRICAÇÃO.
<br>
<br>
SOBRE O PEÇA AGORA
<br>
<p>
    Saiba onde encontrar acessórios, peças e serviços automotivos:
    Na Distribuidora Peça Agora você encontra as melhores autopeças para
    o seu veículo, sendo ele leve (carro e moto) ou pesado (caminhão, ônibus, van,
    e máquinas). Nossa loja possui os melhores preços, e é focada em prover a você
    o melhor atendimento sempre.
</p><p>
    Trabalhamos com peças originais e importadas, todas com garantia e para
    isso, contamos com o apoio dos melhores fornecedores do país e do mundo.
</p><p>
    Tem dúvidas sobre como achar os nosso produtos?
    Simples! Eles sempre estarão cadastrados por código e por nome da peça que
    deseja comprar. Mas, se mesmo assim não tiver encontrado o produto desejado
    ou estiver com alguma dúvida, faça-nos uma pergunta. Será um prazer lhe
    atender.
</p>
<P>Grande Abraço, Equipe Peça Agora</P>
