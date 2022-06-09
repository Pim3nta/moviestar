<?php

    class Message{

        private $url;

        public function __construct($url)
        {
            $this->url = $url;
        }

        // setando a Mensagem, vai receber a mensage, tipo[Error, Sucesso], redirect[Url que será direcionada]
        public function setMessage($msg, $type, $redirect = "index.php"){

                $_SESSION["msg"] = $msg;
                $_SESSION["type"] = $type;

            if($redirect != "back"){
                //Se for diferente de back, direcionar para index
                header("Location: $this->url" . $redirect);
            } else {
                //se o redirect for back, voltar uma pagina com o objeto HTTP_REFERER
                header("Location: " . $_SERVER["HTTP_REFERER"]);
            }
        }
        public function getMessage(){

        if(!empty($_SESSION["msg"])){
            return [
                "msg" => $_SESSION["msg"],
                "type" => $_SESSION["type"]
            ];}
            else{
                return false;
            }
        }

        public function clearMessage(){
                $_SESSION["msg"] = "";
                $_SESSION["type"] = "";
        }
    }



?>