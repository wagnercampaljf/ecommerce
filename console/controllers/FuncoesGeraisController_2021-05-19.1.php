<?php
/**
 * Created by PhpStorm.
 * User: dev_peca_agora
 * Date: 12/07/18
 * Time: 09:26
 */

namespace console\controllers;


use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;


class FuncoesGeraisController extends Controller
{
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'atualizarmenormaiorpreco'          => 'console\controllers\actions\funcoesgerais\AtualizarmenormaiorprecoAction',
            'importacaoprecos'                  => 'console\controllers\actions\funcoesgerais\ImportacaoPrecosAction',
            'obterimagens'                      => 'console\controllers\actions\funcoesgerais\ObterImagensAction',
            'compararprodutos'                  => 'console\controllers\actions\funcoesgerais\CompararProdutosAction',
            'zerarestoque'                      => 'console\controllers\actions\funcoesgerais\ZerarEstoqueAction',
            'atualizarprodutoslng'              => 'console\controllers\actions\funcoesgerais\AtualizarProdutosLNGAction',
            'importacaoprecosbonfante'          => 'console\controllers\actions\funcoesgerais\ImportacaoPrecosBonfanteAction',
            'importacaoprecosvannucci'          => 'console\controllers\actions\funcoesgerais\ImportacaoPrecosVanucciAction',
            'atualizarmultiplicador'            => 'console\controllers\actions\funcoesgerais\AtualizarMultiplicadorAction',
            'compararids'                       => 'console\controllers\actions\funcoesgerais\CompararIdsAction',
            'verificarexisteimagem'             => 'console\controllers\actions\funcoesgerais\VerificarExisteImagemAction',
            'importacaocodigobarras'            => 'console\controllers\actions\funcoesgerais\ImportacaoCodigoBarrasAction',
            'importacaocodigobarrasbr'          => 'console\controllers\actions\funcoesgerais\ImportacaoCodigoBarrasBRAction',
            'importacaoUniversal'               => 'console\controllers\actions\funcoesgerais\ImportacaoUniversalAction',
            'gerartabelafreteb2w'               => 'console\controllers\actions\funcoesgerais\GerarPlanilhaFreteB2WAction',
            'deletarfilialinativa'              => 'console\controllers\actions\funcoesgerais\DeletarFilialInativaAction',
            'gerarimagens'                      => 'console\controllers\actions\funcoesgerais\GerarImagensAction',
            'importarImagemuniversal'           => 'console\controllers\actions\funcoesgerais\ImportacaoImagemUniversalAction',
            'importarimagem'                    => 'console\controllers\actions\funcoesgerais\ImportacaoImagemAction',
            'importarimagembr'                  => 'console\controllers\actions\funcoesgerais\ImportacaoImagemBRAction',
            'importarimagemvannucci'            => 'console\controllers\actions\funcoesgerais\ImportacaoImagemVannucciAction',
            'atualizarnome'                     => 'console\controllers\actions\funcoesgerais\AtualizarNomeAction',
            'importacaovannucci'                => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciAction',
            'importacaovannucci2'               => 'console\controllers\actions\funcoesgerais\ImportacaoVannucci2Action',
            'renomeararquivoslote'              => 'console\controllers\actions\funcoesgerais\RenomearArquivosLoteAction',
            'importacaoUniversal2'              => 'console\controllers\actions\funcoesgerais\ImportacaoUniversal2Action',
            'copiararquivosselecionados'        => 'console\controllers\actions\funcoesgerais\CopiarArquivosSelecionadosAction',
            'subirimagenswebp'                  => 'console\controllers\actions\funcoesgerais\SubirImagensTesteWebpAction',
            'importacaofconforto'               => 'console\controllers\actions\funcoesgerais\ImportacaoFConfortoAction',
            'importacaovannucci3'               => 'console\controllers\actions\funcoesgerais\ImportacaoVannucci3Action',
            'importacaolng'                     => 'console\controllers\actions\funcoesgerais\ImportacaoLNGAction',
            'importacaoimagemlng'               => 'console\controllers\actions\funcoesgerais\ImportacaoImagemNovasAction',
            'importacaovannuccimariana'         => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciMarianaAction',
            'importacaovannuccimarianadaniel'   => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciMarianaDanielAction',
            'importacaovannuccicorrecao'        => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciCorrecaoAction',
            'testemelhorenvios'                 => 'console\controllers\actions\funcoesgerais\TesteMelhorEnvioAction',
            'melhorenviosimplementacao'         => 'console\controllers\actions\funcoesgerais\MelhorEnviosImplementacaoAction',
            'correcaobr'                        => 'console\controllers\actions\funcoesgerais\CorrecaoBRAction',
            'importarimagemfconfourto'          => 'console\controllers\actions\funcoesgerais\ImportacaoImagemFConfourtoAction',
            'correcaogauss'                     => 'console\controllers\actions\funcoesgerais\CorrecaoGaussAction',
            'importacaobr'                      => 'console\controllers\actions\funcoesgerais\ImportacaoBRAction',
            'produtosduplicadosbr'              => 'console\controllers\actions\funcoesgerais\ProdutosDuplicadosBRAction',
            'importardib'                       => 'console\controllers\actions\funcoesgerais\ImportacaoDibAction',
            'importarimagensdib'                => 'console\controllers\actions\funcoesgerais\ImportacaoImagemDibAction',
            'calcularprecovenda'                => 'console\controllers\actions\funcoesgerais\CalcularPrecoVendaAction',
            'atualizarprecos'                   => 'console\controllers\actions\funcoesgerais\AtualizarPrecosFilialAction',
            'importacaovannucci4'               => 'console\controllers\actions\funcoesgerais\ImportacaoVannucci4Action',
            'importacaovannucci5'               => 'console\controllers\actions\funcoesgerais\ImportacaoVannucci5Action',
            'importacaovannuccisemtratar'       => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciSemTratarAction',
            'importacaolngitalo'                => 'console\controllers\actions\funcoesgerais\ImportacaoLNGItaloAction',
            'correcaobrmariana'                 => 'console\controllers\actions\funcoesgerais\CorrecaoBRMarianaAction',
            'correcaoduplicidadebr'             => 'console\controllers\actions\funcoesgerais\CorrecaoDuplicidadeBRAction',
            'criarcaixasdib'                    => 'console\controllers\actions\funcoesgerais\CriarCaixasDibAction',
            'dibpreco'                          => 'console\controllers\actions\funcoesgerais\AtualizarPrecosDibAction',
            'correcaodib'                       => 'console\controllers\actions\funcoesgerais\CorrecaoDibAction',
            'atualizarquantidadelng'            => 'console\controllers\actions\funcoesgerais\AtualizarQuantidadeLNGAction',
            'limparestoqueduplicado'            => 'console\controllers\actions\funcoesgerais\LimparEstoqueDuplicadoAction',
            'calculaprecocompravannucci'        => 'console\controllers\actions\funcoesgerais\CalcularPrecoCompraVannucciAction',
            'atualizarprecoestoquedib'          => 'console\controllers\actions\funcoesgerais\AtualizarPrecosEstoqueDibAction',
            'calculaprecovendavannucci'         => 'console\controllers\actions\funcoesgerais\CalcularPrecoVendaVannucciAction',
            'copiarimagensvannucci'             => 'console\controllers\actions\funcoesgerais\CopiarImagensVannucciAction',
            'importarvannuccipellegrino'        => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciPellegrinoAction',
            'verificacaovannuccimariana'        => 'console\controllers\actions\funcoesgerais\VerificacaoVannucciMarianaAction',
            'verificacaovannuccipellegrino'     => 'console\controllers\actions\funcoesgerais\VerificacaoVannucciPellegrinoAction',
            'correcaoduplicidadeestoque'        => 'console\controllers\actions\funcoesgerais\CorrecaoDuplicidadeEstoqueAction',
            'atualizarprecoslng'                => 'console\controllers\actions\funcoesgerais\AtualizarPrecosLNGAction',
            'compararplanilhasvannucci'         => 'console\controllers\actions\funcoesgerais\CompararPlanilhasVannucciAction',
            'atualizarcodigofabricantevannucci' => 'console\controllers\actions\funcoesgerais\AtualizarCodigoFabricanteVannucciAction',
            'atualizarprecodibcaixa'            => 'console\controllers\actions\funcoesgerais\AtualizarPrecosDibCaixaAction',
            'calcularprecovendacaixa'           => 'console\controllers\actions\funcoesgerais\CalcularPrecoVendaCaixaAction',
            'analiseprodutoslng'                => 'console\controllers\actions\funcoesgerais\AnaliseProdutosLNGAction',
            'analiseprodutoslngplanilha'        => 'console\controllers\actions\funcoesgerais\AnaliseProdutosLNGPlanilhaAction',
            'correcaocontaduplicada'            => 'console\controllers\actions\funcoesgerais\CorrecaoContaDuplicadaAction',
            'atualizarprecovannucci'            => 'console\controllers\actions\funcoesgerais\AtualizarPrecosVannucciAction',
            'calcularprecovendadib'             => 'console\controllers\actions\funcoesgerais\CalcularPrecoVendaDibAction',
            'vannuccicompararplanilhas'         => 'console\controllers\actions\funcoesgerais\AnaliseProdutosVannucciCompararPlanilhasAction',       
            'teste'                             => 'console\controllers\actions\funcoesgerais\AnaliseProdutosVannucciAction',
            'atualizarestoqueeanvannucci'       => 'consolImportacaoImagemLNGActione\controllers\actions\funcoesgerais\AtualizarEstoqueEANVannucciAction',
            'verificarprodutosexistemvannucci'  => 'console\controllers\actions\funcoesgerais\VerificarProdutosExistemVannucciAction',
            'zerarprodutosvannucci'             => 'console\controllers\actions\funcoesgerais\ZerarProdutosVannucciAusentesAction',
	    'lngapi'                            => 'console\controllers\actions\funcoesgerais\LNGAPIAction',
	    'vannuccipelegrino27000'            => 'console\controllers\actions\funcoesgerais\ImportacaoVannucciPellegrino27000Action',
	    'bratualizarestoqueprecos'          => 'console\controllers\actions\funcoesgerais\BRAtualizarEstoquePrecosAction',
	    'vannucciatualizarprodutos'         => 'console\controllers\actions\funcoesgerais\VannucciAtualizarProdutosAction',
	    'alterarimagensparawebp'            => 'console\controllers\actions\funcoesgerais\AlterarImagensParaWebpAction',
	    'marianakitembreagem'               => 'console\controllers\actions\funcoesgerais\ImportacaoMarianaKitEmpreagemAction',
	    'vannucciatualizarprecos'           => 'console\controllers\actions\funcoesgerais\VannucciAtualizarPrecosAction',
	    'brcalcularprecovenda'              => 'console\controllers\actions\funcoesgerais\BRCalcularPrecoVendaAction',
	    'brexisteprodutocodigoglobal'       => 'console\controllers\actions\funcoesgerais\BRExisteProdutoCodigoGlobalAction',
	    'lngatualizarprodutos'              => 'console\controllers\actions\funcoesgerais\LNGAtualizarProdutosAction',
	    'vannuccicalcularprecovenda'        => 'console\controllers\actions\funcoesgerais\VannucciCalcularPrecoVendaAction',
	    'vannuccicriarcaixa'                => 'console\controllers\actions\funcoesgerais\VannucciCriarCaixaAction',
	    'brcriarprodutos'              	=> 'console\controllers\actions\funcoesgerais\BRCriarProdutosAction',
	    'vannucciatualizarprodutos'         => 'console\controllers\actions\funcoesgerais\VannucciAtualizarProdutosAction',
	    'atualizarprodutos'                 => 'console\controllers\actions\funcoesgerais\AtualizarProdutosAction',
	    'vannucciatualizarestoque'          => 'console\controllers\actions\funcoesgerais\VannucciAtualizarEstoqueEANAction',
	    'dibcalcularprecovenda'             => 'console\controllers\actions\funcoesgerais\DibCalcularPrecoVendaAction',
	    'dibatualizarestoquepreco'          => 'console\controllers\actions\funcoesgerais\DibAtualizarPrecosEstoqueAction',
	    'morelateverificaexistemprodutos'   => 'console\controllers\actions\funcoesgerais\MorelateVerificarExistemProdutosAction',
	    'bratualizarprodutos'   		=> 'console\controllers\actions\funcoesgerais\BRAtualizarProdutosAction',
	    'vannuccicriarmarcas'               => 'console\controllers\actions\funcoesgerais\VannucciCriarMarcasAction',
            'vannucciatualizarmarcas'           => 'console\controllers\actions\funcoesgerais\VannucciAtualizarMarcasAction',
	    'brimportarimagem'           	=> 'console\controllers\actions\funcoesgerais\BRImportacaoImagemAction',
	    'kashimaatualizarprecos'            => 'console\controllers\actions\funcoesgerais\KashimaAtualizarPrecosAction',
	    'dibatualizarestoque'               => 'console\controllers\actions\funcoesgerais\DibAtualizarEstoqueAction',
	    'vannuccicriarprodutos'             => 'console\controllers\actions\funcoesgerais\VannucciCriarProdutosAction',
	    'moreltecriarprodutos'              => 'console\controllers\actions\funcoesgerais\MorelateCriarProdutosAction',
	    'morelteatualizarprodutos'          => 'console\controllers\actions\funcoesgerais\MorelateAtualizarProdutosAction',
	    'vannucciatualizarnome'             => 'console\controllers\actions\funcoesgerais\VannucciAtualizarNomesAction',
	    'morelateatualizarmarcas'           => 'console\controllers\actions\funcoesgerais\MorelateAtualizarMarcasAction',
	    'lngrecalcularprecos'               => 'console\controllers\actions\funcoesgerais\LNGRecalcularPrecosAction',
	    'vannuccipreencherdadosplanilha'    => 'console\controllers\actions\funcoesgerais\VannucciPreencherDadosPlanilhaAction',
	    'lngatualizarmultiplicador'         => 'console\controllers\actions\funcoesgerais\LNGAtualizarMultiplicadorAction',
	    'amazongerarplanilhadadoscompleta'  => 'console\controllers\actions\funcoesgerais\AmazonGerarPlanilhaDadosCompletaAction',
	    'lngplanilha'                       => 'console\controllers\actions\funcoesgerais\LNGPlanilhaAction',
	    'gausscalcularprecovenda'           => 'console\controllers\actions\funcoesgerais\GaussCalcularPrecoVendaAction',
	    'morelateverificarprodutos'         => 'console\controllers\actions\funcoesgerais\MorelateVerificarProdutosAction',
            'vannucciverificarprodutos'         => 'console\controllers\actions\funcoesgerais\VannucciVerificarProdutosAction',
	    'morelateatualizarestoque'          => 'console\controllers\actions\funcoesgerais\MorelateAtualizarEstoqueAction',
	    'morelateatualizarestoquepreco'     => 'console\controllers\actions\funcoesgerais\MorelateAtualizarEstoquePrecoAction',
	    'importaripi'                       => 'console\controllers\actions\funcoesgerais\ImportarIPIAction',
            'gaussatualizarprecovenda'           => 'console\controllers\actions\funcoesgerais\GaussAtualizaPrecoVendaAction',
            'sorocardatualizarprecovenda'       => 'console\controllers\actions\funcoesgerais\SorocardAtualizaPrecoVendaAction',
            'paranaatualizarprecovenda'         => 'console\controllers\actions\funcoesgerais\ParanaAtualizaPrecoVendaAction',
            'karteratualizarestoque'           => 'console\controllers\actions\funcoesgerais\KarterAtualizaEstoqueAction',
            'bonfantencm'       => 'console\controllers\actions\funcoesgerais\BonfanteProdutosNCMAction',
            'bonfanteatualizarprecos'       => 'console\controllers\actions\funcoesgerais\BonfanteAtualizarPrecosAction',
            'bonfanterecalcularprecos'       => 'console\controllers\actions\funcoesgerais\BonfanteCalcularPrecoVendaAction',
            'atualizarncmbonfante'          => 'console\controllers\actions\funcoesgerais\AtualizarNCMBonfanteAction',
            'bonfanteatualizarcodigofabricante'          => 'console\controllers\actions\funcoesgerais\AtualizarCodigoFabricanteBonfanteAction',
            'atualizarprecosk3'          => 'console\controllers\actions\funcoesgerais\SK3AtualizaPrecoVendaAction',
            'piracicabaatualizarprecovenda'       => 'console\controllers\actions\funcoesgerais\PiracicabaAtualizaPrecoVendaAction',
            'bfatualizarprecovenda'       => 'console\controllers\actions\funcoesgerais\BFAtualizaPrecoVendaAction',
            'pecaagorafisicaatualizarprecovenda'       => 'console\controllers\actions\funcoesgerais\PecaAgoraFisicaAtualizaPrecoVendaAction',
            'vendacasadaatualizarprecovenda'       => 'console\controllers\actions\funcoesgerais\VendaCasadaAtualizaPrecoVendaAction',
            'modelocalcularprecos'       => 'console\controllers\actions\funcoesgerais\ModeloCalcularPrecoVendaAction',
            'bratualizarmultiplicador'       => 'console\controllers\actions\funcoesgerais\BRAtualizarEstoquePrecosMultiplicadorAction',
            'atualizarcategoria'       => 'console\controllers\actions\funcoesgerais\AtualizarSubcategoriaAction',
	    'analiseprodutosomie'          => 'console\controllers\actions\funcoesgerais\AnaliseProdutosOmieAction',
            'bratualizarmarcas'       => 'console\controllers\actions\funcoesgerais\BRAtualizarMarcasAction',
            'compararplanilha'       => 'console\controllers\actions\funcoesgerais\CompararPlanilhasVannucciAusenteAction',
            'ajustarplanilha'       => 'console\controllers\actions\funcoesgerais\AjustarPlanilhaAction',
            'atualizarcodigoglobaldib'       => 'consRSAtualizaPrecoVendaActionole\controllers\actions\funcoesgerais\AtualizarCodigoGlobalDibAction',
            'atualizarcodigoglobal'       => 'console\controllers\actions\funcoesgerais\AtualizarCodigoGlobalAction',
            'cadastraprodutofilial'       => 'console\controllers\actions\funcoesgerais\CadastrarProdutoFilialAction',
            'atualizarnomeproduto'       => 'console\controllers\actions\funcoesgerais\AtualizarNomeProdutoAction',
            'criarprodutosdib'       => 'console\controllers\actions\funcoesgerais\DibCriarProdutosAction',
	    'migracaoimagensreferencia'	=> 'console\controllers\actions\funcoesgerais\MigracaoImagensReferenciaAction',
            'verificarprodutoexistente'           	=> 'console\controllers\actions\funcoesgerais\VerificarProdutoExistenteAction',
            'atualizarpecaagorars'           	=> 'console\controllers\actions\funcoesgerais\RSAtualizaPrecoVendaAction',
            'calcularprecovendageral'           	=> 'console\controllers\actions\funcoesgerais\CalcularPrecoVendaGeralAction',
            'm4partsatualizarpreco'           	=> 'console\controllers\actions\funcoesgerais\M4PartsAtualizaPrecoVendaAction',
            'lanternarscriaproduto'           	=> 'console\controllers\actions\funcoesgerais\LanternasCriarProdutosAction',
            'emblemasatualizarpreco'           	=> 'console\controllers\actions\funcoesgerais\EmblemasAtualizaPrecoVendaAction',
            'lusarcriarprodutos'           	=> 'console\controllers\actions\funcoesgerais\LusarCriarProdutosAction',
            'eustaquioatualizar'           	=> 'console\controllers\actions\funcoesgerais\EustaquioAtualizaPrecoVendaAction',
            'lusarcalcularpreco'           	=> 'console\controllers\actions\funcoesgerais\LusarCalcularPrecoVendaAction',
            'criarprodutosforros'           	=> 'console\controllers\actions\funcoesgerais\ForrosCriarProdutosAction',
            'calcularprecoforros'           	=> 'console\controllers\actions\funcoesgerais\ForrosCalcularPrecoVendaAction',
            'atualizarprecoforros'           	=> 'console\controllers\actions\funcoesgerais\ForrosAtualizaPrecoVendaAction',
            'm4partscalcularpreco'           	=> 'console\controllers\actions\funcoesgerais\M4PartsCalcularPrecoVendaAction',
            'replicarimagens'           	=> 'console\controllers\actions\funcoesgerais\ReplicarImagemBRAction',

            'morelatemaxpartsatualizarestoquepreco'           	=> 'console\controllers\actions\funcoesgerais\MorelateMaxPartsAtualizarEstoquePrecoAction',








            





































        ]);
    }
}
