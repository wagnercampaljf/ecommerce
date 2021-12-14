<?php
namespace frontend\widgets;


class FormSearch extends \yii\bootstrap\Widget
{
    public $model = null;

    public $container = 'container-fluid';

    public $formGroupClass = 'form-group';

    public $view = 'form';

    public function init()
    {
        if ($this->model == null) {
            $this->model = new \frontend\models\FormSearch;
        }
        parent::init();
    }

    public function run()
    {
        return $this->render(
            $this->view,
            [
                'model' => $this->model,
                'container' => $this->container,
                'formGroupClass' => $this->formGroupClass

            ]
        );
    }
}