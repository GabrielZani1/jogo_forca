<?php
const PLAYER_UM = 1;
const PLAYER_DOIS = 2;

iniciaJogoForca();

function iniciaJogoForca() {    
    while(true) {          
        $palavra_gerada = gerarPalavra();
        $palavra_formada = mascaraPalavra($palavra_gerada['palavra']);
        $palavra_array = gerarPalavraArray($palavra_gerada['palavra']); 
        $palavras_ditas = [];       
        $letra = "";    
        $palavra_final = "";
        $cad_grupo = "";
        $cad_palavra = "";        
        $opcao_menu = 0;
        $ret_cad_grupo = 0;
        $ret_cad_palavra = 0;                     
        $vida_play_1 = 7;
        $vida_play_2 = 7;
        $player = PLAYER_UM;  

        system('clear');                
        echo "BEM VINDO AO JOGO DA FORCA!\nPara iniciar a partida digite '1'\nPara cadastrar uma palavra digite '2'\nPara cadastrar um grupo de palavra digite '3'\nPara fechar o jogo precione qualquer tecla\n";
        $opcao_menu = (int) readline(""); 

        if ($opcao_menu == 1) {    
            while ($palavra_formada != $palavra_gerada['palavra']) {                                                   
                $palavra_final = $palavra_formada;
                $palavra_formada = ""; 
                $contar_erro = 1; 

                if ($player == PLAYER_UM) {                    
                    $vida = $vida_play_1;                    
                } else {                    
                    $vida = $vida_play_2;                    
                }
                if ($vida_play_1 > 0 || $vida_play_2 > 0) {
                    system('clear');            
                    echo "-------------------------------------------------------------------------------------\n";
                    echo "Total de vidas: ".$vida."/7 || Dica da partida: ".$palavra_gerada['dica']." || Palavras já ditas: ";
                    foreach ($palavras_ditas as $pd) {
                        echo $pd;
                    }
                    echo "\n-------------------------------------------------------------------------------------\n"; 
                    echo "VEZ DO JOGADOR ".$player."!\n\n\n";
                    echo " ".$palavra_final;             
                    desenhoForca($vida);

                    $letra = mb_strToUpper(readline("Escolha uma letra: "));                                 
                    $palavras_ditas[] = $letra;            

                    foreach ($palavra_array as $pg) {
                        if (retiraCaracterEspecial($pg) == $letra) {
                            $contar_erro = 0;
                        }
                        $marcar = 0;
                        foreach ($palavras_ditas as $pd) {
                            if (retiraCaracterEspecial($pg) == $pd) {
                                $marcar = 1;                
                            }
                        }
                
                        if ($marcar) {            
                            $palavra_formada .= $pg;                               
                        } else {
                            if ($pg == " "){                
                                $palavra_formada .= " ";                        
                            } else {                
                                $palavra_formada .= "_";                        
                            }
                        }              
                    }

                    if ($contar_erro) {
                        if ($player == PLAYER_UM) {
                            $vida_play_1 --;
                            $player = PLAYER_DOIS;                                                      
                        } else {
                            $vida_play_2 --;
                            $player = PLAYER_UM;                      
                        }                        
                    }                      
                    $ganhou = 1;                                      
                } else {
                    system('clear');                    
                    echo "-------------------------------------------------------------------------------------\n";
                    echo "|                       Gamer Over :( 'Jogador 1 e Jogador 2'                       |\n";
                    echo "-------------------------------------------------------------------------------------\n";
                    echo "|Você perdeu, a palavra gerada era: ".$palavra_gerada['palavra']."                   \n"; 
                    echo "-------------------------------------------------------------------------------------\n\n";
                    $ganhou = 0;

                    readline("Para voltar ao Menu Principal precione qualquer tecla! ");
                    break;
                }                     
            } 

            if ($ganhou) {
                system('clear');                                
                echo "Parabéns Jogador ".$player." Você Ganhou :)\n";
                echo "A palavra gerada era: ".$palavra_gerada['palavra']."\n";
                readline("Para voltar ao Menu Principal precione qualquer tecla! ");                
            }
        } elseif ($opcao_menu == 2) {            
            while (empty($cad_palavra) && !$ret_cad_palavra) {
                system('clear');
                echo "|---------------------------------------------------------|\n";
                echo "|                    Cadastrar Palavra                    |\n";
                echo "|---------------------------------------------------------|\n";    
                $cad_palavra = mb_strToUpper(readline("Informe uma palavra para cadastrar: "));  
                
                if (!empty($cad_palavra)) {
                    if (!validaPalavra($cad_palavra)) {
                        listaGrupo();
                        $tipo_grupo = mb_strToUpper(readline("Informe um Grupo para esta palavra: "));
                        
                        if (!empty($tipo_grupo)) {
                            cadastrarPalavra($cad_palavra, $tipo_grupo);

                            system('clear');
                            echo "Palavra cadastrado com sucesso!\n";
                            readline("Para voltar ao Menu Principal precione qualquer tecla! ");
                            $ret_cad_palavra = 1;                                                       
                        } 
                    } else {
                        system('clear');
                        echo "Erro ao cadastrar uma Palavra!\nJá existe ".$cad_palavra." cadastrado.\n";
                        readline(""); 
                        $cad_palavra = "";
                        $ret_cad_palavra = 0;                          
                    }           
                }
            }
        } elseif ($opcao_menu == 3) {
            while (empty($cad_grupo) && !$ret_cad_grupo) {
                system('clear');
                echo "|---------------------------------------------------------|\n";
                echo "|                     Cadastrar Grupo                     |\n";
                echo "|---------------------------------------------------------|\n";    
                $cad_grupo = mb_strToUpper(readline("Informe um tipo de grupo para cadastrar: "));   
                
                if (!empty($cad_grupo)) {
                    $ret_cad_grupo = cadastrarGrupo($cad_grupo);

                    if (!$ret_cad_grupo) {
                        system('clear');
                        echo "Erro ao cadastrar o Grupo!\nJá existe ".$cad_grupo." cadastrado.\n";
                        readline(""); 
                        $cad_grupo = "";                          
                    } else {
                        system('clear');
                        echo "Grupo cadastrado com sucesso!\n";
                        readline("Para voltar ao Menu Principal precione qualquer tecla! ");                         
                    }
                }
            }
        } else {
            system('clear');
            break;
        }    
    }
}

