<?php

namespace console\controllers\actions\testeconsole;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ProdutoFilial;
use common\models\Imagens;

class TesteB2WComLogoAction extends Action
{

    public function run(){

        echo "INÍCIO B2W Com Logo\n\n";

        $produto_filial = ProdutoFilial::find() ->andWhere(['=', 'produto_id', 29040])
                                                ->one();
        print_r($produto_filial->produto->getUrlImagesB2WComlogo());

        echo "\n\nFIM Com Logo";

    }
}
