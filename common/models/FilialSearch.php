<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Filial;

/**
 * FilialSearch represents the model behind the search form about `common\models\Filial`.
 */
class FilialSearch extends Filial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lojista_id', 'banco_id', 'id_tipo_empresa', 'envio'], 'integer'],
            [['nome', 'razao', 'documento', 'numero_banco', 'token_moip', 'telefone', 'telefone_alternativo', 'refresh_token_meli', 'email_pedido'], 'safe'],
            [['juridica', 'integrar_b2w', 'mercado_livre_secundario', 'mercado_livre_logo'], 'boolean'],
            [['porcentagem_venda'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Filial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'juridica' => $this->juridica,
            'lojista_id' => $this->lojista_id,
            'banco_id' => $this->banco_id,
            'porcentagem_venda' => $this->porcentagem_venda,
            'id_tipo_empresa' => $this->id_tipo_empresa,
            'integrar_b2w' => $this->integrar_b2w,
            'envio' => $this->envio,
            'mercado_livre_secundario' => $this->mercado_livre_secundario,
            'mercado_livre_logo' => $this->mercado_livre_logo,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'razao', $this->razao])
            ->andFilterWhere(['like', 'documento', $this->documento])
            ->andFilterWhere(['like', 'numero_banco', $this->numero_banco])
            ->andFilterWhere(['like', 'token_moip', $this->token_moip])
            ->andFilterWhere(['like', 'telefone', $this->telefone])
            ->andFilterWhere(['like', 'telefone_alternativo', $this->telefone_alternativo])
            ->andFilterWhere(['like', 'refresh_token_meli', $this->refresh_token_meli])
            ->andFilterWhere(['like', 'email_pedido', $this->email_pedido]);

        return $dataProvider;
    }
}
