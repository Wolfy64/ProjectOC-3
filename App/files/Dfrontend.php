<?php

require_once 'Controllers/Page.php';

class Frontend extends Page
{
    public function __construct()
    {
    }

    /**
     * Built the index page by default
     */
    public function index()
    { 
        $this->template('Frontend/home');
    }

    /**
     * Built the page of Alaska book
     */
    public function alaska(array $parameters) // soit [] (/alaska), soit ['page', '1'] (/alaska/page/1)
    {
        $nb_params = count($parameters);


        if ($nb_params != 0 || $nb_params != 2)
          return new Exception("INVALID PARAMETERS");

        if ($nb_params == 0)
        {
          // listing
        }

        else 
        {
          $post_id = $parameters[1];
          // display individual post
        }




        // if ( !isset($_GET['post']) ){
        //     $data = $this->postManager->readAllPost();
        //     $this->template('Frontend/alaskaList', $data);

        // } elseif ( !is_numeric($_GET['post']) ){
        //     $this->template('Errors/404');

        // } else {
        //     $data = $this->postManager->read($_GET['post']);

        //     if ( $data === FALSE ){
        //         $this->template('Errors/404');
        //     } else {
        //         $this->template('Frontend/alaskaPost', $data);
        //     }
        // }
    }

    public function hasAndRegisterSubControler($sub_controler)
    {
        if ($sub_controler == "index") {
          $this->subControler = "index";
          return true;
        }

        if ($sub_controler == "alaska") {
          $this->subControler = "alaska";
          return true;
        }
    }

    public function execute($params)
    {
        if ($this->subControler == "index")
          $this->index();
        if ($this->subControler == "alaska")
          $this->alaska($params);
    }






















    /**
     * Built the Admin page
     */
    public function admin()
    {
        if( $this->connection->isAdmin() ){ // If True
            $this->adminPage();
        } else {
            $this->template('Frontend/connection');
        }
    }

    /**
     * Check that connection to the Admin page is allowed
     */
    public function connection()
    {
        if ( Utils::checkArray($_POST, ['user', 'password']) ) { // If True
            $user     = htmlspecialchars($_POST['user']);
            $password = htmlspecialchars($_POST['password']);
            
            $this->admin( $this->connection->verifyAccount($user, $password) );

        } else {
            $this->admin();
        }
    }

    /**
     * Destroy $_SESSION
     * @return Void
     */
    public function signOut()
    {
        session_destroy();
        $this->index();
    }

    /**
     * @param array from $_POST
     */
    public function addComment()
    {

        if ( Utils::checkArray($_POST['comment'], ['idBlogAlaska', 'author', 'contents']) ){

            $data = [];
            foreach ($_POST['comment'] as $key => $value) {
                $data += [$key => htmlspecialchars($value)];
            }

            $this->commentsManager->create( $comment = new Comments($data) );
            header('Location: /alaska?post=' . $data['idBlogAlaska']);
        } else {
            $this->template('Errors/404');
        }
    }

    /**
     * Report Comments
     * @return Void
     */
    public function report()
    {
        if ( Utils::checkArray($_POST, ['report', 'idBlogAlaska']) ){
            $idComment = intval($_POST['report']);
            $idPost    = intval($_POST['idBlogAlaska']);

            $this->commentsManager->report($idComment);
            header('Location: /alaska?post=' . $idPost);
  
        } else {
            $this->template('Errors/404');
        }
        
    }

    /**
     * Cancel comments reports 
     * @return Void
     */
    public function cancelReport()
    {
        if ( Utils::checkArray($_GET[], ['cancel']) ){
            $idComment = intval($_POST['cancel']);

            $this->commentsManager->cancelReport($idComment);
            // header('Location: /alaska?post=' . $idPost);
  
        } else {
            $this->template('Errors/404');
        }
        
    }

    /**
     * Built the admin page
     */
    private function adminPage()
    {
        if ( isset($_SESSION['admin']) === TRUE ){

            if ( isset($_GET['page']) ){
                $action = htmlspecialchars($_GET['page']);

                switch ($action) {
                    case 'new':
                        $this->template('Backend/new');
                        break;

                    case 'posts':
                        $data = $this->postManager->readAllPost();
                        $this->template('Backend/posts', $data);
                        break;

                    case 'report':
                        $data = $this->commentsManager->showReport();
                        $this->template('Backend/report', $data);
                        break;
                    
                    default:
                        $this->template('Errors/404');
                        break;
                }

            } else {
                $data = $this->commentsManager->reportCount();
                $this->template('Backend/admin', $data);
            }
        }
    }
}
