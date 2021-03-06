<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test page for SAML
 *
 * @package    auth_saml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../../config.php');
// @codingStandardsIgnoreEnd
require('../setup.php');

// First setup the PATH_INFO because that's how SSP rolls.
$_SERVER['PATH_INFO'] = '/' . $saml2auth->spname;

/*
 * There are 4 methods of logging out:
 *
 * 1) Initiated from moodles logout, in which case we first logout of
 *    moodle and then log out of the middle SP and then optionally
 *    redirect to the IdP to do full Single Logout. This is the way
 *    a majority of users logout and is fully supported.
 *
 * 2) If doing SLO via IdP via the HTTP-Redirect binding
 *
 * 3) Same as 2 but via the front channel HTTP-Post binding. This should
 *    work but is untested. TODO.
 *
 * 4) Backchannel logout via the SOAP binding. TODO.
 *
 */
try {
    $session = \SimpleSAML\Session::getSessionFromRequest();
    $session->registerLogoutHandler($saml2auth->spname, $saml2auth, 'auth_saml2_after_logout_from_idp_front_channel');

    require('../.extlib/simplesamlphp/modules/saml/www/sp/saml2-logout.php');
} catch (Exception $e) {
    // TODO SSPHP uses Exceptions for handling valid conditions, so a succesful
    // logout is an Exception. This is a workaround to just go back to the home
    // page but we should probably handle SimpleSAML_Error_Error similar to how
    // extlib/simplesamlphp/www/_include.php handles it.
    redirect(new moodle_url('/'));
}

