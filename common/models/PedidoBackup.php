<?php

namespace common\models;

use frontend\controllers\MailerController;
use Yii;

/**
 * Este é o model para a tabela "pedido".
 *
 * @property integer $id
 * @property double $valor_total
 * @property string $dt_referencia
 * @property string $data_prevista
 * @property integer $comprador_id
 * @property integer $filial_id
 * @property integer $transportadora_id
 * @property double $valor_frete
 * @property integer $forma_pagamento_id
 * @property string $token_moip
 * @property string $etiqueta
 * @property integer $plp_id
 * @property string $token_payment
 *
 * @property PedidoProdutoFilial[] $pedidoProdutoFilials
 * @property ProdutoFilial[] $produtoFilials
 * @property Comprador $comprador
 * @property Filial $filial
 * @property Transportadora $transportadora
 * @property FormaPagamento $formaPagamento
 * @property StatusPedido[] $statusPedidos
 *
 * @author Vinicius Schettino 02/12/2014
 */
class Pedido extends \yii\db\ActiveRecord
{
    var $status;
    public static $statusClasses = array(
        1 => 'common\models\PedidoStatusAberto',
        2 => 'common\models\PedidoStatusConfirmado',
        3 => 'common\models\PedidoStatusEnviado',
        4 => 'common\models\PedidoStatusConcluido',
        5 => 'common\models\PedidoStatusCancelado'
    );

    public static $statusSkyhub = [
        'NEW' => 1,
        'APPROVED' => 2,
        'SHIPPED' => 3,
        'DELIVERED' => 4,
        'CANCELED' => 5
    ];

    public static $transportadorasClasses = [
        1 => '\frete\conectores\SedexFrete',
        2 => '\frete\conectores\PacFrete',
        3 => '\frete\conectores\ParticularFrete',
        4 => '\frete\conectores\RetiradaFrete',
        5 => '\frete\conectores\PicorelliFrete',
    ];

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'pedido';
    }

    /**
     * Instancia atributo status de acordo com o id do status do pedido
     *
     * @author Vitor Horta  24/03/2015
     * @since 0.1
     */
    public function afterFind()
    {
        parent::afterFind();
        if ($this->statusAtual) {
            $class = Pedido::$statusClasses[$this->statusAtual->tipoStatus->id];
            $this->status = new $class();
        }
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['valor_total', 'comprador_id', 'filial_id', 'forma_pagamento_id'], 'required'],
            [['transportadora_id'], 'required', 'on' => 'checkout', 'message' => 'Selecione uma das "Formas de Envio"'],
            [['valor_total', 'valor_frete'], 'number'],
            [['dt_referencia', 'data_prevista'], 'safe'],
            [['comprador_id', 'filial_id', 'transportadora_id', 'forma_pagamento_id', 'plp_id'], 'integer'],
            [['token_moip', 'token_payment'], 'string', 'max' => 255],
            [['etiqueta'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'valor_total' => 'Valor Total',
            'dt_referencia' => 'Data de Referência',
            'comprador_id' => 'Comprador ID',
            'filial_id' => 'Filial ID',
            'transportadora_id' => 'Transportadora ID',
            'valor_frete' => 'Valor Frete',
            'forma_pagamento_id' => 'Forma Pagamento ID',
            'token_moip' => 'Token MOIP',
            'token_payment' => 'Token Payment',
            'data_prevista' => 'Data Prevista'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getPedidoProdutoFilials()
    {
        return $this->hasMany(PedidoProdutoFilial::className(), ['pedido_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getProdutoFilials()
    {
        return $this->hasMany(ProdutoFilial::className(),
            ['id' => 'produto_filial_id'])->viaTable('pedido_produto_filial', ['pedido_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getComprador()
    {
        return $this->hasOne(Comprador::className(), ['id' => 'comprador_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getServicoTransportadora()
    {
        return $this->hasOne(ServicoTransportadora::className(), ['id' => 'servico_transportadora_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getTransportadora()
    {
        return $this->hasOne(Transportadora::className(), ['id' => 'transportadora_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function getBoletos()
    {
        return $this->hasMany(Boleto::className(), ['pedido_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public static function find()
    {
        return new PedidoQuery(get_called_class());
    }

    public function getStatusPedidos()
    {
        return $this->hasMany(StatusPedido::className(), ['pedido_id' => 'id']);
    }

    /**
     * Retorna o status mais atual de um determinado pedido, ordenando pela data_corrente
     *
     * @return static
     * @author Vitor 11/03/2015
     * @since  0.1
     */
    public function getStatusAtual()
    {
        return $this->hasOne(StatusPedido::className(), ['pedido_id' => 'id'])->orderBy('data_corrente DESC');

    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Vitor 11/03/2015
     */
    public function getFormaPagamento()
    {
        return $this->hasOne(FormaPagamento::className(), ['id' => 'forma_pagamento_id']);
    }

    public function getTextoPedidoConcluido()
    {
        $class = self::$transportadorasClasses[$this->transportadora_id];

        return $class::getTextoPedidoConcluido($this);
    }

    public function mudarStatus($novoStatus)
    {
        $this->status = $this->status->mudarStatus($novoStatus);
        $this->statusAtual->tipo_status_id = $this->status->id;
        $this->statusAtual->save(false);
        $usuario = $this->filial->usuario;

        $params = array(
            'id' => $this->id,
            'usuarioEmail' => $usuario->email,
            'usuarioNome' => $usuario->nome,
        );

        Yii::$app->asyncMailer->sendMail($params, MailerController::MUDANCA_STATUS);

        if (PedidoStatusEnviado::isCompleted($this->statusAtual->tipo_status_id)) {
            Yii::$app->mailer->compose('pedido_enviado', [
                'nome' => $this->comprador->nome,
                'status' => $this->statusAtual->tipoStatus->nome,
                'id' => $this->id,
                'cod_rastro' => $this->etiqueta
            ])->setFrom(Yii::$app->params['supportEmail'])->setTo($this->comprador->email)->setBcc('logistica.pecaagora@gmail.com')->setSubject('Pedido Enviado')->send();
        }
    }

    public function getValorTotalSemJuros()
    {
        $valorToral = 0;
        foreach ($this->pedidoProdutoFilials as $pedidoProdutoFilial) {
            $valorToral += $pedidoProdutoFilial->quantidade * $pedidoProdutoFilial->valor;
        }

        return $valorToral;
    }


}

/**
 * Classe para contenção de escopos da Pedido, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class PedidoQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Vinicius Schettino 02/12/2014
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['pedido.nome' => $sort_type]);
    }
}
