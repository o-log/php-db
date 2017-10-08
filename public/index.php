<?php

require_once '../vendor/autoload.php';

\DBDemo\DBDemoConfig::init();

echo '<h1>WebExecuteSQL</h1>';

//\OLOG\Model\WebExecuteSQL::render(__DIR__ . '/../');

echo '<hr/>';

echo '<div><a href="/">reload</a></div>';

//
// CONSTANT MODELS TEST
//

echo '<h2>Const models <a href="/?a=add_constmodel">+</a></h2>';
echo '<div>Class name: <b>' . \PHPModelDemo\ConstTest::class . '</b></div>';

if (\OLOG\GET::optional('a') == 'insert') {
    /*
    $new_model = new \PHPModelDemo\ConstTest();
    $new_model->setTitle(rand(1, 1000));
    $new_model->save();
     * 
     */
}
