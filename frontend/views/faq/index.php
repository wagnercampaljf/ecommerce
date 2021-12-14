<?php

/* @var $this yii\web\View */
$this->title = 'FAQ';
$this->params['breadcrumbs'][] = $this->title;
/**
 * função para recursivamente montar as categorias e itens do faq em accordions
 * @link http://getbootstrap.com/javascript/#collapse
 * @param $c CategoriaFaq|ItemFaq Model a ser renderizado como accordion
 * @param $id string uniqid() identificador de cada elemento. Não pode ser o id porque, entre Categorias e itens, este se repete.
 * @param string $icon string sufixo do ícone FontAwesome para ser utilizado no header do elemento
 */
function drawAccordion($c, $id, $icon = '')
{
    if ($c != null) {
        ?>
        <div id="<?= $id ?>" class="faq-wrapper">
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading-<?= $id ?>">
                    <a data-toggle="collapse" data-parent="#faq-accordion" href="#faq-<?= $id ?>"
                       aria-expanded="false"
                       aria-controls="faq-<?= $id ?>" class="collapsed">
                        <h4 class="panel-title">
                            <i class="fa fa-<?= $icon ?>"></i>
                            <?= $c->nome; ?>
                        </h4>
                    </a>

                </div>
                <div id="faq-<?= $id ?>" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="heading-<?= $id ?>"
                     aria-expanded="false" style="height: 0px;">
                    <div class="panel-body">
                        <?php
                        //na hora de mostrar o conteúdo, se $c for uma categoria,
                        //renderizar recursivamente o conjunto de itens pertencentes a mesma.
                        //senão, se $c já for um item, imprimir o conteúdo do mesmo.
                        if (method_exists($c, 'getItensFaq')) {
                            foreach ($c->itensFaq as $i) {
                                drawAccordion($i, uniqid(), 'question');
                            }
                        } else {
                            echo $c->descricao;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

?>
<div class="faq-index">
    <h3>FAQ - Perguntas Frequentes</h3>

    <div class="faq">
        <div class="panel-group" id="faq-accordion" role="tablist" aria-multiselectable="true">
            <?php
            //para cada categoria
            $categorias = \common\models\CategoriaFaq::find()->ordemAlfabetica()->publica()->with('itensFaq')->all();
            foreach ($categorias as $c) {
                drawAccordion($c, uniqid(), 'th-list');
            } ?>

        </div>
    </div>
</div>