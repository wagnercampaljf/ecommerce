<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 23/10/2015
 * Time: 16:34
 */
use common\models\Caracteristica;
use common\models\Categoria;
use common\models\Cidade;
use common\models\Estado;
use common\models\Fabricante;
use common\models\Filial;
use common\models\Marca;
use common\models\Modelo;
use common\models\Subcategoria;
use common\models\ValorProdutoFilial;
use frontend\widgets\TagsSearch;
use kartik\select2\Select2;
use kartik\slider\Slider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

?>
<div class="sidebar-search  col-xs-12 col-sm-4 col-md-3 col-lg-3">
    <div class="">
        <div class="loading hide">
            <i class="fa fa-spinner fa-spin fa-4x"></i>
        </div>
        <a class="title-categoria-label" data-toggle="collapse" rel="nofollow" href=".categorias">
            <div class="title-categoria clearfix ">
                FILTRAR POR
                <div class="categorias collapse in pull-right" style="transition: none;margin-right: 4px;">
                    <i class="fa fa-chevron-right"></i>
                </div>
                <div class="categorias collapse pull-right" style="transition: none;">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="collapse categorias clearfix">
            <div class="list-group">
                <?php
                $href = Yii::$app->request->get();
                ArrayHelper::remove($href, '_pjax');
                if ($href) {
                    echo Html::a(
                            'Limpar todos os filtros',
                            Url::to(Url::to($subCategoria->slug)),
                            [
                                'class' => 'btn btn-warning btn-xs col-xs-12 col-sm-12 col-md-12 col-lg-12',
                                'onclick' => '$("#main-search-product").val(null);'
                            ]
                        ) . '<hr>';
                }
                $href = [''] + Yii::$app->request->get();
                ?>
                <?= TagsSearch::widget([
                    'params' => ArrayHelper::merge(['_pjax' => '#filters'], Yii::$app->request->get()),
                    'attributes' => [
                        'categoria_id' => Categoria::className(),
                        'subcategoria_id' => Subcategoria::className(),
                        'filial_id' => Filial::className(),
                        'fabricante_id' => Fabricante::className(),
                        'estado_id' => Estado::className(),
                        'cidade_id' => Cidade::className(),
                        'marca_id' => Marca::className(),
                        'modelo_id' => Modelo::className(),
                        'caracteristica_id' => Caracteristica::className(),
                    ]
                ]); ?>
            </div>
            <div class="input-group attr-search">
                <span class="input-group-addon">Marca: </span>
                <?php
                echo Select2::widget([
                    'name' => 'marca_id',
                    'value' => Yii::$app->request->get('marca_id'),
                    'data' => ArrayHelper::map(
                        Marca::find()->orderBy(['marca.nome' => SORT_ASC])->all(),
                        'id',
                        'nome'
                    ),
                    'options' => ['prompt' => 'Escolha uma Marca'],
                    'pluginEvents' => [
                        "select2:select" => "function() {
                                $('#main-search-product').val($(this).children('option:checked').html());
                                $('#marca_id').attr('href', $.urlParamChange('marca_id', $(this).val()));
                                $('#marca_id').trigger('click');
                            }",
                    ]
                ]);
                //                var_dump( $href);
                //                die;
                echo Html::a('', $href, ['id' => 'marca_id'])
                ?>
            </div>
            <div class="input-group attr-search">
                <span class="input-group-addon">Modelo: </span>
                <?php
                echo Select2::widget([
                    'name' => 'modelo_id',
                    'options' => ['prompt' => 'Escolha uma Modelo'],
                    'value' => Yii::$app->request->get('modelo_id'),
                    'data' => ArrayHelper::map(
                        Modelo::find()->andWhere([
                            'marca_id' => Yii::$app->request->get('marca_id')
                        ])->orderBy(['modelo.nome' => SORT_ASC])->all(),
                        'id',
                        'nome'
                    ),
                    'pluginEvents' => [
                        "select2:select" => "function() {
                                $('#main-search-product').val($('#main-search-product').val() +' '+ $(this).children('option:checked').html());
                                $('#modelo_id').attr('href', $.urlParamChange('modelo_id', $(this).val()));
                                $('#modelo_id').trigger('click');
                            }",
                    ]
                ]);
                echo Html::a('', $href, ['id' => 'modelo_id']);
                ?>
            </div>
            <?php
            $categoria_id = Yii::$app->request->get('categoria_id');
            $subcategoria_id = Yii::$app->request->get('subcategoria_id');
            ?>
            <div class="list-attrs-search">
                <div class="input-group attr-search">
                    <span class="input-group-addon">Loja: </span>
                    <?php
                    $loja = Yii::$app->request->get('filial_id') ? Filial::findOne(Yii::$app->request->get('filial_id'))->nome : null;
                    echo Select2::widget([
                        'name' => 'filial_id',
                        'initValueText' => $loja,
                        'value' => Yii::$app->request->get('filial_id'),
                        'options' => [
                            'placeholder' => 'Escolha uma Loja',
                            'id' => 'select_filial',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['search/get-filial']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'pluginEvents' => [
                            "select2:select" => 'function(){
                                $(\'#filial_id\').attr(\'href\', $.urlParamChange(\'filial_id\',$(this).val()));
                                $(\'#filial_id\').trigger(\'click\');
                            }'
                        ]
                    ]);
                    echo Html::a('', $href, ['id' => 'filial_id']);
                    ?>
                </div>
                <div class="input-group attr-search">
                    <span class="input-group-addon">Fabricante: </span>
                    <?php
                    $fabricante = Yii::$app->request->get('fabricante_id') ? Fabricante::findOne(Yii::$app->request->get('fabricante_id'))->nome : null;
                    echo Select2::widget([
                        'name' => 'fabricante_id',
                        'initValueText' => $fabricante,
                        'value' => Yii::$app->request->get('fabricante_id'),
                        'options' => [
                            'placeholder' => 'Escolha um Fabricante',
                            'id' => 'select_fabricante',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['search/get-fabricante']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'pluginEvents' => [
                            "select2:select" => 'function(){
                                $(\'#fabricante_id\').attr(\'href\', $.urlParamChange(\'fabricante_id\',$(this).val()));
                                $(\'#fabricante_id\').trigger(\'click\');
                            }'
                        ]
                    ]);
                    echo Html::a('', $href, ['id' => 'fabricante_id']);
                    ?>
                </div>
                <div class="input-group attr-search">
                    <span class="input-group-addon">Estado: </span>
                    <?php
                    $estado = Yii::$app->request->get('estado_id') ? Estado::findOne(Yii::$app->request->get('estado_id'))->nome : null;
                    echo Select2::widget([
                        'name' => 'estado_id',
                        'initValueText' => $estado,
                        'value' => Yii::$app->request->get('estado_id'),
                        'data' => ArrayHelper::map(Estado::find()->all(), 'id', 'nome'),
                        'options' => [
                            'placeholder' => 'Escolha a Estado',
                            'id' => 'select_estado',
                        ],
                        'pluginEvents' => [
                            "select2:select" => 'function(){
                                $(\'#cidade_id\').attr(\'href\', $.urlParamChange(\'estado_id\',$(this).val()));
                                $(\'#cidade_id\').trigger(\'click\');
                            }'
                        ]
                    ]);
                    echo Html::a('', $href, ['id' => 'estado_id']);
                    ?>
                </div>
                <div class="input-group attr-search">
                    <span class="input-group-addon">Cidade: </span>
                    <?php
                    $cidade = Yii::$app->request->get('cidade_id') ? Cidade::findOne(Yii::$app->request->get('cidade_id'))->nome : null;
                    echo Select2::widget([
                        'name' => 'cidade_id',
                        'initValueText' => $cidade,
                        'value' => Yii::$app->request->get('cidade_id'),
                        'options' => [
                            'placeholder' => 'Escolha a Cidade',
                            'id' => 'select_cidade',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to(['search/get-cidade']),
                                'dataType' => 'json',
                                'data' => new JsExpression('
                                function(params) {
                                    return {
                                        q:params.term,
                                        estado:' . Yii::$app->request->get('estado_id', 'null') . '
                                    };
                                }')
                            ],
                        ],
                        'pluginEvents' => [
                            "select2:select" => 'function(){
                                $(\'#cidade_id\').attr(\'href\', $.urlParamChange(\'cidade_id\',$(this).val()));
                                $(\'#cidade_id\').trigger(\'click\');
                            }'
                        ]
                    ]);
                    echo Html::a('', $href, ['id' => 'cidade_id']);
                    ?>
                </div>

                <div class="attr-search">
                    <div class="input-group-addon col-xs-12 col-sm-12 col-md-12 col-lg-12"
                         style=" border-radius: 4px;border-right: 1px solid #cccccc">Preço:
                    </div>
                    <br>

                    <div class="input-group-addon col-xs-12 col-sm-12 col-md-12 col-lg-12"
                         style=" border-radius: 4px;border-left: 1px solid #cccccc">
                        <?php
                        $tipo_value = (Yii::$app->params['isJuridica']() ? 'valor_cnpj' : 'valor');
                        $value_max = ValorProdutoFilial::find()->select([$tipo_value])->byCategoria($categoria_id)->bySubcategoria($subcategoria_id)->orderBy([
                            ('valor_produto_filial.' . $tipo_value) => SORT_DESC
                        ])->createCommand()->queryScalar();
                        $value_min = ValorProdutoFilial::find()->select([$tipo_value])->byCategoria($categoria_id)->bySubcategoria($subcategoria_id)->orderBy([
                            ('valor_produto_filial.' . $tipo_value) => SORT_ASC
                        ])->createCommand()->queryScalar();
                        echo Slider::widget([
                            'name' => 'preco',
                            'value' => Yii::$app->request->get('preco'),
                            'sliderColor' => Slider::TYPE_PRIMARY,
                            'handleColor' => Slider::TYPE_GREY,
                            'options' => ['class' => 'form-control '],
                            'pluginOptions' => [
                                'min' => !$value_min ? 0 : (int)$value_min,
                                'max' => !$value_max ? 10 : (int)$value_max,
                                'step' => 5,
                                'range' => true,
                                'handle' => 'square',
                            ],
                            'pluginEvents' => [
                                'slideStop' => 'function (data){
                                $(\'#preco\').attr(\'href\', $.urlParamChange(\'preco\',data.value));
                                $(\'#preco\').trigger(\'click\');
                            }'
                            ]
                        ]);
                        echo Html::a('', $href, ['id' => 'preco'])
                        ?>
                    </div>
                    <div class="">
                        <small class="pull-left">Preço Mínimo</small>
                        <small class="pull-right">Preço Máximo</small>
                    </div>
                </div>
                <br>
                <div class="input-group attr-search">
                    <span class="input-group-addon">Entrega Grátis: </span>
                    <?php
                    echo Select2::widget([
                        'name' => 'entrega_propria',
                        'value' => Yii::$app->request->get('caracteristica_id'),
                        'options' => ['prompt' => ''],
                        'data' => [
                            Caracteristica::ENTREGA_PROPRIA => 'Sim',
                            0 => 'Não'
                        ],
                        'pluginEvents' => [
                            "select2:select" => "function() {
                                var val = ($(this).val() > 0) ? $(this).val() : '';
                                $('#caracteristica_id').attr('href', $.urlParamChange('caracteristica_id', val));
                                $('#caracteristica_id').trigger('click');
                            }",
                        ]
                    ]);
                    echo Html::a('', $href, ['id' => 'caracteristica_id']);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (($subcategoria = Subcategoria::findOne($subcategoria_id)) && (count($subcategoria->subcategoriaDocumentoReferencias) > 0)):
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">Catalogos:</div>
            <div class="panel-body">
                <ul class="list-group list-catalogos-search">
                    <?php
                    foreach ($subcategoria->subcategoriaDocumentoReferencias as $subcategoriaDocumentoReferencia) {
                        $src = $subcategoriaDocumentoReferencia->documentoReferencia->href;
                        echo Html::tag(
                            'span',
                            $subcategoriaDocumentoReferencia->documentoReferencia->label,
                            [
                                'class' => 'link list-group-item',
                                'onclick' => 'window.open("' . $src . '", "_blank")',
                            ]
                        );
                    }
                    ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

