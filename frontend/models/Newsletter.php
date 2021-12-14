<?php

namespace app\models;

use Yii;

/**
 * Este é o model para a tabela "newsletter".
 *
 * @property integer $id
 * @property string $nome
 * @property string $email
 * @property boolean $Leves
 * @property boolean $Pesados
 *
 * @author Ot�vio 27/11/2015
 */
class Newsletter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Ot�vio 27/11/2015
     */
    public static function tableName()
    {
        return 'newsletter';
    }

    /**
     * @inheritdoc
     * @author Ot�vio 27/11/2015
     */
    public function rules()
    {
        return [
            [['nome', 'email'], 'required'],
            [['Leves', 'Pesados'], 'boolean'],
            ['email', 'unique'],
            ['email', 'email'],
            [['nome', 'email'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     * @author Ot�vio 27/11/2015
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'Email',
            'Leves' => 'Leves',
            'Pesados' => 'Pesados',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 27/11/2015
     */
    public static function find()
    {
        return new NewsletterQuery(get_called_class());
    }
}

/**
 * Classe para contenção de escopos da Newsletter, utilizada nas operações find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Ot�vio 27/11/2015
 */
class NewsletterQuery extends \yii\db\ActiveQuery
{
    /**
     * Ordenação Alfabética
     * @return \yii\db\ActiveQuery
     * @author Ot�vio 27/11/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['newsletter.nome' => $sort_type]);
    }
}
