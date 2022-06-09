<?php

    class Movie {
        public $id;
        public $title;
        public $description;
        public $image;
        public $trailer;
        public $category;
        public $length;
        public $user_id;

        //Randonizar o arquivo
        public function imageGenerateName(){
            return bin2hex(random_bytes(60)) . ".jpg"; 
        }
    }

    interface MovieDAOInterface {

        // Semelhante ao User, retorna o objeto datamovie
        public function buildMovie($data);
        //Retorna todos os filmes
        public function findAll();
        //Vai selecionar os ulitimos filmes
        public function getLatestMovies();
        // selecionar o filme de acordo com a categoria
        public function getMoviesByCategory($category);
        // seleciona o filme pelo id
        public function getMoviesByUserId($id);
        public function findById($id);
        public function findByTitle($title);
        public function create(Movie $movie);
        public function update(Movie $movie);
        public function destroy($id);

    }