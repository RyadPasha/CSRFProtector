<?php

/**
 * CSRFProtector
 * A standalone php library for csrf mitigation in web applications.
 * @package
 * @author Mohamed Riyad
 * @copyright Mohamed Riyad
 * @version 1.0-2018
 * @access public
 * @license GNU v3
 */
class CSRFFrontEnd
{
    private $errorFunction;
    private $tokenManager;

    public function __construct(TokenManager $tokenManager, callable $errorFunction = null)
    {
        $this->tokenManager = $tokenManager;
        $this->errorFunction = ($errorFunction != null) ? $errorFunction : function ()
        {
            die("CSRF Protected");
        };
    }
    public function checkGets()
    {
        if (!empty($_POST))
        {
            return;
        }
        if (isset($_GET['csrftoken']) && $this->tokenManager->useToken($_GET['csrftoken']))
        {
            return;
        }

        if (empty($_GET) && $this->tokenManager->isFirstVisit())
        {
            return;
        }
        call_user_func($this->errorFunction);
    }
    public function checkPosts()
    {
        if (!empty($_GET))
        {
            return;
        }
        if (isset($_POST['csrftoken']) && $this->tokenManager->useToken($_POST['csrftoken']))
        {
            return;
        }
        if (empty($_POST) && $this->tokenManager->isFirstVisit())
        {
            return;
        }
        call_user_func($this->errorFunction);
    }
    public function checkUser()
    {
        if ($this->tokenManager->isAcceptedClick())
        {
            return;
        }
        call_user_func($this->errorFunction);
    }
}

?>