function cadastrarPalavra($palavra, $tipo_grupo) {
    $arq_grava = fopen("tabela_palavras.txt", "a");
    
    fwrite($arq_grava, $palavra."|".$tipo_grupo."\n");                
    fclose($arq_grava);
}

function validaPalavra($palavra_cad) {
    $reg_palavra = consultaPalavraGerada();
    $bool = 0;

    foreach ($reg_palavra as $rp) {
        if ($rp['palavra'] == $palavra_cad) {
            $bool = 1;
        }
    }
    return $bool;
}

function consultaPalavraGerada() {      
    $registros = [];

    if (file_exists("tabela_palavras.txt")) {
        $arquivo = fopen("tabela_palavras.txt", "r");

        while (($data = fgetcsv($arquivo, 0, "|")) != false) {                           
            $registros[] = 
            [
                'palavra' => $data[0],
                'dica' => $data[1]                
            ];
        }
        fclose($arquivo);              
    }
    return $registros;
}

function gerarPalavra() {
    $palavra = consultaPalavraGerada();
    $posicao = rand(0, count($palavra) -1);
    
    return $palavra[$posicao];    
}

function cadastrarGrupo($tipo_grupo){
    $arq_grav = fopen("grupo.txt", "a");
    $bool = 0; 
    
    if (!validaGrupo($tipo_grupo)) {
        fwrite($arq_grav, $tipo_grupo."|\n");        
        fclose($arq_grav);
        $bool = 1;
    } 
    return $bool;
}

