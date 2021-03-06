<?php

require_once '../Controllers/Page.php';

class Backend extends Page
{
    public function __construct(Router $router, string $methodName)
    {
        parent::__construct($router, $methodName);
        $this->connectAdmin($methodName);
    }

// METHODS PAGES

    /**
     * Built the admin home page
     */
    public function home()
    {
        $data = ['reportCount'  => $this->commentManager->reportCount(),
                 'commentCount' => $this->commentManager->commentCount(),
                 'postCount'    => $this->postManager->postCount()
                ];

        $this->template('Backend/home', $data);
    }

    /**
     * Built the admin manageReport page
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "manageReport"
     * @return Void
     */
    public function manageReport()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'manageReport' ) {
            $this->template('Errors/404');

        } else {
            $data = $this->commentManager->readAllReport();
            $this->template('Backend/manageReport', $data);           
        }
    }

    /**
     * Built the admin manageReport page
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "managePost"
     * @return Void
     */
    public function managePost()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'managePost' ) {
            $this->template('Errors/404');

        } else {
            $data = $this->postManager->readAllPost();
            $this->template('Backend/managePost', $data);           
        }
    }

    /**
     * Built the admin manageComment page
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "manageComment"
     * @return Void
     */
    public function manageComment()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'manageComment' ) {
            $this->template('Errors/404');

        } else {
            $data = $this->commentManager->readAll();
            $this->template('Backend/manageComment', $data);           
        }  
    }

    /**
     * Built the admin writePost page
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "writePost"
     * @return Void
     */
    public function writePost($data = NULL)
    {
        $route = $this->router->getRoute();

        if ( $route[1] != ('writePost' || 'updatePost') ) {
            $this->template('Errors/404');

        } else {
            $this->template('Backend/writePost', $data);           
        }
    }

    public function settings()
    {
        $this->template('Backend/settings');
    }

