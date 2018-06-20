<?php
/**
 * Created by PhpStorm.
 * User: gustavoweb
 * Date: 18/06/2018
 * Time: 14:23
 */

require_once __DIR__ . '/Cielo.php';

$cielo = new \Payment\Cielo(false);

//$card = $cielo->createCreditCard('Gustavo Web', '4024007153763191', 'Gustavo Web', '12/2020');
$card = $cielo->getCreditCard('TOKEN DO CARTAO');


//$transaction = $cielo->paymentRequest('123', '1000', 1, 'TOKEN DO CARTAO', true);

//$transaction = $cielo->getTransaction('PAYMENTID DA REQUISIÇÃO DE PAGAMENTO');
var_dump($card);