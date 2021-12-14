<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Comprador;

/**
 * CompradorSearch represents the model behind the search form about `common\models\Comprador`.
 */
class CompradorSearch extends Comprador
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'empresa_id', 'nivel_acesso_id'], 'integer'],
            [['nome', 'cpf', 'username', 'password', 'dt_criacao', 'dt_ultima_mudanca_senha', 'email', 'cargo', 'auth_key', 'password_reset_token', 'token_moip'], 'safe'],
            [['ativo'], 'boolean'],
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
        $query = Comprador::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' =>
                [
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'empresa_id' => $this->empresa_id,
            'dt_criacao' => $this->dt_criacao,
            'ativo' => $this->ativo,
            'dt_ultima_mudanca_senha' => $this->dt_ultima_mudanca_senha,
            'nivel_acesso_id' => $this->nivel_acesso_id,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'cpf', $this->cpf])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'cargo', $this->cargo])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'token_moip', $this->token_moip]);

        return $dataProvider;
    }
}
