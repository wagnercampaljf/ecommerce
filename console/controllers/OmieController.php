<?php


namespace console\controllers;

use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class OmieController extends Controller
{
	public function actions()
	{
		return ArrayHelper::merge(parent::actions(), [
			'consultaprodutos'      	    		=> 'console\controllers\actions\omie\ConsultaProdutosAction',
			'consultacontacorrente' 	    		=> 'console\controllers\actions\omie\ConsultaContaCorrenteAction',
			'criaproduto'           	    		=> 'console\controllers\actions\omie\CriaProdutoAction',
			'alteraproduto'	    	    			=> 'console\controllers\actions\omie\AlteraProdutoAction',
			'criaprodutotodos'      	   		 	=> 'console\controllers\actions\omie\CriaProdutoTodosAction',
			'alteraprodutotodos'    	    		=> 'console\controllers\actions\omie\AlteraProdutoTodosAction',
			'alteraprodutotodosdimensoes'   		=> 'console\controllers\actions\omie\AlteraProdutoTodosDimensoesAction',
			'criaprodutodibtodos'           		=> 'console\controllers\actions\omie\CriaProdutoDibTodosAction',
			'alteraprodutos'                		=> 'console\controllers\actions\omie\AlteraProdutosAction',
			'alteraprodutocodigopa'         		=> 'console\controllers\actions\omie\AlteraProdutoCodigoPAAction',
			'alteraprodutosparacodigopasp'  		=> 'console\controllers\actions\omie\AlteraProdutosParaCodigoPASPAction',
			'alteraprodutosparacodigopasparquivo'   => 'console\controllers\actions\omie\AlteraProdutosParaCodigoPASPArquivoAction',
			'alteraprodutoscorrigirduplicados'      => 'console\controllers\actions\omie\AlteraProdutosCorrigirDuplicadosAction',
			'alteradescricaotodos'                  => 'console\controllers\actions\omie\AlteraDescricaoTodosAction',
			'alterancmtodos'		    	    	=> 'console\controllers\actions\omie\AlteraProdutoTodosNCMAction',
			'criaprodutoscontaduplicada'            => 'console\controllers\actions\omie\CriaProdutosContaDuplicadaAction',
			'corrigirprodutotodos'                  => 'console\controllers\actions\omie\CorrigirProdutoTodosAction',
			'alterarncmplanilha'                    => 'console\controllers\actions\omie\AlteraNCMPlanilhaAction',
			'sincronizarestoque'                    => 'console\controllers\actions\omie\SincronizarEstoqueAction',
			'aplicardesconto'			    		=> 'console\controllers\actions\omie\AplicarDescontoAction',
			'alterarprodutosparapamg'               => 'console\controllers\actions\omie\AlteraProdutosParaCodigoPAMGAction',
			'excluirlampadas'                       => 'console\controllers\actions\omie\ExcluirLampadasAction',
			'alterarestoqueminasgerais'             => 'console\controllers\actions\omie\AlterarEstoqueMinasGeraisAction',
			'verificarcorrigirtodascontas'          => 'console\controllers\actions\omie\VerificacaoCorrecaoTodasContasAction',
			'vannucciatualizarestoque'              => 'console\controllers\actions\omie\VannucciAtualizarEstoquePeloOmieAction',
			'verificarncm'                          => 'console\controllers\actions\omie\VerificarNCMAction',
			'excluircaixas'                         => 'console\controllers\actions\omie\ExcluirCaixasAction',
			'importardadosdoomieporfilial'          => 'console\controllers\actions\omie\ImportarDadosDoOmiePorFilialAction',
			'excluirprodutoscomfiltro'		    	=> 'console\controllers\actions\omie\ExcluirProdutosComFiltroAction',
			'atualizardadosprodutostodos'           => 'console\controllers\actions\omie\AtualizarDadosProdutosTodosAction',
			'puxarestoquedoomie'                    => 'console\controllers\actions\omie\PuxarEstoqueDoOmieAction',
			'recebercontasareceber'                 => 'console\controllers\actions\omie\ReceberContasAReceberAction',
			'recebercontasarecebercontaduplicada'   => 'console\controllers\actions\omie\ReceberContasAReceberContaDuplicadaAction',
			'criarpedido'                 	    	=> 'console\controllers\actions\omie\CriarPedidoAction',
			'importacaonotasomie'           		=> 'console\controllers\actions\omie\ImportacaoNotasOmieAction',
			'importacaonotasomiee'           		=> 'console\controllers\actions\omie\ImportacaoNotasOmieeAction',
			'importacaoclientesomie'           		=> 'console\controllers\actions\omie\ImportacaoClientesOmieAction',
			'importacaofornecedoromie'           	=> 'console\controllers\actions\omie\ImportacaoFornecedorOmieAction',
			'importacaocontacorrenteomie'  			=> 'console\controllers\actions\omie\ImportacaoContaCorrenteOmieAction',
			//adicionado dia 12/05/2021
			'importacaotransportadoraomie'  		=> 'console\controllers\actions\omie\ImportacaoTrasportadoraOmieAction',
			'inclusaocodigointegracaoproduto'  		=> 'console\controllers\actions\omie\InclusaoCodigoIntegracaoProdutoAction',
			'sincronizarestoqueautomatico'         	=> 'console\controllers\actions\omie\SincronizarEstoqueAutomaticoAction',
			'puxarchavenotafiscalpedidomercadolivre'        => 'console\controllers\actions\omie\PuxarChaveNotaFiscalPedidoMercadoLivreAction',
			'corrigirpesostabela'                         => 'console\controllers\actions\omie\CorrigirPesosTabelaAction',
			'corrigirclientes'                              => 'console\controllers\actions\omie\CorrigirClientesAction',
			'sincronizarestoqueautomaticomg4'          => 'console\controllers\actions\omie\SincronizarEstoqueAutomaticoMG4Action',
			'sincronizarestoqueautomaticomg1'          => 'console\controllers\actions\omie\SincronizarEstoqueAutomaticoMG1Action',
		]);
	}
}
