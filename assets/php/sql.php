<?php

/////////////////////////////////////////////////////
////////////////    QUERY        /////////////////////
#
# /*exibir api*/ print_r($api['functions']['sql']['query']);
#
function query($sql){
    #inclui _connecta
    // print_r($sql);
    // echo "\n";

    $con = new _conecta;

    #abre conexao
    $con->AbreConexao();

    #seta o valor query
    $Sel = mysql_query($sql) or die(mysql_error());

    #fecha a conexao
    $con->FechaConexao();

    #retorna query
    return $Sel;
}
////////////////    QUERY        /////////////////////
/////////////////////////////////////////////////////




/////////////////////////////////////////////////////
///////////////     SELECT      /////////////////////
#
# /*exibir api*/ print_r($api['functions']['sql']['select']);
#
function select($tabela, $campos, $regra){

    ////////////////////////////////////////
    #Seleciona o valor do nome dos campos da array $campos
    $arrCampo = array_keys($campos);
    
    #Seleciona o valor dentro dos campos da array $campos
    $arrValores = array_values($campos);

    #Contar a quantidade de campos array possui quanto aos [campos]
    $numCampo = count($arrCampo);

    #Contar a quantidade de campos array possui quanto aos [dados]
    $numValores = count($arrValores);

    ////////////////////////////

    #Seleciona o valor do nome dos campos da array $regra
    $arrRegraCampo = array_keys($regra);
    
    #Seleciona o valor dentro dos campos da array $regra
    $arrRegraValores = array_values($regra);

    #Contar a quantidade de campos array possui quanto aos [campos] de $campos
    $numRegraCampo = count($arrRegraCampo);

    #Contar a quantidade de campos array possui quanto aos [dados] de $campos
    $numRegraValores = count($arrRegraValores);
    ////////////////////////////////////////

    
    ///////////    EXECUTA //////////////////
    #valida quanto a quantidade de campos nas arrays
    if ($numCampo == $numValores && $numRegraCampo == $numRegraValores && $numCampo > '0') {

        //SELECT * FROM edicao WHERE ID='$id' 
        $sql = 'SELECT '; //delimita a acao

        foreach ($arrValores as $valores) {
            $sql .= $valores.', '; // os valores a serem resgatados
        }
            $sql = substr_replace($sql, ' ', -2, 1);

        $sql .= 'FROM '.$tabela.' '; // delimita a tabela

        for ($i='0'; $i < $numRegraCampo; $i++) {
            $sql .= $arrRegraCampo[$i].' '.$arrRegraValores[$i].' ';//regras;
        }


        ///////////
        #executa o query
        $sel = query($sql);
        #echo $sql;

        ///////////
        #tranformo sql em array
            $i = '0';
            while ($val = mysql_fetch_array($sel)) {
                $res[$i] = $val;

                $i = $i+'1';
            }

        ///////////
        #Valido se houve realmente um acrecimo de array
        if ($res['0']['0'] != '') {

            #conto quantas respostas houve
            $temp['res']['count'] = count($res);

            #confiro se houve mais de uma resposta
            switch($temp['res']['count']){

                #quando tem apenas uma resposta o objeto é colocado no index da array ($res[0][a] -> $res[a])
                case '1':                            
                    return $res = $res['0'];//retorna paneas uma resposta dentro da array
                break;

                #quando tem mais de uma resposta ele exibe normal ($res[0][a]; $res[1][a]; $res[2][a])
                default:
                    return $res;//retorna todas as respostas
                break;
            }
        }//Fim de quando houve resposta //if ($res['0']['0'] != '')

        #Retorno o valor Empty quando não houve respota
        else{
            return 'Empty';//Retorno o valor Empty
        }//Fim de #Valido se houve realmente um acrecimo de array //else ($res['0']['0'] == '')

    }//Fim de if ($numCampo == $numValores)

    else{
        return '
        Incompatibilidade com um dos campos que exigem arrays, veja a requisição deste objeto.
        <br>
        <a href="?api=functions->sql->select->param">Consulte a api</a>
        ';
    }
    ///////////    EXECUTA //////////////////

}//Fim de function select($tabela, $dados, $regra)
///////////////     SELECT      /////////////////////
/////////////////////////////////////////////////////




