<?php

    require_once("models/User.php");
    require_once("models/Message.php");


    class UserDAO implements UserDAOInterface{

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url)
        {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        // Retornos da build
        public function buildUser($data){

            $user = new User();

            $user->id = $data["id"];
            $user->name = $data["name"];
            $user->lastname = $data["lastname"];
            $user->email = $data["email"];
            $user->password = $data["password"];
            $user->image = $data["image"];
            $user->bio = $data["bio"];
            $user->token = $data["token"];
            
            return $user;
            
        }
        //Criando Usuario, sem autenticação
        public function create(User $user, $authUser = false){
        
        $stmt = $this->conn->prepare("INSERT INTO users(
            name, lastname, email, password, token
            ) VALUES (
                :name, :lastname, :email, :password, :token
            )");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":token", $user->token);

            $stmt->execute();

            // Autenticar usuario caso auth seja true
            if($authUser){
                $this->setTokenToSession($user->token);
            }
        }

        public function update(User $user, $redirect = true){

            $stmt = $this->conn->prepare("UPDATE users SET
            name = :name,
            lastname = :lastname,
            email = :email,
            image = :image,
            bio = :bio,
            token = :token,
            WHERE id = :id
            ");
            
            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":image", $user->image);
            $stmt->bindParam(":bio", $user->bio);
            $stmt->bindParam(":token", $user->token);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            if($redirect){
            //Redireciona para o perfil do usuario
            $this->message->setMessage("Dados Atualizados Com sucesso","success","editprofile.php");
            }

        }
        //Função de Validação de Token = restrição da pagina
        public function verifyToken($protected = false){

            //verifica se a SESSION[Token] está vazia
            if(!empty($_SESSION["token"])){
                // Pega o token da session
                $token = $_SESSION["token"];

                $user = $this->findByToken($token);

                if($user){
                    return $user;
                } elseif($protected){
                    //redireciona o usuario nao autenticado
                     $this->message->setMessage("Faça login para acessar a pagina","error","index.php");
                }

            } else if($protected){
                 // Redireciona usuário não autenticado
                 $this->message->setMessage("Faça a autenticação para acessar esta página!", "error", "index.php");

            }
        }
        //login
        public function setTokenToSession($token, $redirect = true){
            //salvar token na session
        $_SESSION["token"] = $token;

        if($redirect){
            //Redireciona para o perfil do usuario
            $this->message->setMessage("Seja bem-vindo","success","editprofile.php");
            }
        }
        //pesquisar token
        public function findByToken($token){
                
            if($token != ""){

                $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");

                $stmt->bindParam(":token", $token);

                $stmt->execute();
                //se encontrar
                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                }else{
                    return false;
                }

            }else{
                return false;
            }
        }
        //Autenticação completa
        public function authenticateUser($email, $password){
            $user = $this->findByEmail($email);

            if($user){

                //checar se as senhas batem, função do php
                if(password_verify($password, $user->password)){

                    //Gerar um token e inserir na session
                    $token = $user->generateToken();

                    $this->setTokenToSession($token, false);

                    //Atualizar o token no usuario

                    $user->token = $token;

                    $this->update($user, false);

                    return true;
                }else{
                    return false;
                }
            } else {
                return false;
            }
        }

        //Encontrar usuario por email
        public function findByEmail($email){

            if($email != ""){

                $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");

                $stmt->bindParam(":email", $email);

                $stmt->execute();
                //se encontrar
                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                }else{
                    return false;
                }

            }else{
                return false;
            }

        }
        // Encontrar usuario por id
        public function findById($id){
            
      if($id != "") {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        if($stmt->rowCount() > 0) {

          $data = $stmt->fetch();
          $user = $this->buildUser($data);
          
          return $user;

        } else {
          return false;
        }

      } else {
        return false;
      }
    }

        public function destroyToken(){
            // Remove token da session
            $_SESSION["token"] = "";

            //rediciona e apresenta mensagem de sucesso
             $this->message->setMessage("Você fez o Logout com sucesso","success","index.php");
            
        }
        // Troca de senha
        public function changePassword(User $user){
            //recebendo um objeto user

            $stmt = $this->conn->prepare("UPDATE users SET
            password = :password
            WHERE id = :id
            ");

        $stmt->bindParam("password", $user->password);
        $stmt->bindParam(":id", $user->id);

        $stmt->execute();

               //rediciona e apresenta mensagem de sucesso
             $this->message->setMessage("Senha alterada com sucesso","success","editprofile.php");
            

        }
    }
?>