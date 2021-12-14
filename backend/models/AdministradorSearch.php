<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Administrador;

/**
 * AdministradorSearch represents the model behind the search form about `backend\models\Administrador`.
 */
class AdministradorSearch extends Administrador
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'nome', 'cpf', 'email', 'password', 'dt_criacao', 'dt_ultima_mudanca_senha', 'auth_key', 'password_reset_token'], 'safe'],
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
        $query = Administrador::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'dt_criacao' => $this->dt_criacao,
            'ativo' => $this->ativo,
            'dt_ultima_mudanca_senha' => $this->dt_ultima_mudanca_senha,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'nome', $this->nome])
            ->andFilterWhere(['ilike', 'cpf', $this->cpf])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'auth_key', $this->auth_key])
            ->andFilterWhere(['ilike', 'password_reset_token', $this->password_reset_token]);

        return $dataProvider;
    }
}
