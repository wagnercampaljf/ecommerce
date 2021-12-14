<?php

namespace common\models;

use Yii;

/**
 * Este é o model para a tabela "transporte_proprio_filial".
 *
 * @property integer $id
 * @property double $peso_max
 * @property integer $altura_max
 * @property integer $largura_max
 * @property integer $profundidade_max
 * @property integer $distancia_gratuita
 * @property integer $distancia_max
 * @property double $preco_km
 * @property integer $filial_id
 *
 * @property Filial $filial
 *
 * @author Vinicius Schettino 02/12/2014
 */
class TransporteProprioFilial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public static function tableName()
    {
        return 'transporte_proprio_filial';
    }

    /**
     * @inheritdoc
     * @author Vinicius Schettino 02/12/2014
     */
    public function rules()
    {
        return [
            [['peso_max', 'preco_km', 'valor_minimo'], 'number'],
            [
                ['altura_max', 'largura_max', 'profundidade_max', 'distancia_gratuita', 'distancia_max', 'filial_id'],
                'integer'
            ],
            [['filial_id'], 'required']
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
            'peso_max' => 'Peso Max',
            'altura_max' => 'Altura Max',
            'largura_max' => 'Largura Max',
            'profundidade_max' => 'Profundidade Max',
            'distancia_gratuita' => 'Distancia Gratuita',
            'distancia_max' => 'Distancia Max',
            'preco_km' => 'Preco Km',
            'filial_id' => 'Filial ID',
            'valor_minimo' => 'Valor Minimo',
        ];
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
    public static function find()
    {
        return new TransporteProprioFilialQuery(get_called_class());
    }

    public function getValorFrete($distancia)
    {
        if ($this->distancia_gratuita >= $distancia) {
            return 0;
        }
        if ($this->distancia_max <= $distancia) {
            return false;
        }

        return $this->preco_km * $distancia;
    }
}

/**
 * Classe para contenção de escopos da TransporteProprioFilial, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Vinicius Schettino 02/12/2014
 */
class TransporteProprioFilialQuery extends \yii\db\ActiveQuery
{
    public function byFilial($id)
    {
        return $this->andWhere(['filial_id' => $id]);
    }

    public function byDistancia($distancia)
    {
        return $this->andWhere('distancia_max > ' . $distancia);
    }

    public function byAltura($altura)
    {
        return $this->andWhere('altura_max > ' . $altura);
    }

    public function byProfundidade($profundidade)
    {
        return $this->andWhere('profundidade_max > ' . $profundidade);
    }

    public function byLargura($largura)
    {
        return $this->andWhere('largura_max > ' . $largura);
    }

    public function byPeso($peso)
    {
        return $this->andWhere('peso_max > ' . $peso);
    }

    public function byValorMinimo($valor)
    {
        $this->andWhere('valor_min <= ' . $valor);

        return $this->orWhere('valor_min IS NULL');
    }
}
