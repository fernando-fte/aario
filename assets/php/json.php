<?php

include 'connections.php';
include 'sql.php';
// include 'api.php';


# header('Content-Type:' . "text/plain");

#echo json_decode({"segmento":"page", "grupo":"home", "type":"jumbotron"});


#/*exibir api de select*/ print_r($api['functions']['sql']['select']);



# PADRAO DE VALORES A SER RECEBIDO DE SELECT #
# os campos são recebidos como array mesmo os dentro de {objects} #
// $post['table'] = "tabela";

// $post['select']['segmento'] = "page";
// $post['select']['grupo'] = "";
// $post['select']['type'] = "";

// $post['regra']['order'] = "index";
// $post['regra']['where'] = "LIKE%";
// $post['regra']['limit'] = "";
# controlando as regras


# PADRAO DE VALORES A SER RECEBIDO DE INSERT #
// $post['type'] = 'insert';
// $post['table'] = 'tabela';

// $post['select']['segmento'] = 'Segmento';
// $post['select']['grupo'] = 'Grupo';
// $post['select']['type'] = 'Type';

// $post['regra']['where'] = 'LIKE';
// $post['regra']['limit'] = '1';


# PADRAO DE VALORES A SER RECEBIDO DE UPDATE #
// $post['type'] = 'update';
// $post['table'] = 'tabela';

// $post['select']['segmento'] = 'Segmento';
// $post['select']['grupo'] = 'Grupo';
// $post['select']['type'] = 'Type';
// $post['select']['index'] = '1';

// $post['values']['nome'] = 'a';

// $post['regra']['where'] = 'LIKE';
// $post['regra']['limit'] = '1';

// $post['regra']['order']['to'] = 'index';
// $post['regra']['order']['by'] = 'ASC';







# defino na string post o $_POST
$post = $_POST;

// print_r($post);

### verificando e validando regra ###
if (!array_key_exists("regra", $post)) {
    $post['regra']['where'] = "LIKE";
    $post['regra']['limit'] = "1";
    $post['regra']['order']['to'] = "index";
    $post['regra']['order']['by'] = "ASC";
}else{
    #valida where
    if(!array_key_exists("where", $post['regra'])){
        $post['regra']['where'] = "LIKE";
    }
    if(!array_key_exists("order", $post['regra'])){
        $post['regra']['order']['to'] = "index";
        $post['regra']['order']['by'] = "ASC";
    }
    if(!array_key_exists("limit", $post['regra'])){
        $post['regra']['limit'] = "1";
    }

    if(!array_key_exists("to", $post['regra']['order'])){
        $post['regra']['order']['to'] = "index";
    }

    if(!array_key_exists("by", $post['regra']['order'])){
        $post['regra']['order']['by'] = "ASC";
    }
}
### verificando e validando outros parametros ###
if(!array_key_exists("status", $post)){ $post['status'] = false; }