// METHODS ACTIONS

    // Add new post

    /**
     * Add a new post
     * @return Void
     */
    public function addPost()
    {
        $toCheck = ['title','author', 'contents'];

        if ( Utils::checkArray($_POST, $toCheck) ){

            $_SESSION['message'] = 'Your post has been added';

            $this->postManager->create( $post = new Post($_POST) );
            header('Location: /admin/home');
            exit;

        } else {
            $this->template('Errors/404');
        }        
    }
    
    // Manage published post

    /**
     * Update published post
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "updatePost" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function updatePost()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'updatePost' ){
            $this->template('Errors/404');

        } else {
            $idPost = intval( $route[2] );
            $data = $this->postManager->readPost($idPost);

            $this->writePost($data);       
        }
    }

    /**
     * Delete a published post
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "deletePost" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function deletePost()
    {
        $route = $this->router->getRoute();

        $_SESSION['message'] = 'Your comment has been deleted';

        if ( $route[1] != 'deletePost' ){
            $this->template('Errors/404');

        } else {
            $idComment = intval( $route[2] );
            $this->postManager->delete($idComment);

            header('Location: /admin/managePost');
            exit;           
        }
    }

    /**
     * Update user comment
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "modifiedPost" 
     *   $route[2] = idPost => Integer
     * @return Void
     */    
    public function modifiedPost()
    {
        $toCheck = ['id','title','author', 'contents'];

        if ( Utils::checkArray($_POST, $toCheck) ){

            $_SESSION['message'] = 'Your comment has been modified';

            $this->postManager->update( $post = new Post($_POST) );
            header('Location: /admin/managePost');
            exit;

        } else {
            $this->template('Errors/404');
        }        
    }


    // Manage user report comment

    /**
     * Cancel comment reports
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "cancelReport" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function cancelReport()
    {
        $route = $this->router->getRoute();
        
        if ( $route[1] != 'cancelReport' ){
            $this->template('Errors/404');

        } else {
            $idComment = intval( $route[2]  );
            $this->commentManager->cancelReport($idComment);

            $_SESSION['message'] = 'User report has been canceled';

            header('Location: /admin/manageReport');
            exit;
         
        }
    }

    /**
     * Delete comment reports
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "deleteReport" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function deleteReport()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'deleteReport' ){
            $this->template('Errors/404');

        } else {
            $idComment = intval( $route[2] );
            $this->commentManager->delete($idComment);

            $_SESSION['message'] = 'User report has been deleted';

            header('Location: /admin/manageReport');
            exit;      
        }
    }

    // Manage published user comment

    /**
     * Delete comment reports
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "deleteComment" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function deleteComment()
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'deleteComment' ){
            $this->template('Errors/404');

        } else {
            $idComment = intval( $route[2] );
            $this->commentManager->delete($idComment);

            $_SESSION['message'] = 'User comment has been deleted';

            header('Location: /admin/manageComment');
            exit;          
        }
    }

    /**
     * Update user comment
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "updateComment" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function updateComment() 
    {
        $route = $this->router->getRoute();

        if ( $route[1] != 'updateComment' ){
            $this->template('Errors/404');

        } else {
            $idComment = intval( $route[2] );
            $data = $this->commentManager->readCommentToUpdate($idComment);

            $this->template('Backend/updateComment', $data);       
        }
    }

    /**
     * Update published post
     *   $route[0] = Page   => "admin"
     *   $route[1] = Action => "modifiedComment" 
     *   $route[2] = idPost => Integer
     * @return Void
     */
    public function modifiedComment()
    {
        $toCheck = ['id', 'contents'];

        if ( Utils::checkArray($_POST, $toCheck) ){

            $data = [];
            foreach ($_POST as $key => $value) {
                $data += [$key => htmlspecialchars($value)];
            }

            $_SESSION['message'] = 'User comment has been modified';

            $this->commentManager->update( new Comment($data) );
            header('Location: /admin/manageComment');
            exit;

        } else {
            $this->template('Errors/404');
        }  
    }

    // Settings

    public function newPassword()
    {
        $toCheck = ['user', 'pass', 'pass2'];

        if ( Utils::checkArray($_POST, $toCheck) ){
            if ( $_POST['pass'] != $_POST['pass2'] ){

                $_SESSION['message'] = 'The two password are not identical';
                header('Location: /admin/settings');
                exit;

            } else {
                $user = $_POST['user'];
                $password = $_POST['pass'];

                $_SESSION['message'] = 'Your password has been updated';
                
                $this->userConnection->passHach($user, $password);
                header('Location: /admin/home');
                exit;
            }

        } else {
            $this->template('Errors/404');
        }  

    }

// METHODS CONNECTION ADMIN

    /**
     * Check that connection to the Admin page is allowed
     */
    public function signIn()
    {
        $toCheck = ['user', 'password'];

        if ( Utils::checkArray($_POST, $toCheck) ) { // If TRUE
            $user     = htmlspecialchars($_POST['user']);
            $password = htmlspecialchars($_POST['password']);

            $this->userConnection->verifyAccount($user, $password);

            if ( $this->isAdmin() ){ // If TRUE
                header('Location: /admin/home');
                exit;

            }else {
                $_SESSION['message'] = 'User or password incorrect';
                $this->template('Backend/signIn');
            }

        } else {

            $this->template('Backend/signIn');
        }
    }

    /**
     * Destroy $_SESSION
     * @return Void
     */
    public function signOut()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    /**
     * Manage if user can acces to Admin section
     * @param string $methodName
     * @return Void
     */
    private function connectAdmin(string $methodName)
    {
        if ( $this->isAdmin() != TRUE && $methodName === 'signIn' ){ // If NOT true AND 'signIn'
            $this->signIn();

        } elseif( $this->isAdmin() === TRUE && $methodName === 'signIn') { // If true AND 'signIn'
            header('Location: /admin/home');
            exit;

        } elseif( $this->isAdmin() === TRUE && $methodName != 'signIn' ) { // If true AND NOT 'signIn'
            $this->$methodName();
        } else{
            $this->signIn();
        }        
    }

    /**
     * Check if user is Admin
     * @return bool
     */
    private function isAdmin()
    {
        if ( !isset($_SESSION['admin']) || $_SESSION['admin'] != TRUE ){
            return FALSE;

        } else {
            return TRUE;
        }
    }
}