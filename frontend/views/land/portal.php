<?php
/**
 * Created by PhpStorm.
 * User: Otávio
 * Date: 27/04/2016
 * Time: 18:15
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = "Portal das Oficinas p" . $this->title;
$this->registerMetaTag(['name' => 'description', 'content' => 'O melhor preço em autopeças para carros, motos, caminhões, ônibus e tratores. Confira nossas ofertas e compre online! Entregamos em todo o Brasil!']);

$this->registerJs('
    wow = new WOW(
          {
            animateClass: \'animated\',
            offset:       100,
            callback:     function(box) {
              console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
            }
          }
        );
    wow.init();
    ');

$this->registerJs("
$('#comeceAgora').click(function(){
    $('html, body').animate({
        scrollTop: $( $(this).attr('href') ).offset().top
    }, 1300);
    return false;
});
");
?>

<div class="bannerLandPortal row ">
    <a href="http://www.pecaagora.com/portaldasoficinas"><img src="<?= Url::to('@assets/'); ?>img/land/landportal.jpg"
                                                              class="imageLandPortal"/></a>
</div>

<div class="row text-center chamadaPortal">
    <h3>Faça parte da inovação e venda seus serviços na Internet. É fácil e rápido!</h3>
    <a id="comeceAgora" href="#entreContato" rel="nofollow" class="btn btn-primary btn-lg">COMECE AGORA!</a>
</div>
<div class="row">
    <div class="landPartLeft wow animated fadeInLeft col-xs-12 col-sm-12 col-md-7 col-lg-5" data-wow-delay="200ms">
        <div class="imagem col-xs-12 col-sm-2 col-md-3 col-lg-2">
            <div class="img"><i class="fa fa-bullhorn fa-5x" aria-hidden="true"></i></div>
        </div>
        <div class="texto col-xs-12 col-sm-10 col-md-9 col-lg-10">
            <h2>Divulgue</h2>
            <p>Agora você pode aumentar a procura por sua Oficina, oferecendo seus serviços na Internet.</p>
        </div>

    </div>
    <div class="visible-lg col-lg-2">
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInDown;"></div>
        <div class="border-left wow zoomInRight animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInRight;"></div>
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible;animation-duration: 2s;animation-delay: 200ms;animation-name: fadeInDown;"></div>
    </div>
</div>
<div class="row">
    <div class="visible-lg col-lg-2 col-md-offset-5 col-lg-offset-5">
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInDown;"></div>
        <div class="border-right wow zoomInRight animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInRight;"></div>
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible;animation-duration: 2s;animation-delay: 200ms;animation-name: fadeInDown;"></div>
    </div>
    <div
        class="landPartRight wow animated fadeInRight col-xs-12 col-sm-12 col-md-7 col-lg-5 col-md-offset-5 col-lg-offset-0"
        data-wow-delay="200ms">
        <div class="imagem col-xs-12 col-sm-2 col-md-3 col-lg-2">
            <div class="img"><i class="fa fa-pencil-square-o fa-5x" aria-hidden="true"></i></div>
        </div>
        <div class="texto col-xs-12 col-sm-10 col-md-9 col-lg-10">
            <h2>Personalize</h2>
            <p>Tenha uma página só para sua oficina e coloque nela as informações que quiser.</p>
        </div>

    </div>
</div>
<div class="row">
    <div class="landPartLeft wow animated fadeInLeft col-xs-12 col-sm-12 col-md-7 col-lg-5" data-wow-delay="200ms">
        <div class="imagem col-xs-12 col-sm-2 col-md-3 col-lg-2">
            <div class="img"><i class="fa fa-line-chart  fa-5x" aria-hidden="true"></i></div>
        </div>
        <div class="texto col-xs-12 col-sm-10 col-md-9 col-lg-10">
            <h2>Melhore seus Resultados</h2>
            <p>Aumente seu rendimento recebendo cada vez mais clientes em sua oficina.</p>
        </div>
    </div>
    <div class=" visible-lg col-lg-2">
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInDown;"></div>
        <div class="border-left wow zoomInRight animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInRight;"></div>
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible;animation-duration: 2s;animation-delay: 200ms;animation-name: fadeInDown;"></div>
    </div>
</div>
<div class="row">
    <div class="visible-lg col-lg-2 col-md-offset-5 col-lg-offset-5">
        <div class="border-middle wow fadeInDown animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInDown;"></div>
        <div class="border-right wow zoomInRight animated" data-wow-duration="2s" data-wow-delay="200ms"
             style="visibility: visible; animation-duration: 2s; animation-delay: 200ms; animation-name: fadeInRight;"></div>
    </div>
    <div
        class="landPartRight wow animated fadeInRight col-xs-12 col-sm-12 col-md-7 col-lg-5 col-md-offset-5 col-lg-offset-0"
        data-wow-delay="200ms">
        <div class="imagem col-xs-12 col-sm-2 col-md-3 col-lg-2">
            <div class="img"><i class="fa fa-map-marker fa-5x" aria-hidden="true"></i></div>
        </div>
        <div class="texto col-xs-12 col-sm-10 col-md-9 col-lg-10">
            <h2>Seja Encontrado</h2>
            <p>Com o filtro por localidade você será encontrado por clientes que estão na sua região.</p>
        </div>

    </div>
</div>
</div>
<div class="row text-center segChamadaPortal">
    <h3>Algumas Oficinas Participantes</h3>
    <div class="row lojasPortal col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
            <img class="lojaPortal" src="<?= Url::to('@assets/'); ?>img/land/lojas/loja1.png" width="100%"/>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2  col-sm-offset-1 col-md-offset-1 col-lg-offset-1">
            <img class="lojaPortal" src="<?= Url::to('@assets/'); ?>img/land/lojas/loja2.png" width="100%"/>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2  col-sm-offset-1 col-md-offset-1 col-lg-offset-1">
            <img class="lojaPortal" src="<?= Url::to('@assets/'); ?>img/land/lojas/loja3.png" width="100%"/>
        </div>
    </div>
    <h3>Comece hoje mesmo a vender seus serviços na Internet!</h3>
    <h3>Fale com nossa equipe de vendas e tire suas dúvidas.</h3>
</div>
<div class="container">
    <div class="row">
        <div class="tabelaPortal clearfix  col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class=" col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div
                    class="colunaEsquerda sombra col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <h2 class="text-center">Veja suas Vantagens</h2>
                    <hr>
                    <ul style="">
                        <li><i class="fa fa-check" aria-hidden="true"></i> Suporte de Tele Atendimento</li>
                        <br>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Conteúdo e Marketing Digital</li>
                        <br>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Visibilidade nas Buscas Orgânicas do Google
                            (SEO)
                        </li>
                        <br>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Personalização da Oficina Virtual</li>
                        <br>
                        <li><i class="fa fa-check" aria-hidden="true"></i> Visibilidade e Mídia Online</li>
                        <br>
                    </ul>
                </div>
                <div
                    class="visible-xs colunaDireita sombra col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <h2 class="text-center">Nossos Planos</h2>
                    <hr>
                    <div class="text-left">
                        <h4>Plano Mensal</h4>
                        <p>R$235 / mês </p>
                        <h4>Plano Anual</h4>
                        <p>R$115 / mês (Pague R$1380 /ano para oferecer seus serviços online, se pagar a vista temos
                            descontos incriveis)
                        </p>
                        <h4>Plano Coletivo</h4>
                        <p> Reúna um grupo de oficinas parceiras, peçam um Pacote Coletivo, juntos ganharão mais
                            visibilidade e descontos até 90% por mês) </p>
                    </div>
                </div>
                <div id="entreContato"
                     class=" colunaDireita sombra col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <h2 class="text-center">Entre em Contato</h2>
                    <hr>

                    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

                        <div class="alert alert-success">
                            Obrigado por fazer a solicitação no Peça Agora. Responderemos o mais rápido possível!
                        </div>

                    <?php else: ?>

                        <p>
                            Envie seus dados e entraremos em contato o mais rápido possível.
                        </p>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                                <div class="form-group">
                                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                                    <?= $form->field($model, 'name') ?>

                                    <?= $form->field($model, 'email') ?>

                                    <?= $form->field($model, 'telefone')->textInput(['maxlength' => 20]) ?>



                                    <?= Html::submitButton('Enviar',
                                        ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                                </div>

                                <?php ActiveForm::end(); ?>

                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

            <div class="hidden-xs col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div
                    class="colunaDireita sombra col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
                    <h2 class="text-center">Nossos Planos</h2>
                    <hr>
                    <div class="text-left">
                        <h4>Plano Mensal</h4>
                        <p>R$235 / mês </p>
                        <h4>Plano Anual</h4>
                        <p>R$115 / mês (Pague R$1380 /ano para oferecer seus serviços online, se pagar a vista temos
                            descontos incriveis)
                        </p>
                        <h4>Plano Coletivo</h4>
                        <p> Reúna um grupo de oficinas parceiras, peçam um Pacote Coletivo, juntos ganharão mais
                            visibilidade e descontos até 90% por mês) </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
