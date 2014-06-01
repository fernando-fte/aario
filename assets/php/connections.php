<?php
class _conecta{

    var $ip = 'localhost';
    var $user = 'root';
    var $pass = '';
    var $database = 'meubanco';

    function AbreConexao(){
        $this->conn = mysql_connect($this->ip, $this->user, $this->pass) or die ( '<h1>erro ao selecionar Banco de dados</h1>' );
        mysql_select_db($this->database, $this->conn) or die ( '<h1>erro ao selecionar Tabela</h1>' );

        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
    }

    function FechaConexao(){
        mysql_close($this->conn);
    }
}
/////////////////////////////////////////
///////Valor geral para conecta//////////
$conecta = new _conecta;
///////Valor geral para conecta//////////
/////////////////////////////////////////

?>