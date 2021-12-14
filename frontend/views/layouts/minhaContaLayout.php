<?php
use yii\helpers\Url;

$this->params['comprador'] = Yii::$app->user->getIdentity();
/* @var $this yii\web\View */
/* @var $content string */
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<div class="minhaconta-index container">

    <div class="tabs-minhaconta">
        <?php
        $pagina = $this->params['active'];
        if (!isset($pagina)) {
            $pagina = "home";
        }
        if (!isset($pageData)) {
            $data = '';
        }
        ?>

        <div class="sidebar-search col-md-3 col-sm-12">
            <div class="panel panel-primary  ">
                <div class="panel-heading">Minha Conta</div>

                <div class="panel-body">
                    <ul id="tabs" class="nav nav-tabs nav-tabs-view nav-stacked " data-tabs="tabs">
                        <li <?php if ($pagina == "home") {
                            echo 'class="active"';
                        } ?>><a href="<?= Url::to(['/minhaconta/index']) ?>">Painel
                                de Controle</a></li>
                        <li <?php if ($pagina == "dados") {
                            echo 'class="active"';
                        } ?>><a href="<?= Url::to(['/minhaconta/dados']) ?>">Meus dados</a>
                        </li>
                        <li <?php if ($pagina == "address") {
                            echo 'class="active"';
                        } ?>><a href="<?= Url::to(['/minhaconta/update-address']) ?>">Meu Endereço</a>
                        </li>
                        <li <?php if ($pagina == "pedidos" || $pagina == "pedido") {
                            echo 'class="active"';
                        } ?>><a href="<?= Url::to(['/minhaconta/pedidos']) ?>">Meus Pedidos</a>
                        </li>
                        <li <?php if ($pagina == "carrinhos") {
                            echo 'class="active"';
                        } ?>><a href="<?= Url::to(['/minhaconta/carrinhos']) ?>">Meus Carrinhos</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?= $content ?>
</div>
<?php $this->endContent(); ?>
