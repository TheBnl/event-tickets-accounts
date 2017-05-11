<?php
/**
 * LoginStep.php
 *
 * @author Bram de Leeuw
 * Date: 29/03/17
 */


namespace Broarm\EventTickets;

use CalendarEvent_Controller;
use Extension;

/**
 * Class LoginStep
 * 
 * @property TicketControllerExtension|TicketExtension|CalendarEvent_Controller $owner
 */
class LoginStep extends Extension
{
    private static $allowed_actions = array(
        'login'
    );

    /**
     * Continue to the account step
     *
     * @return LoginController
     */
    public function login()
    {
        return new LoginController($this->owner->dataRecord);
    }
}
