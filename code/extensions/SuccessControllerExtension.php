<?php
/**
 * SuccessControllerExtension.php
 *
 * @author Bram de Leeuw
 * Date: 10/05/17
 */

namespace Broarm\EventTickets;

use Extension;
use Member;

/**
 * Class SuccessControllerExtension
 * 
 * @property SuccessControllerExtension|SuccessController $owner
 */
class SuccessControllerExtension extends Extension
{
    public function afterPaymentComplete(Reservation $reservation)
    {
        if ($member = Member::currentUser()) {
            $member->Reservations()->add($reservation);
        }
    }
}
