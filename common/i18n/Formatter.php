<?php
/**
 * Created by PhpStorm.
 * User: smart_i9
 * Date: 07/04/2015
 * Time: 13:13
 */

namespace common\i18n;

use yii\i18n;

class Formatter extends i18n\Formatter
{

    /**
     * Método para criação de formatos customizados
     *
     * Exemplo CPF: mask(11122233300,'###.###.###-##') = 111.222.333-00
     *
     * @param $val = valor a ser formatado
     * @param string $mask = Formato no qual # representa um caracter e os demais caracteres representam os separados customizados
     * @return string
     * @author Vitor Horta
     * @since 0.1
     */
    public function customFormat($val, $format)
    {
        if ($val === null || $format === null)
            return $val;

        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($format) - 1; $i++) {
            if ($format[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($format[$i]))
                    $maskared .= $format[$i];
            }
        }
        return $maskared;
    }

    /**
     * Retorna valor em formato CPF
     * @param $val
     * @return string
     * @author Vitor Horta
     * @since 0.1
     */
    public function asCPF($val)
    {
        return $this->customFormat($val, '###.###.###-##');
    }

    /**
     * Retorna valor em formato CNPJ
     * @param $val
     * @return string
     * @author Vitor Horta
     */
    public function asCNPJ($val)
    {
        return $this->customFormat($val, '##.###.###/####-##');
    }

    /**
     * Retorna valor em formato CNPJ
     * @param $val
     * @return string
     * @author Vitor Horta
     */
    public function asTelefone($val)
    {

        $mask = (strlen($val) == 10) ? '(##) ####-####' : '(##) #####-####';
        return $this->customFormat($val, $mask);
    }

    /**
     * Retorna valor em formato CEP
     * @param $val
     * @return string
     * @author Otavio Augsuto
     */
    public function asCEP($val)
    {
        $mask = '#####-###';
        return $this->customFormat($val, $mask);
    }
}

