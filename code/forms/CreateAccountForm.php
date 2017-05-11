<?php
/**
 * CreateAccountForm.php
 *
 * @author Bram de Leeuw
 * Date: 11/05/17
 */

namespace Broarm\EventTickets;

use ConfirmedPasswordField;
use Controller;
use Convert;
use FieldList;
use Form;
use FormAction;
use Group;
use Member;
use PasswordValidator;
use TextField;

class CreateAccountForm extends Form
{
    private static $user_group_name = 'Ticket shop users';

    private static $password_min_length = 8;
    private static $password_min_score = 0;
    private static $password_tests = array(
        'lowercase',
        'uppercase',
        'digits',
        'punctuation'
    );


    public function __construct(Controller $controller, $name)
    {
        $fields = new FieldList(array(
            TextField::create('FirstName', 'FirstName'),
            TextField::create('Surname', 'Surname'),
            TextField::create('Email', 'Email'),
            ConfirmedPasswordField::create('Password', 'Password'),
        ));

        $actions = new FieldList(array(
            new FormAction('createAccount', 'Create account')
        ));

        parent::__construct($controller, $name, $fields, $actions, Member::singleton()->getValidator());
        $this->extend('updateCreateAccountForm');
    }

    /**
     * @param      $data
     * @param Form $form
     */
    public function createAccount($data, Form $form)
    {
        $this->extend('updateCreateAccount', $data, $form);
        $member = Member::create();
        $form->saveInto($member);

        $password = PasswordValidator::create();
        $password->minLength(self::config()->get('password_min_length'));
        $password->characterStrength(
            self::config()->get('password_min_score'),
            self::config()->get('password_tests')
        );

        $result = $password->validate($data['Password']['_Password'], $member);
        if (!$result->valid()) {
            $form->addErrorMessage('Password', $result->message(), 'error');
            $this->getController()->redirect($this->getController()->Link());
        } else {
            $member->write();
            $member->addToGroupByCode($this->getGroupCode());
            $member->logIn();
            $this->getController()->redirect($this->getController()->getParentController()->Link());
        }
    }

    /**
     * Get the ticket group by code, if it does not exists create it
     *
     * @return Group
     */
    public function getTicketGroup()
    {
        if (!$group = Group::get()->find('Code', $this->getGroupCode())) {
            $group = Group::create();
            $group->Title = self::config()->get('user_group_name');
            $group->Code = $this->getGroupCode();
            $group->write();
        }

        return $group;
    }

    /**
     * Create a usable group code
     *
     * @return String
     */
    private function getGroupCode()
    {
        return Convert::raw2url(self::config()->get('user_group_name'));
    }
}