<?php

use app\models\Newsletter;
use common\models\CategoriaModelo;
use common\models\Fabricante;
use common\models\Filial;
use common\models\Marca;
use common\models\Modelo;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ProdutoSearch;
use common\models\ValorProdutoFilial;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\models\Categoria;
use frontend\widgets\FormSearch;
use yii\db\Query;
use Marketplacehub\Skyhub\ApiClient as Skyhub;

/* @var $this yii\web\View */
$this->title = 'Sobre o Peça Agora';
$this->params['breadcrumbs'][] = $this->title;
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

?>
<style type="text/css">

    p {
        text-align: justify;
        text-indent: 50px;
        font-size: 16px;
    }

    h2 {
        margin-bottom: 25px;
    }

</style>
<div class="container col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <h2><?= Html::encode($this->title) ?></h2>

    <iframe width="100%" height="300" src="https://www.youtube.com/embed/R0QTbXFfH8k?rel=0" frameborder="0"
            allowfullscreen></iframe>
    <br>
    <br>
    <br>
    <h3>Nosso principal objetivo é reduzir os custos das compras de peças </h3>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Com a experiência dos sócios em projetos de gestão e otimização
        de frotas,
        percebeu-se que os processos de orçamento e compra de peças eram ineficientes,
        pois as formas utilizadas para fazer as compras como telefones e e-mails
        eram lentas e dificultavam a busca de informações dos melhores produtos,
        preços e prazos. Essa situação resultava na demora das entregas e em erros
        na escolha das mercadorias. Tudo isso aumentava o custo da manutenção e
        impactava negativamente os clientes.
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">A partir desse problema foi idealizado o Peça Agora, um
        shopping online
        que reúne as melhores lojas, os melhores preços e os melhores prazos para
        promover maior interação entre compradores e vendedores e facilitar as
        compras. Pensado cuidadosamente para gerar a facilidade na obtenção das
        informações dos produtos, o Peça Agora conta com filtros de busca que
        permitem que o cliente encontre o produto desejado de forma mais rápida
        e um comparador de preços, para que o cliente possa escolher e realizar a melhor compra.
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">O Peça Agora é desenvolvido pela OPT Soluções, empresa que
        realiza projetos de gestão
        e otimização de frotas, visando desenvolver tecnologias e soluções inovadoras no ramo de
        frotas. Atuando no ramo desde 2013, a empresa está atualmente situada no CRITT (Centro Regional
        de Inovação e Transferência de Tecnologia) na Universidade Federal de Juiz de Fora (UFJF) em
        Minas
        Gerais.
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Desde sua concepção, o Peça Agora já ganhou diversos prêmios e
        foi finalista de vários concursos de Inovação.
        Pode-se destacar:
    </div>
    <ul class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <li>Ganhador do SEBRAETEC do SEBRAE-MG edição 2014;</li>
        <li>Ganhador do Programa Promessas Endeavor;</li>
        <li>Ganhador do RETEC edição 2014;</li>
        <li>Finalista do Desafio Brasil Edição 2014;</li>
        <li>Finalista do Feincitec do Crea MG, edição 2014;</li>
        <li>Finalista contemplado com o terceiro lugar no concurso de Produtos e processos inovadores do IFSudeste MG.
        </li>
    </ul>
    <hr>
    <h2> Equipe</h2>
    <h4> Somos apaixonados pelo que fazemos </h4>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"> A nossa equipe é composta por profissionais qualificados no
        ramo de frotas e comprometidos com a nossa
        principal missão: promover a agilidade na compra de peças .
        Nós acreditamos que a inovação deve ser contínua e trabalhamos para desenvolver tecnologias e soluções
        inovadoras no ramo de frotas para que possamos atender cada vez melhor a necessidade dos nossos clientes .
    </div>
    <hr>
    <h2> Missão, visão e valores </h2>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h4>Missão:</h4>  Atender de forma assertiva nosso clientes e solucionar os problemas da linha Diesel.
    </div><br>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h4>Visão:</h4> Ser referência de Mercado em Autopeças Diesel até 2021.
    </div><br>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h4>Valores:</h4>
        <ul>
            <li> Transparência</li>
            <li> Ética</li>
            <li> Inovação</li>
            <li> Assertividade</li>
            <li> Confiança</li>
        </ul>
    </div><br><br>

    <h4> Acreditamos que as parcerias são a base de qualquer projeto </h4>
    <i> Veja alguns de nossos parceiros:</i><br>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 panel panel-default clearfix">


        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem1.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem2.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem3.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem4.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem5.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem6.png') ?>" width="100%"></div>
        <div class="col-xs-4 col-sm-4 col-md-3 col-lg-6"><img
                    src="<?= Url::to('@assets/img/parceiros/Imagem7.png') ?>" width="100%"></div>

    </div>
</div>

