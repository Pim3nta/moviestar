<?php

        require_once("globals.php");
        require_once("db.php");
        require_once("models/User.php");
        require_once("models/Message.php");
        require_once("dao/UserDAO.php");
            

        $message = new Message($BASE_URL);
        
        $userDao = new UserDao($conn, $BASE_URL);

    // Filtra o tipo de formulario que está chegando

    $type = filter_input(INPUT_POST, "type");

    //Valida o tipo de formulário

    if($type === "register")
    {
        //dados que chegaram do post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        // verificação se os dados minimos chegaram do post
        if($name && $lastname && $email && $password)
        {
            //validação se password está igual
            if($password === $confirmpassword){

                //verificar se o e-mail já está cadastrado no sistema, testar outro email.
                
                if($userDao->findByEmail($email) === false){
                    //se caso a função não encontrar o email, Iniciar cadastro de usuário,
                   // estanciando o objecto

                   $user = new User();
                    
                    //criação de token e senha
                    $userToken = $user->generateToken();
                    $finalPassword = $user->generatePassword($password);

                    $user->name = $name;
                    $user->lastname = $lastname;
                    $user->email = $email;
                    $user->password = $password;
                    $user->token = $userToken;

                    $auth = true;

                    //enviando o objeto user para a função create
                    $userDao->create($user, $auth);

                } else {
                    $message->setMessage("Usuario já cadastrado, tente outro e-mail","error","back");
                }

            }else{
                
                 $message->setMessage("As senhas não são iguais.","error","back");
            }

        }else{
            //Msg de erro, faltando dados dos inputs
            $message->setMessage("Por favor, preencha todos os campos.","error","back");
        
        }
    } else if($type === "login"){

        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        //tentar autenticar usuario
        if($userDao->authenticateUser($email, $password)){

            $message->setMessage("Seja Bem-vindo","success","editprofile.php");

        //direciona caso não conseguir autenticar
        } else{

            $message->setMessage("Usuario e/ou senha incorretos","error","back");
        }

    } else{
            $message->setMessage("Informações Invalidas","error","index.php");
    }



?>