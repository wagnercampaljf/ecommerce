<?php

namespace app\models;

use Yii;

/**
 * Este Ã© o model para a tabela "newsletter".
 *
 * @property integer $id
 * @property string $nome
 * @property string $email
 * @property boolean $Leves
 * @property boolean $Pesados
 *
 * @author Otávio 27/11/2015
 */
class Newsletter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     * @author Otávio 27/11/2015
     */
    public static function tableName()
    {
        return 'newsletter';
    }

    /**
     * @inheritdoc
     * @author Otávio 27/11/2015
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
     * @author Otávio 27/11/2015
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
     * @author Otávio 27/11/2015
     */
    public static function find()
    {
        return new NewsletterQuery(get_called_class());
    }
}

/**
 * Classe para contenÃ§Ã£o de escopos da Newsletter, utilizada nas operaÃ§Ãµes find() da mesma
 * @return \yii\db\ActiveQuery
 * @author Otávio 27/11/2015
 */
class NewsletterQuery extends \yii\db\ActiveQuery
{
    /**
     * OrdenaÃ§Ã£o AlfabÃ©tica
     * @return \yii\db\ActiveQuery
     * @author Otávio 27/11/2015
     */
    public function ordemAlfabetica($sort_type = SORT_ASC)
    {
        return $this->orderBy(['newsletter.nome' => $sort_type]);
    }
}
