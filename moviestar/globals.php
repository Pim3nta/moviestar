    <?php
    //Fiquei Horas para descobrir que esse session deveria está aqui
    session_start();
    /* DEVE SERMPRE SER CHAMADO EM TODOS OS HIPERLINKS, PARA O FUNCIONAMENTO DE DIRETÓRIO DENTRO DO SERVER*/
    $BASE_URL = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]."?") . "/";

    