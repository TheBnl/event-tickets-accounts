<?php
/**
 * AccountController.php
 *
 * @author Bram de Leeuw
 * Date: 29/03/17
 */

namespace Broarm\EventTickets;

use Cookie;
use FormAction;
use LiteralField;
use Member;
use MemberLoginForm;
use Security;
use Session;

/**
 * Class AccountController
 *
 * @package Broarm\EventTickets
 */
class LoginController extends CheckoutStepController
{
    protected $step = 'login';

    private static $require_login = false;

    private static $allowed_actions = array(
        'LoginForm',
        'logout',
        'createaccount'
    );

    /**
     * Get a link to the create account step
     * @return string
     */
    public function getCreateAccountLink()
    {
        return $this->Link("{$this->step}/createaccount");
    }

    /**
     * Show the create account form
     *
     * @return CreateAccountController
     */
    public function createAccount()
    {
        return new CreateAccountController($this->dataRecord, $this);
    }

    /**
     * Get the discount form
     *
     * @return MemberLoginForm
     */
    public function LoginForm()
    {
        // Set the MemberLoginForm back url to the current step
        Session::set('BackURL', $this->getNextStepLink());

        // Create the login form
        $memberLoginForm = new MemberLoginForm($this, 'LoginForm');

        if (!Member::currentUserID()) {
            $createAccountLabel = _t("MemberLoginForm.CREATE_ACCOUNT", 'Create account');
            $memberLoginForm->Actions()->insertAfter('action_dologin', LiteralField::create(
                'createAccount',
                "<a class='button' href='{$this->getCreateAccountLink()}'>$createAccountLabel</a>"
            ));
        }

        // If a user is logged in or of login is not required show the next step button
        if (!(bool)self::config()->get('require_login') || Member::currentUserID()) {
            // if there is a member logged in change the button label
            if (Member::currentUserID()) {
                $label = _t("MemberLoginForm.CONTINUE", 'Next step');
                $action = 'action_logout';
            } else {
                $label = _t("MemberLoginForm.CONTINUE_GUEST", 'Continue as a guest');
                $action = 'createAccount';
            }

            $memberLoginForm->Actions()->insertAfter(
                $action,
                LiteralField::create('NextStep', "<a class='button' href='{$this->getNextStepLink()}'>$label</a>")
            );
        }

        return $memberLoginForm;
    }
    
    /**
     * Log out without destroying the whole session
     *
     * @see Member::logOut() for what session variables are cleared
     * @see Security::logout() for the original implementation of used by MemberLoginForm
     */
    public function logout()
    {
        Session::clear("loggedInAs");
        if (Member::config()->get('login_marker_cookie')) {
            Cookie::set(Member::config()->get('login_marker_cookie'), null, 0);
        }
        Cookie::force_expiry('alc_enc');
        Session::clear('readingMode');
        $this->redirect($this->Link());
    }
}
