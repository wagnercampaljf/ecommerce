<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 21/06/2016
 * Time: 12:34
 */

namespace console\controllers;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class MercadoLivreController extends Controller
{
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'create' 					=> 'console\controllers\actions\mercadolivre\CreateAction',
	    'createuni' 				=> 'console\controllers\actions\mercadolivre\CreateuniAction',
            'relist' 					=> 'console\controllers\actions\mercadolivre\RelistAction',
            'updateuni' 				=> 'console\controllers\actions\mercadolivre\UpdateuniAction',
            'update' 					=> 'console\controllers\actions\mercadolivre\UpdateAction',
            'closed' 					=> 'console\controllers\actions\mercadolivre\ClosedAction',
            'delete' 					=> 'console\controllers\actions\mercadolivre\DeleteAction',
	    'reset' 					=> 'console\controllers\actions\mercadolivre\ResetAction',
	    'verificaintegracao'			=> 'console\controllers\actions\mercadolivre\VerificaintegracaoAction',
	    'updatecorrecaoglobal'      		=> 'console\controllers\actions\mercadolivre\UpdateCorrecaoGlobalAction',
	    'atualizacategoria'         		=> 'console\controllers\actions\mercadolivre\AtualizaCategoriaAction',
	    'updatecontaduplicada'			=> 'console\controllers\actions\mercadolivre\UpdateContaDuplicadaAction',
	    'deletecontaduplicada'      		=> 'console\controllers\actions\mercadolivre\DeleteContaDuplicadaAction',
	    'createcontaduplicada'      		=> 'console\controllers\actions\mercadolivre\CreateContaDuplicadaAction',
	    'alterarprecoalgomais'			=> 'console\controllers\actions\mercadolivre\AlterarPrecoAlgomaisAction',
	    'analiseprodutosduplicadosvannucci'		=> 'console\controllers\actions\mercadolivre\AnaliseProdutosDuplicadosVannucciAction',
	    'updatetitulo'                    		=> 'console\controllers\actions\mercadolivre\UpdateTituloAction',
	    'updateimagem'                      	=> 'console\controllers\actions\mercadolivre\UpdateImagemAction',
	    'updatepreco'				=> 'console\controllers\actions\mercadolivre\UpdatePrecoAction',
	    'colocardibml3'                     	=> 'console\controllers\actions\mercadolivre\ColocarDibML3Action',
	    'atualizardimensoes'                	=> 'console\controllers\actions\mercadolivre\AtualizarDimensoesAction',
	    'relatorioprodutosporcategoriaheath'	=> 'console\controllers\actions\mercadolivre\GerarRelatorioProdutosPorCategoriaHeathAction',
	    'analiseconcorrencia'                   	=> 'console\controllers\actions\mercadolivre\AnaliseConcorrenciaMLAction',
	    'obtercategorias'                      	=> 'console\controllers\actions\mercadolivre\ObterCategoriasAction',
	    'createsemjuros'                       	=> 'console\controllers\actions\mercadolivre\CreateProdutosSemJurosAction',
	    'updatetitulocontaduplicada'                => 'console\controllers\actions\mercadolivre\UpdateTituloContaDuplicadaAction',
	    'updatecontaprincipalparacontaduplicada'	=> 'console\controllers\actions\mercadolivre\UpdateContaPrincipalParaContaDuplicadaAction',
	    'limparprodutossemvinculo'              	=> 'console\controllers\actions\mercadolivre\LimparProdutosSemVinculoAction',
	    'updateprecocontaduplicada'             	=> 'console\controllers\actions\mercadolivre\UpdatePrecoContaDuplicadaAction',
	    'updateean' 		                => 'console\controllers\actions\mercadolivre\UpdateEANAction',
	    'updatecategoria'                           => 'console\controllers\actions\mercadolivre\UpdateCategoriaAction',
	    'updatecategoriacontaduplicada'             => 'console\controllers\actions\mercadolivre\UpdateCategoriaContaDuplicadaAction',
	    'updatenumeropeca'				=> 'console\controllers\actions\mercadolivre\UpdateNumeroPecaAction',
	    'updateeativo'	                        => 'console\controllers\actions\mercadolivre\UpdateEAtivoAction',
	    'updatenumeropecacontaduplicada'          	=> 'console\controllers\actions\mercadolivre\UpdateNumeroPecaContaDuplicadaAction',
	    'updatefichatecnica'                    	=> 'console\controllers\actions\mercadolivre\UpdateFichaTecnicaAction',
	    'gerarrelatorioprodutosdescricao'           => 'console\controllers\actions\mercadolivre\GerarRelatorioProdutosDescricaoAction',
	    'verificardescricaoplanilha'                => 'console\controllers\actions\mercadolivre\VerificaDescricaoMLPlanilhaAction',
	    'updateprecocomvendas'                      => 'console\controllers\actions\mercadolivre\UpdatePrecoComVendasAction',
	    'morelatecreate'                        	=> 'console\controllers\actions\mercadolivre\MorelateCreateAction',
	    'updatesemjuros'                        	=> 'console\controllers\actions\mercadolivre\UpdateSemJurosAction',

	    'updateprecobr'                         => 'console\controllers\actions\mercadolivre\UpdatePrecoBRAction',
            'updateprecodib'                        => 'console\controllers\actions\mercadolivre\UpdatePrecoDIBAction',
            'updateprecofisica'                     => 'console\controllers\actions\mercadolivre\UpdatePrecoFisicaAction',
            'updateprecoita'                        => 'console\controllers\actions\mercadolivre\UpdatePrecoItaAction',
            'updateprecokit'                        => 'console\controllers\actions\mercadolivre\UpdatePrecoKitAction',
            'updateprecolng'                        => 'console\controllers\actions\mercadolivre\UpdatePrecoLNGAction',
            'updateprecomorelate'                   => 'console\controllers\actions\mercadolivre\UpdatePrecoMorelateAction',
            'updateprecoparana'                     => 'console\controllers\actions\mercadolivre\UpdatePrecoParanaAction',
            'updateprecopiracicaba'                 => 'console\controllers\actions\mercadolivre\UpdatePrecoPiracicabaAction',
            'updateprecopr'                         => 'console\controllers\actions\mercadolivre\UpdatePrecoPRAction',
            'updateprecosorocaba'                   => 'console\controllers\actions\mercadolivre\UpdatePrecoSorocabaAction',
            'updateprecospacabamentos'              => 'console\controllers\actions\mercadolivre\UpdatePrecoSPAcabamentosAction',
            'updateprecovannucci'                   => 'console\controllers\actions\mercadolivre\UpdatePrecoVannucciAction',
            'updateprecobf'                         => 'console\controllers\actions\mercadolivre\UpdatePrecoBFAction',
	    'updateprecoporfilial'                  => 'console\controllers\actions\mercadolivre\UpdatePrecoPorFilialAction',
	    'updatecondicao'                        => 'console\controllers\actions\mercadolivre\UpdateCondicaoAction',
	    'gerarrelatoriocategoriaporfilial'      => 'console\controllers\actions\mercadolivre\GerarRelatorioCategoriaPorFilialAction',
   	    'updatemarca'                           => 'console\controllers\actions\mercadolivre\UpdateMarcaAction',
	    'pedidoscriaratualizar'                 => 'console\controllers\actions\mercadolivre\PedidosCriarAlterarAction',
	    'verificacaocaixas'                     => 'console\controllers\actions\mercadolivre\VerificacaoCaixasAction',
	    'resetarprodutosportitulo'              => 'console\controllers\actions\mercadolivre\ResetarProdutosPorTituloAction',
        ]);
    }
}
