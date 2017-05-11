<?php
/**
 * MemberExtension.php
 *
 * @author Bram de Leeuw
 * Date: 10/05/17
 */

namespace Broarm\EventTickets;

use DataExtension;
use FieldList;
use GridField;
use GridFieldConfig_RecordEditor;

/**
 * Class MemberExtension
 *
 * @method \HasManyList Reservations()
 * 
 * @property MemberExtension|\Member $owner
 */
class MemberExtension extends DataExtension
{
    private static $has_many = array(
        'Reservations' => 'Broarm\EventTickets\Reservation'
    );

    /**
     * Add a grid field to the member object
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Reservations');
        if ($this->owner->Reservations()->exists()) {
            $reservationLabel = _t('TicketExtension.Reservations', 'Reservations');
            $fields->addFieldToTab(
                "Root.$reservationLabel",
                GridField::create('Reservations', $reservationLabel, $this->owner->Reservations(), GridFieldConfig_RecordEditor::create())
            );
        }
    }
}
