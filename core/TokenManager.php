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
class TokenManager
{
    private $tokenFunction;
    private $maxTime;
    private $minSecondBeforeNextClick;

    public function __construct(callable $tokenFunction = null, $maxTime = 120, $minSecondBeforeNextClick = 1)
    {
        $this->tokenFunction = ($tokenFunction != null) ? $tokenFunction : function ()
        {
            return "_" . mt_rand(1, 20) . mt_rand(1, 20) . mt_rand(1, 20);
        };
        $this->maxTime = $maxTime;
        $this->minSecondBeforeNextClick = $minSecondBeforeNextClick;
    }

    public function applyNewToken()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }
        $token = call_user_func($this->tokenFunction);
        while (isset($_SESSION[$token]))
        {
            $token = call_user_func($this->tokenFunction);
        }
        $_SESSION[$token] = time() + $this->maxTime;
        $_SESSION['nextClick'] = time() + $this->minSecondBeforeNextClick;
        return $token;
    }

    public function useToken($token)
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }
        if (isset($_SESSION[$token]) && $_SESSION[$token] >= time())
        {
            unset($_SESSION[$token]);
            return true;
        } elseif (isset($_SESSION[$token]))
        {
            unset($_SESSION[$token]);
        }
        return false;
    }
    public function isFirstVisit()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }
        return empty($_SESSION);
    }
    public function isAcceptedClick()
    {
        if (session_status() !== PHP_SESSION_ACTIVE)
        {
            session_start();
        }
        return !isset($_SESSION['nextClick']) || $_SESSION['nextClick'] <= time();
    }

}

?>