# defino em $post o post
// $post = $_POST;
# valido e confiro se existe um tipo de conexao
if (array_key_exists('type', $post)) {

    # verifico a conexao e chamo os arquivos
    switch ($post['type']) {

        #quando o tipo for atualização de tabela
        case 'update':

            # repasso as regras
            $temp['regra'] = f_json_where($post); # processo a regra
            $temp['tabela'] = '`'.$post['table'].'`'; # tabela
            $temp['campos'] = array('' => '*'); # campos selecinados

            # capturo o select
            $temp['select'] = select($temp['tabela'], $temp['campos'], $temp['regra']);

            # mapeia o object para array
            $temp['return'] = (json_decode($temp['select']['values'], true));

            # mescla o resultado do banco de dados e o valor recebido por post
            # a key array "values" é o nome da coluna
            $temp['values'] = array_merge($temp['return'], $post['values']);

            # copilo values para object
            $temp['dados']['values'] = json_encode($temp['values']);


            # defino em temp['campos'] para ordenar, acrecentando o values da ultima consulta
            unset($temp['campos']);
            $temp['campos']['index'] = $post['select']['index'];
            $temp['campos']['segmento'] = $post['select']['segmento'];
            $temp['campos']['grupo'] = $post['select']['grupo'];
            $temp['campos']['type'] = $post['select']['type'];
            $temp['campos']['values'] = addslashes($temp['select']['values']); # seleciono valor pre-selecionado

            # defino que regra where é "="
            $post['regra']['where'] = "=";

            # retorno os valores ao post->select
            $post['select'] = $temp['campos'];

            # construo as regras
            $temp['regra'] = f_json_where($post);

            # envio para update
            update($temp['tabela'], $temp['dados'], $temp['regra']);

            $return = '[{"update":"true"}]';

            // # TESTE #
            // # Depois de inserido a validação
            // unset($post['select']['values']);
            // $temp['sub']['regra'] = f_json_where($post); # processo a regra
            // $temp['sub']['tabela'] = '`'.$post['table'].'`'; # tabela
            // $temp['sub']['campos'] = array('' => '*'); # campos selecinados

            // # capturo o select
            // $temp['sub']['select'] = select($temp['sub']['tabela'], $temp['sub']['campos'], $temp['sub']['regra']);

            // $return = '[{"update":"erro","return":"'. $temp['sub']['select']['values'].'"}]';  


            # retorno ao json
            echo $return;
            // stripslashes( // remove as alternativas
            break;

        #quando for insert
        case 'insert':
            ## 1ª ETAPA ##
            # defino o tipo de order
            $post['regra']['order']['by'] = "DESC";

            # repasso as regras
            $temp['regra'] = f_json_where($post); # processo a regra
            $temp['tabela'] = '`'.$post['table'].'`'; # tabela
            $temp['campos'] = array('' => '*'); # campos selecinados

            # capturo o select
            $temp['select'] = select($temp['tabela'], $temp['campos'], $temp['regra']);

            
            ## 2ª ETAPA ##
            # acrecento valor zero para index quando vazio
            if ($temp['select'] == 'Empty') { 
                $post['select']['index'] = 1; 
            }
            # confiro se existe index e se é vazio
            else if (array_key_exists('index', $temp['select'])) {
                # defino que o valor inserido deve ser maior que o anterior dentro de index
                $post['select']['index'] = $temp['select']['index']+'1';
            }

            # defino em values a etiqueta status com o valor desabled
            $post['select']['values'] = '{"status":"Inativo", "template-toogle":{"status":"Ativo"}}';



            ## 3ª ETAPA ##
            # defino em temp['campos'] para ordenar 
            unset($temp['campos']);
            $temp['campos']['index'] = $post['select']['index'];
            $temp['campos']['segmento'] = $post['select']['segmento'];
            $temp['campos']['grupo'] = $post['select']['grupo'];
            $temp['campos']['type'] = $post['select']['type'];
            $temp['campos']['values'] = $post['select']['values'];


            # insiro os valores
            insert($temp['tabela'], $temp['campos']);


            ## 4ª ETAPA ##
            # acrecenta em $return os valores para json
            $return = '[{"index":"'.$temp['campos']['index'].'", "status":"desabled", "type":"update"}]';

            # retorno ao json
            echo $return;

            # zero temp;
            unset($temp);
            break;

        #quando for selecionar o banco
        case 'select':
            $temp['regra'] = f_json_where($post); # processo a regra
            $temp['tabela'] = '`'.$post['table'].'`'; # tabela
            $temp['campos'] = array('' => '*'); # campos selecinados

            # faz chamada de select
            $temp['select'] = select($temp['tabela'], $temp['campos'], $temp['regra']);

            # seleciona values em return, e insere no object #
            $return = '['.f_json_values($temp['select']).']';

 
             # Exibo caso status seja valido
            if(f_valida_status($return) == true){
                #exibe quando for verdadeiro
                echo $return;
            }

            # valio caso seja uma exibição para push=update
            if($post['status']){
                # verifico o tipo de solicitação
                switch ($post['status']) {
                    case 'update': # quando for upate
                        #exibe todos menos os que forão excluidos  indiretamente
                        if(f_valida_status($return) != 'excluded'){ echo $return; }
                        break;

                    case 'recover': # quando necessario exibir para recuperar
                        # retorno quando for do tipo removido
                        if(f_valida_status($return) == 'excluded'){ echo $return; }
                        break;
                }
            } 

            break;
    }
}


##################################################
################### FUNÇÕES ######################
##################################################

######### transforma object em array #############
function f_object_to_array( $object ){
   if( !is_object( $object ) && !is_array( $object ) ) { /*retorna resultado*/ return $object; }

   if( is_object( $object ) ) { $object = get_object_vars(  /*retorna resultado*/ $object );}
   
   #retorna resultado
   return array_map( 'objectToArray', $object );
}
######### transforma object em array #############

##################################################

