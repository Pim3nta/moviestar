<?php 

    //Classe de User para a manupulação da tabela usuário

    class User{

        public $id;
        public $name;
        public $lastname;
        public $email;
        public $password;
        public $image;
        public $bio;
        public $token;

        public function getFullName($user){
            return $user->name . " " . $user->lastname;
        }

        public function generateToken(){
            //função especifica da model e tem a finalidade de retornar uma hash de 50 caraceteres
            return bin2hex(random_bytes(50));
        }

        public function generatePassword($password){
        return password_hash($password, PASSWORD_DEFAULT);

        }
        //Randonizar o arquivo
        public function imageGenerateName(){
            return bin2hex(random_bytes(60)) . ".jpg"; 
        }

    }

    //Metodos
    interface UserDAOInterface {
        public function buildUser($data);
        //Criando Usuario, sem autenticação
        public function create(User $user, $authUser = false);

        public function update(User $user, $redirect = true);
        //Função de Validação de Token = restrição da pagina
        public function verifyToken($protected = false);
        //login
        public function setTokenToSession($token, $redirect = true);
        //Autenticação completa
        public function authenticateUser($email, $password);
        //pesquisar token
        public function findByToken($token);
        //Encontrar usuario por email
        public function findByEmail($email);
        // Encontrar usuario por id
        public function findById($id);
        // função de logout
        public function destroyToken();
        // Troca de senha
        public function changePassword(User $user);
    }