function validaGrupo($cad_grupo) {
    $reg_grupos = consultaGrupo();
    $bool = 0;

    foreach ($reg_grupos as $rg) {
        if ($rg['grupo'] == $cad_grupo) {            
            $bool = 1;
        }       
    }
    return $bool;
}

function listaGrupo() {
    $reg_grupos = consultaGrupo();
    array_multisort(array_column($reg_grupos, 'grupo'), SORT_ASC, $reg_grupos); 

    echo "\n\n\n\n|---------------------------------------------------------|\n";
    echo "|                    Grupos Cadastrados                   |\n";
    echo "|---------------------------------------------------------|\n";
    foreach ($reg_grupos as $rg) {
        echo "|".str_pad($rg['grupo'], 57, " ")."\n";
    }
    echo "|---------------------------------------------------------|\n";
}

function consultaGrupo() {
    $registros = [];

    if (file_exists("grupo.txt")) {
        $arquivo = fopen("grupo.txt", "r");

        while (($data = fgetcsv($arquivo, 0, "|")) != false) {                           
            $registros[] = 
            [
                'grupo' => $data[0]                
            ];
        }
        fclose($arquivo);              
    }
    return $registros;
}

function gerarPalavraArray($palavra) {
    $inicio = 0;    
    $palavra_array = [];

    while ($inicio < mb_strLen($palavra, 'UTF-8')) {
        $letra = mb_subStr($palavra, $inicio, 1, 'UTF-8');

        if ($letra == " ") {
            $palavra_array[] = " ";
            $inicio ++;
        } else {
            $palavra_array[] = $letra;
            $inicio ++;
        }    
    }      
    return $palavra_array;   
}

function mascaraPalavra($palavra) {
    $inicio = 0;    
    $palavra_mascara = "";

    while ($inicio <= mb_strLen($palavra, 'UTF-8')) {
        $letra = mb_subStr($palavra, $inicio, 1, 'UTF-8');

        if ($letra == " ") {
            $palavra_mascara .= " ";
            $inicio ++;
        } else {
            $palavra_mascara .= str_replace($letra, "_", $letra);        
            $inicio ++;
        }    
    }      
    return $palavra_mascara;   
}

function retiraCaracterEspecial($palavra) {
    $from = "ÁÀÃÂÉÊÈÌÍÒÓÔÕÚÜÇ";
    $to = "AAAAEEEIIOOOOUUC";
    $keys = [];
    $values = [];
    
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);

    return strtr($palavra, $mapping);
}

function desenhoForca($vida_jogador) {
    switch ($vida_jogador) {
        case 7:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                __|__     \n";
            echo " |              /|     |\   \n";
            echo " |             / |     | \  \n";
            echo " |            /  |     |  \ \n";
            echo " |               |_____|    \n";
            echo " |               ||   ||    \n";
            echo " |               ||   ||    \n";
            echo " |               ||   ||    \n";
            echo " |               d     b    \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 6:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                __|__     \n";
            echo " |              /|     |\   \n";
            echo " |             / |     | \  \n";
            echo " |            /  |     |  \ \n";
            echo " |               |_____|    \n";
            echo " |                    ||    \n";
            echo " |                    ||    \n";
            echo " |                    ||    \n";
            echo " |                     b    \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 5:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                __|__     \n";
            echo " |              /|     |\   \n";
            echo " |             / |     | \  \n";
            echo " |            /  |     |  \ \n";
            echo " |               |_____|    \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 4:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                __|__     \n";
            echo " |               |     |\   \n";
            echo " |               |     | \  \n";
            echo " |               |     |  \ \n";
            echo " |               |_____|    \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 3:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                __|__     \n";
            echo " |               |     |    \n";
            echo " |               |     |    \n";
            echo " |               |     |    \n";
            echo " |               |_____|    \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 2:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (°_°)     \n";
            echo " |                  |       \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
        case 1:
            echo "\n\n";
            echo " |------------------|       \n";
            echo " |                  |       \n";
            echo " |                 _|_      \n";
            echo " |                (X_X)     \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo " |                          \n";
            echo "_|__________________        \n";
            break;
    }
}

?>