####### verifica se esta recebendo select ########
function f_json_where($post){
    #$temp['count'] = 0;
    if (array_key_exists("select", $post)) {
        #Seleciona o valor do nome dos campos de $post['select']
        $temp['key'] = array_keys($post['select']);
        $temp['key']['count'] = count($temp['key']); # conto quanto valores

        #Seleciona o valor dentro dos campos de $post['select']
        $temp['val'] = array_values($post['select']);
        $temp['val']['count'] = count($temp['val']); # conto quanto valores


        # Laço para atribuir os valores de where #
        for ($temp['count']=0; $temp['count'] < $temp['val']['count']; $temp['count']++) {

            #verifico o tipo de seleção no were
            if ($post['regra']['where'] == "LIKE" or $post['regra']['where'] == "=") {
                # construo where
                if($temp['count'] <= 0) {
                    #quando a primeira regra
                    $temp['where'] = '`'.$temp['key'][$temp['count']].'` '.$post['regra']['where'].' \''.$temp['val'][$temp['count']].'\' ';
                }else{
                    #quando mais de uma regra
                    $temp['where'] = $temp['where'] .'AND `'.$temp['key'][$temp['count']].'` '.$post['regra']['where'].' \''.$temp['val'][$temp['count']].'\' ';
                }
            }

            else if ($post['regra']['where'] == "LIKE%") {
                # construo where
                if($temp['count'] <= 0) {
                    #quando a primeira regra
                    $temp['where'] = '`'.$temp['key'][$temp['count']].'` LIKE \'%'.$temp['val'][$temp['count']].'%\' ';
                }else{
                    #quando mais de uma regra
                    $temp['where'] = $temp['where'] .'AND `'.$temp['key'][$temp['count']].'` LIKE \'%'.$temp['val'][$temp['count']].'%\' ';
                }
            }
        } #final do laço

        # defino regra 
        $regra['WHERE '] =  $temp['where'];
        $regra['ORDER BY '] =  '`'.$post['regra']['order']['to'].'` '.$post['regra']['order']['by'].'';
        # defino limite dentro de regra
        if ($post['regra']['limit'] > '0') {
            $regra['LIMIT '] =  $post['regra']['limit'];        
        }

        # apago temp
        unset($temp);

        #retorno regra para a função
        return $regra;
    }
}
####### verifica se esta recebendo select ########


##################################################

######### processar values de select_db ##########
function f_json_values($select){
    # repasso os valores em uma string todo o $post

    ## verifico quantas respostas e seleciona apeneas values ##
    # caso values esteja na raiz da array
    if (array_key_exists("values", $select)) {
        $return = $select['values']; #exibe
    }

    # caso exista uma lista de resultados
    else{
        # conta quantas ocorrencias
        $temp['count'] = count($select);

        # seleciono cada sequancia
        for ($temp['some']=0; $temp['some'] < $temp['count']; $temp['some']++) {

            # trabalha o resultado de res
            if($temp['some'] <= 0) {

                # preenche quando em zero
               $temp['return'] = $select[$temp['some']]['values'];
            }else{

                # adiciona quando maior que zero
                $temp['return'] = $temp['return'] .", ". $select[$temp['some']]['values'];
            }

            # aplico em return
            $return = $temp['return'];
        }
    }

    # retoorna a função
    return $return;
}
######### processar values de select_db ##########

##################################################

################ verifica valido #################
function f_valida_status ($return) {
    # recebo object{} po $return

    # transforma em array
    $return = (json_decode($return, true));

    # Verifica se existe status
    if(array_key_exists('status', $return['0'])){
        # converto para smal case o texto solicitado
        $return['0']['status'] = strtolower($return['0']['status']);

        switch ($return['0']['status']) {
            # caso seja ativo
            case 'active':
                return true;
                break;
            case 'actived':
                return true;
                break;
            case 'ativo':
                return true;
                break;
            case 'desativo':
                return true;
                break;

            # caso seja desativado retorna falso
            case 'dasebled':
                return false;
                break;
            case 'desable':
                return false;
                break;
            case 'inative':
                return false;
                break;
            case 'inativo':
                return false;
                break;
            case 'desativo':
                return false;
                break;

            # caso seja removido retorna falso
            case 'exclude':
                return 'excluded';
                break;
            case 'removed':
                return 'excluded';
                break;
            case 'excluido':
                return 'excluded';
                break;
        }
    }

    # Caso não tenha status passa como valido
    else{
        return true;
    }
}
################ verifica valido #################
##################################################


?>
