<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 20/05/2015
 * Time: 15:15
 */
use yii\helpers\Url;

$this->title = 'Introdução';
?>
<div class="panel  panel-success">
    <div class="panel-heading ">
        <h2 class="panel-title">Para cadastrar sua loja no Peça Agora é necessário seguir alguns passos!</h2>
    </div>
    <div class="panel-body">
        <div class="row">

            <div class="col-sm-6 col-md-3">
                <div style="min-height:325px; padding-top: 30px; background-color: #f0f0f0"
                     class="thumbnail text-center">

                    <span style="color:#007576;font-size: 75px;" class="fa fa-edit"></span>

                    <div class="caption">
                        <h3>1. Cadastro no Moip</h3>

                        <p style="text-align: justify; text-indent: 30px">Moip é a nossa plataforma financeira online,
                            onde serão feitos os pagamentos das compras no
                            site. Por isso, você vai precisar de uma <a target="_blank"
                                                                        href="https://www.moip.com.br/criarcarteira.do">conta
                                de Vendedor Moip. </a></p>
                        <!--                        <p><a href="#" class="btn btn-default" role="button">Cadastrar no Moip</a></p>-->
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div style="min-height:325px; padding-top: 30px; background-color: #f0f0f0"
                     class="thumbnail text-center">

                    <span style="color:#007576;font-size: 75px;" class="fa fa-key"></span>

                    <div class="caption">
                        <h3>2. Autorizar </h3>

                        <p style="text-align: justify; text-indent: 30px">O Peça Agora precisa da sua autorização para
                            criar pedidos e enviar pagamentos para sua conta
                            Moip. Vamos pedir sua permissão durante o processo.</p>

                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div style="min-height:325px; padding-top: 30px; background-color: #f0f0f0"
                     class="thumbnail text-center">

                    <span style="color:#007576;font-size: 75px;" class="fa fa-floppy-o"></span>

                    <div class="caption">
                        <h3>3. Registrar</h3>

                        <p style="text-align: justify; text-indent: 30px">
                            Estamos quase lá! Precisamos de mais algumas informações para concluir sua conta no Peça
                            Agora.
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div style="min-height:325px; padding-top: 30px; background-color: #f0f0f0"
                     class="thumbnail text-center">

                    <span style="color:#007576;font-size: 75px;" class=" fa fa-thumbs-up"></span>

                    <div class="caption">
                        <h3>4. Aprovação</h3>

                        <p style="text-align: justify; text-indent: 30px"> Pronto! Temos tudo o que precisamos por
                            enquanto. Vamos analisar seu cadastro e entrar em
                            contato em até 72h.</p>

                    </div>
                </div>
            </div>

            <p>
                <a href="<?= Url::to(['authorize-moip']) ?>" style="margin:20px 10px 0 0; float: right"
                   class="btn btn-primary btn-lg" role="button">
                    Vamos lá!
                </a>
            </p>

            <p style="font-size:16px;margin:35px 10px 0 0;float: right">Já tenho cadastro na plataforma<b> Moip</b></p>
        </div>
    </div>
</div>