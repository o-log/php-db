<?php

namespace OLOG\DB;

/**
* можно использовать одно подключение для нескольких
* объектов БД (если они смотрят на одну физическую базу) чтобы правильно 
* работали транзакции
 */
class ConnectorMySQL extends ConnectorPDO implements ConnectorInterface 
{


}