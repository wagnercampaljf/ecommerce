<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 16/03/2016
 * Time: 17:14
 */

namespace lojista\controllers\actions\pedido;


use common\models\EnderecoEmpresa;
use common\models\EnderecoFilial;
use common\models\Pedido;
use common\models\Produto;
use common\models\Transportadora;
use lojista\components\AccessDataProducao;
use PhpSigep\Model\ServicoDePostagem;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class Action extends \yii\base\Action
{

    const servico_postagem = [
        'PAC' => ServicoDePostagem::SERVICE_PAC_INTERMEDIACAO_ECOMMERCE,
        'SEDEX' => ServicoDePostagem::SERVICE_SEDEX_INTERMEDIACAO_ECOMMERCE
    ];

    /**
     * @param $id
     * @return Pedido
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Pedido::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $ambiente
     * @return \PhpSigep\Config
     * @throws BadRequestHttpException
     */
    protected function start($ambiente)
    {
        if (!$accessData = $this->getAccessData($ambiente)) {
            throw new BadRequestHttpException();
        }

        $config = new \PhpSigep\Config();
        $config->setAccessData($accessData);
        $config->setEnv($ambiente);
        $config->setCacheOptions(
            array(
                'storageOptions' => array(
                    // Qualquer valor setado neste atributo será mesclado ao atributos das classes
                    // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                    // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                    'enabled' => false,
                    'ttl' => 10,
                    // "time to live" de 10 segundos
                    'cacheDir' => sys_get_temp_dir(),
                    // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                ),
            )
        );

        \PhpSigep\Bootstrap::start($config);

        return $config;
    }

    /**
     * @param $ambiente
     * @return \PhpSigep\Model\AccessData
     */
    protected function getAccessData($ambiente)
    {
        if ($ambiente == \PhpSigep\Config::ENV_DEVELOPMENT) {
            return new \PhpSigep\Model\AccessDataHomologacao();
        }
        if ($ambiente == \PhpSigep\Config::ENV_PRODUCTION) {
            return new AccessDataProducao();
        }

        return null;
    }

    /**
     * @param $model
     * @return \PhpSigep\Model\Dimensao
     */
    protected function getDimensao($model)
    {
        $altura = Produto::find()->innerJoinWith(['filiaisProduto.pedidos'])->andWhere(['pedido.id' => $model->id])->sum('produto.altura');
        $largura = Produto::find()->innerJoinWith(['filiaisProduto.pedidos'])->andWhere(['pedido.id' => $model->id])->sum('produto.largura');
        $profundidade = Produto::find()->innerJoinWith(['filiaisProduto.pedidos'])->andWhere(['pedido.id' => $model->id])->sum('produto.profundidade');

        $volume = $altura * $largura * $profundidade;
        $altura = $largura = $profundidade = pow($volume, (1 / 3));

        $dimensao = new \PhpSigep\Model\Dimensao();
        $dimensao->setAltura(ceil($altura));
        $dimensao->setLargura(ceil($largura));
        $dimensao->setComprimento(ceil($profundidade));
        $dimensao->setTipo(\PhpSigep\Model\Dimensao::TIPO_PACOTE_CAIXA);

        return $dimensao;
    }

    /**
     * @author Igor Mageste
     * @since 16/03/2016
     * @param $nome
     * @param $enderecoComprador EnderecoEmpresa
     * @return \PhpSigep\Model\Destinatario
     */
    protected function getDestinatario($nome, $enderecoComprador)
    {
        $destinatario = new \PhpSigep\Model\Destinatario();
        $destinatario->setNome($nome);
        $destinatario->setLogradouro($enderecoComprador->logradouro);
        $destinatario->setNumero($enderecoComprador->numero);
        $destinatario->setComplemento($enderecoComprador->complemento);
        $destinatario->setReferencia($enderecoComprador->referencia);

        return $destinatario;
    }

    /**
     * @author Igor Mageste
     * @since 16/03/2016
     * @param $enderecoComprador EnderecoEmpresa
     * @return \PhpSigep\Model\DestinoNacional
     */
    protected function getDestino($enderecoComprador)
    {
        $destino = new \PhpSigep\Model\DestinoNacional();
        $destino->setBairro($enderecoComprador->bairro);
        $destino->setCep($enderecoComprador->cep);
        $destino->setCidade($enderecoComprador->cidade->nome);
        $destino->setUf($enderecoComprador->cidade->estado->sigla);

        return $destino;
    }

    /**
     * @author Igor Mageste
     * @since 16/03/2016
     * @param $filial_nome string
     * @param $enderecoFilial EnderecoFilial
     * @return \PhpSigep\Model\Remetente
     */
    protected function getRemetente($filial_nome, $enderecoFilial)
    {
        $remetente = new \PhpSigep\Model\Remetente();
        $remetente->setNome($filial_nome);
        $remetente->setLogradouro($enderecoFilial->logradouro);
        $remetente->setNumero($enderecoFilial->numero);
        $remetente->setComplemento($enderecoFilial->complemento);
        $remetente->setBairro($enderecoFilial->bairro);
        $remetente->setCep($enderecoFilial->cep);
        $remetente->setUf($enderecoFilial->cidade->estado->sigla);
        $remetente->setCidade($enderecoFilial->cidade->nome);

        return $remetente;
    }

    /**
     * @author Igor Mageste
     * @since 16/03/2016
     * @param $nrEtiqueta string
     * @return \PhpSigep\Model\Etiqueta
     */
    protected function getEtiqueta($nrEtiqueta)
    {
        $etiqueta = new \PhpSigep\Model\Etiqueta();
        $etiqueta->setEtiquetaSemDv($nrEtiqueta);

        return $etiqueta;
    }

    /**
     * @author Igor Mageste
     * @since 21/07/2016
     * @param $model Pedido
     * @return \PhpSigep\Model\ServicoAdicional[]
     */
    protected function getServicoAdicional($model)
    {
        $servicoAdicionais = [];

        $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
        $servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_REGISTRO);
        array_push($servicoAdicionais, $servicoAdicional);

        $servicoAdicional = new \PhpSigep\Model\ServicoAdicional();
        $servicoAdicional->setCodigoServicoAdicional(\PhpSigep\Model\ServicoAdicional::SERVICE_VALOR_DECLARADO);
        $servicoAdicional->setValorDeclarado(number_format($model->valor_total, 2, ',', ''));
        array_push($servicoAdicionais, $servicoAdicional);

        return $servicoAdicionais;
    }

    /**
     * @author Igor Mageste
     * @since 21/07/2016
     * @param $model Transportadora
     * @return ServicoDePostagem
     */
    protected function getServicoPostagem($model)
    {
        $servicoPostagem = new \PhpSigep\Model\ServicoDePostagem(
            ArrayHelper::getValue(
                self::servico_postagem,
                $model->codigo
            )
        );

        return $servicoPostagem;
    }

    /**
     * @author Igor Mageste
     * @since 16/03/2016
     * @param $model Pedido
     * @return \PhpSigep\Model\ObjetoPostal
     */
    protected function getEncomenda($model)
    {
        $destinatario = $this->getDestinatario($model->comprador->nome, $model->comprador->empresa->enderecoEmpresa);
        $destino = $this->getDestino($model->comprador->empresa->enderecoEmpresa);
        $dimensao = $this->getDimensao($model);
        $etiqueta = $this->getEtiqueta($model->etiqueta);
        $servicoPostagem = $this->getServicoPostagem($model->transportadora);
        $servicoAdicional = $this->getServicoAdicional($model);
        $peso = Produto::find()->innerJoinWith(['filiaisProduto.pedidos'])->andWhere(['pedido.id' => $model->id])->sum('produto.peso');

        $encomenda = new \PhpSigep\Model\ObjetoPostal();
        $encomenda->setDestinatario($destinatario);
        $encomenda->setDestino($destino);
        $encomenda->setDimensao($dimensao);
        $encomenda->setEtiqueta($etiqueta);
        $encomenda->setPeso($peso);
        $encomenda->setServicoDePostagem($servicoPostagem);
        $encomenda->setServicosAdicionais($servicoAdicional);

        return $encomenda;
    }
}