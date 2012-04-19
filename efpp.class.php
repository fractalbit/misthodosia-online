<?php

// This class requires fAuthorization and fSession classes from the flourish library

include_once("efpp.translation.gr.php");

class efpp_user {
    private $password;
    private $username;
    private $safemode = false;
    private $is_logged_in = FALSE;

    public $message;
    public $error_message;
		
    public function __construct($uname, $pass, $sm = TRUE, $cookie_expires = '1 hour') {
        global $efpp_txt, $session_path;


        empty($uname) ? $this->message = 'Ο λογαριασμός διαχειριστή δεν έχει οριστεί σωστά. Παρακαλούμε διορθώστε το αρχείο config.inc.php - <a href="https://github.com/fractalbit/misthodosia-online/blob/master/readme.md" target="_blank">Τεκμηρίωση</a>' : $this->username = $uname;
        strlen($pass)<6 ? $this->message = 'Ο λογαριασμός διαχειριστή δεν έχει οριστεί σωστά. Παρακαλούμε διορθώστε το αρχείο config.inc.php - <a href="https://github.com/fractalbit/misthodosia-online/blob/master/readme.md" target="_blank">Τεκμηρίωση</a>' : $this->password = $pass;
        
        $this->safemode = $sm;
		
        //if(!empty($session_path)) fSession::setPath($session_path);
        //fSession::setLength($cookie_expires);

        if(admin_configured()){
            //die('den pame kala');
            if(isset($_GET['logout'])){
                // Log the user out                        
                fAuthorization::destroyUserInfo();
                savelog(date('d/m/Y H:i:s', time()) . ' - O διαχειριστής αποσυνδέθηκε');          
                $this->is_logged_in = FALSE;

                //Set an appropriate message
                $this->message = '<div id="efpp_logged_out" class="efpp">'.$efpp_txt['loggedOutSuccess'].'</div>';

                if($this->safemode) die($this->message . $this->show_login_form());
            }else{
                if(isset($_POST['efpp_action']) && $_POST['efpp_action'] == 'login'){
                    // Check credentials
                    if($_POST['efpp_username'] == $this->username && $_POST['efpp_password'] == $this->password){                    
                        // Log in the user as admin
                        fAuthorization::setUserAuthLevel('admin');   
                        savelog(date('d/m/Y H:i:s', time()) . ' - Ο διαχειριστής συνδέθηκε');          
                        $this->is_logged_in = TRUE;

                        // Set an appropriate message
                        $this->message = '<div id="efpp_logged_in" class="efpp">'.$efpp_txt['You are logged in as'].' '.$this->username.'. <a href="'.$_SERVER['PHP_SELF'].'?logout">'.$efpp_txt['Logout'].'</a> </div>';

                        if($this->safemode) echo $this->message;
                    }else{
    					$this->error_message = '<div id="efpp_wrong_login" class="efpp">'.$efpp_txt['Wrong Username password'].'</div>';
                        if($this->safemode) die($this->message . $this->show_login_form());
                        savelog(date('d/m/Y H:i:s', time()) . ' - <span class="error">Αποτυχμένη προσπάθεια εισόδου διαχειριστή</span>');
                    }
                }else{
                    $this->check_logged_in();
                }
            }
        }
    }
   
    function check_logged_in(){
        global $efpp_txt;
       
        //if(empty($this->password)) die("You have to set a password before using this class.");
       if(admin_configured()){
       
            // Branch based on the user’s login status
            if (fAuthorization::checkLoggedIn()) {
                // Code to execute if the user is logged in
                $this->is_logged_in = TRUE;
                $this->message = '<div id="efpp_logged_in" class="efpp">'.$efpp_txt['You are logged in as'].' '.$this->username.'. <a href="'.$_SERVER['PHP_SELF'].'?logout">'.$efpp_txt['Logout'].'</a> </div>';

                if($this->safemode) echo $this->message;
            }else{
                $this->is_logged_in = FALSE;
                $this->message = '<div id="efpp_not_logged_in" class="efpp">'.$efpp_txt['Not logged in']. ' ' . $this->error_message . '</div>';
                if($this->safemode) die($this->show_login_form());            
            }       
        }

        return $this->is_logged_in;        
    }

    public function show_login_form(){
        global $efpp_txt;
        ob_start();
        ?>
            <div id="efpp_form_container">
                <form id="efpp_form" class="efpp_form" name="efpp_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" />
                    <p id="efpp_login_prompt"><?= $efpp_txt['Please login']?></p>
                    <p id="efpp_username_container">
                        <label for="efpp_username"><?= $efpp_txt['username'] ?></label> 
                        <input name="efpp_username" id="efpp_username" type="text"/> 
                    </p>
                    <p id="efpp_password_container">
                        <label for="efpp_password"><?= $efpp_txt['password'] ?></label> 
                        <input name="efpp_password" id="efpp_password" type="password" /> 
                    </p>
                    <p id="efpp_button_container">  
                        <input type="submit" name="efpp_login_button" id="efpp_login_button" value="<?= $efpp_txt['Login'] ?>" class="btn primary large" />
                    <input type="hidden" name="efpp_action" value="login" />
                </form>
            </div>
        <?php
        $login_form = ob_get_contents();
        ob_end_clean();
        return $login_form;
    }   

}


function admin_configured(){
    if(strlen(SUPER_USER)>0 && strlen(SUPER_PASS)>5){
        //die('admin is fine thank you');
        return true;
    }else{
        //die('admin is sick');
        return false;
    }
}