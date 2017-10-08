<?php

require_once '../vendor/autoload.php';

\DBDemo\DBDemoConfig::init();

echo '<h1>DB test</h1>';
echo '<hr/>';

echo '<div><a href="/">reload</a></div>';

echo '<h2>Insert <a href="/?a=insert">+</a></h2>';

if (\OLOG\GET::optional('a') == 'insert') {
    \OLOG\DB\DB::query(\DBDemo\DBDemoConfig::SPACE, 'insert into test (title) values (?)', [rand(0, 1000)]);
}

$data_arr = \OLOG\DB\DB::readColumn(\DBDemo\DBDemoConfig::SPACE, 'select id from test');

foreach ($data_arr as $id){
    echo '<p>' . $id . '</p>';
}