<?php

        require_once("globals.php");
        require_once("db.php");
        require_once("models/User.php");
        require_once("models/Message.php");
        require_once("dao/UserDAO.php");
            

        $message = new Message($BASE_URL);
        
        $userDao = new UserDao($conn, $BASE_URL);
        //resgatar o tipo do formulario
        $type = filter_input(INPUT_POST, "type");

        //validacoes de input
        if($type === "update"){

            //Retorna os dados do usuario
            $userData = $userDao->verifyToken();
            //receber dados do post
            $name = filter_input(INPUT_POST, "name");
            $lastname = filter_input(INPUT_POST, "lastname");
            $email = filter_input(INPUT_POST, "email");
            $bio = filter_input(INPUT_POST, "bio");

            //Criar novo Objeto para salvar
            $user = new User();

            //preencher os dados do usuario
            $userData->name = $name;
            $userData->lastname = $lastname;
            $userData->email = $email;
            $userData->bio = $bio;

            // Upload da imagem
            //verifica se o input do front está enviando o id "image"
            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])){

                $image = $_FILES["image"];
                //valida a extensão do envio
                $imageTypes = ["image/jpeg", "image/jpg","image/png"];
                $jpgArray = ["image/jpeg", "image/jpg"];

                //Analisa o tipo de imagem
                if(in_array($image["type"], $imageTypes)){

                    //Checar se jpg
                    if(in_array($image, $jpgArray)){
                        $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                    //imagem é png
                    }else{
                        $imageFile = imagecreatefrompng($image["tmp_name"]);
                    }
                    
                    $imageName = $user->imageGenerateName();

                    imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                    $userData->image = $imageName;

                }else{
                    $message->setMessage("Tipo Inválido de extensão de imagem!","error","back");
                }
            }

            $UserDAO->update($userData);
            
            //Atualizar Senha do usuario
        }else if($type === "changepassword"){

            $password = filter_input(INPUT_POST, "password");
            $confirmpassword = filter_input(INPUT_POST, "confirmpassword");
            //Retorna os dados do usuario
            $userData = $userDao->verifyToken();

            $id = $userData->id;

            if($password == $confirmpassword){

                $user = new User();

                $finalPassword = $user->generatePassword($password);
                
                $user->password = $finalPassword;
                $user->id = $id;

                $userDao->changePassword($user);
            }else{
                $message->setMessage("As senhas não são iguais!","error","back");
            }

        }else{
         $message->setMessage("Informações Invalidas","error","index.php");
        }