/////////////////////////////////////////////////////
///////////////     INSERIR      /////////////////////
#
# /*exibir api*/ print_r($api['functions']['sql']['insert']);
#
function insert($tabela, $dados){
    #pegar campos array
    $arrCampo = array_keys($dados);
    
    #Pega valores da arrays
    $arrValores = array_values($dados);

    #Contar campos da array
    $numCampo = count($arrCampo);

    #Contar os valores
    $numValores = count($arrValores);

    #Validacao
    if($numCampo == $numValores && $numCampo > '0'){
        #Seleciona todos os valores e campos
        $sql = 'INSERT INTO '.$tabela.' (';
            # Seleciona dentro de $sql todos os campos da tabela
            foreach ($arrCampo as $campo) {
                $sql .= '`'.$campo.'`, ';
            }
            $sql = substr_replace($sql, ') ', -2, 1);
        
            #Seleciona dentro de $sql todos os valores retornados
        $sql .= 'VALUES (';
            foreach ($arrValores as $valores) {
                $sql .= '\''.$valores.'\', ';
            }
            $sql = substr_replace($sql, ')', -2, 1);


            #recupera a funcao seleciona para retornar os valores
            query($sql);

    }else{
        echo '
        Incompatibilidade com um dos campos que exigem arrays, veja a requisição deste objeto.
        <br>
        <a href="?api=functions->sql->insert->param">Consulte a api</a>
        ';
    }

}//Fim de function inserir //inserir($tabela, $dados)

///////////////     INSERIR      /////////////////////
/////////////////////////////////////////////////////




/////////////////////////////////////////////////////
///////////////     UPDATE      /////////////////////
#
# /*exibir api*/ print_r($api['functions']['sql']['update']);
#
function update($tabela, $dados, $regra){
    #pegar campos array
    $arrCampo = array_keys($dados);
    
    #Pega valores da arrays
    $arrValores = array_values($dados);

    #Contar campos da array
    $numCampo = count($arrCampo);

    #Contar os valores
    $numValores = count($arrValores);


    #Validacao
    if($numCampo == $numValores && $tabela != '' && $numValores > '0'){
        #Seleciona todos os valores e campos
        $sql = 'UPDATE '.$tabela.' SET ';

            #Seleciona dentro de $sql todos os campos da tabela
            for ($i='0'; $i < $numCampo ; $i++) { 
                $sql .= '`'.$arrCampo[$i].'` = \''.addslashes($arrValores[$i]).'\', ';
            }
            $sql = substr_replace($sql, '', '-2', '1');

            # caso a regra seja uma array com valores variados
            if (is_array($regra)) {

                #Seleciona o valor do nome dos campos da array $regra
                $arrRegraCampo = array_keys($regra);
                
                #Seleciona o valor dentro dos campos da array $regra
                $arrRegraValores = array_values($regra);

                #Contar a quantidade de campos array possui quanto aos [campos] de $campos
                $numRegraCampo = count($arrRegraCampo);

                #Contar a quantidade de campos array possui quanto aos [dados] de $campos
                $numRegraValores = count($arrRegraValores);

                for ($i='0'; $i < $numRegraCampo; $i++) {
                    $sql .= $arrRegraCampo[$i].' '.$arrRegraValores[$i].' ';//regras;
                }
            }
            # caso a regra seja uma string com o valor de id
            else{
                $sql .= 'WHERE Id='.$id;
            }

            #recupera a funcao seleciona para retornar os valores
            query($sql);
            // print_r($sql);
 

    }else{
        echo '
        Incompatibilidade com um dos campos que exigem arrays, veja a requisição deste objeto.
        <br>
        <a href="?api=functions->sql->update->param">Consulte a api</a>
        ';
    }

}
///////////////     ALTEREAR      /////////////////////
/////////////////////////////////////////////////////


?>