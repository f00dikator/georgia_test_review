<?php

define('SESSION_PATH',              '/tmp/');
define('SESSION_TIMEOUT', '3600');
define('WWW_PATH','/georgiatestreview');
define('DEFAULT_URL',               'index.php?');

class GTR_Session {

  private $_sessID;            // Session ID (SESSIONID in cookie)
  private $_isRedirect;        // true - redirect necessary
  private $_redirectURL;       // target URL for redirect
  public $ROOT;
  public $ROOT_URL;

  public function __construct() {

     $this->ROOT = "";
     $this->ROOT_URL = WWW_PATH . DEFAULT_URL; 
  }

  public function processRequest() {

      $this->getCookies();
      $this->sessionCheck();
      $this->authCheck();
  }

  private function getCookies()
  {
      if (!isset($_COOKIE['GTR_SESSIONID'])) {
         $this->_sessID = NULL;
      } else {
         $this->_sessID = $_COOKIE['GTR_SESSIONID'];
      }

   } // getCookies()


   private function sessionCheck()
   {

         $isSessionValid = false;

//         session_save_path($this->ROOT . SESSION_PATH);

//         $sessionPath = session_save_path();

//         if (!isset($sessionPath) || !strlen($sessionPath))
//            $sessionPath = "/tmp";
         session_start();

         $sessionPath = session_save_path();
         if (!isset($sessionPath) || !strlen($sessionPath))
            $sessionPath = "/tmp";

         $this->sessionTimeout();

         if (isset($this->_sessID)) {

            if (@stat($sessionPath."/sess_".$this->_sessID) !== false) {

               if (isset($_SESSION['STATUS']) &&
                  $_SESSION['STATUS'] == 1) {

                  $isSessionValid = true;
               }
            }

         } else {

               $this->createNewSession(false);
               $isSessionValid = true;
         }

         if (!$isSessionValid) {

            $this->createNewSession(true);
         }

         $_SESSION['LASTACTIVITY'] = time(NULL);
         $_SESSION['LASTTIME'] = time(NULL);
         $_SESSION['PROCESS'] = getmypid();

   } // sessionCheck() 

   private function sessionTimeout()
   {
   
      if (isset($this->_sessID)) {

//         session_save_path($this->ROOT . SESSION_PATH);
         $sessionPath = session_save_path();

         if (!isset($sessionPath) || !strlen($sessionPath))
            $sessionPath = "/tmp";

         if (file_exists($sessionPath."/sess_".$this->_sessID)){

            $lastactivity = time(NULL);
            if (isset($_SESSION['LASTTIME']))
               $lastactivity = $_SESSION['LASTTIME'];

            //$timediff = time() - filectime($sessionPath."/sess_".$this->_sessID);
            $timediff = time() - $lastactivity;

            if ($timediff >= SESSION_TIMEOUT) {
                 $_SESSION['GTR_TIMEOUT'] = SESSION_TIME_EXCEEDED;
            }
         }
      }
   } // sessionTimeout()


   private function createNewSession($isTampered)
   {
      if ($isTampered !== false) {

//         session_save_path($this->ROOT . SESSION_PATH);
         $sessionPath = session_save_path();

         if (!isset($sessionPath) || !strlen($sessionPath))
            $sessionPath = "/tmp";

         if (file_exists($sessionPath."/sess_".$this->_sessID))
            @unlink($sessionPath."/sess_".$this->_sessID);

         $_SESSION = array();
         @session_destroy();
         session_start();
      }

      $_SESSION['STATUS'] = 1;
      $_SESSION['STARTTIME'] = time(NULL);

   } // createNewSession()


   private function authCheck()
   {
         $_SESSION['GTR_TIMEOUT'] = 0;
   }
 
}
?>
