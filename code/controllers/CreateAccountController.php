<?php
/**
 * CreateAccountController.php
 *
 * @author Bram de Leeuw
 * Date: 07/04/17
 */

namespace Broarm\EventTickets;

use Page_Controller;
use SSViewer;

/**
 * Class CreateAccountController
 *
 * @mixin TicketExtension
 *
 * @package Broarm\EventTickets
 */
class CreateAccountController extends Page_Controller
{
    /**
     * @var LoginController
     */
    protected $parentController;

    private static $allowed_actions = array(
        'CreateAccountForm'
    );

    /**
     * CreateAccountController constructor.
     * Sets a reference to the parent controller
     *
     * @param \CalendarEvent|\Page   $dataRecord
     * @param LoginController $parentController
     */
    public function __construct($dataRecord, LoginController $parentController)
    {
        $this->parentController = $parentController;
        parent::__construct($dataRecord);
    }

    /**
     * Get the parent controller
     *
     * @return LoginController
     */
    public function getParentController()
    {
        return $this->parentController;
    }

    /**
     * Get the check in form
     *
     * @return CreateAccountForm
     */
    public function CreateAccountForm()
    {
        return new CreateAccountForm($this, 'CreateAccountForm');
    }

    /**
     * Force the controller action
     *
     * @param string $action
     *
     * @return SSViewer
     */
    public function getViewer($action)
    {
        if ($action === 'index') {
            $action = 'createaccount';
        }
        
        return parent::getViewer($action);
    }

    /**
     * Get a relative link to the current controller
     * Needed to handle the form
     *
     * @param null $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        if (!$action) {
            return $this->getParentController()->getCreateAccountLink();
        }

        return $this->dataRecord->RelativeLink($action);
    }
}
