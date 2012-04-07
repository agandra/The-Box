<?php
/**
 * PHP 5.3.3+
 *
 * The Box
 * Copyright 2012, Near The Box (www.nearthebox.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Near The Box (www.nearthebox.com)
 * @link          http://github.com/agandra/The-Box  The Box PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// Set the DEBUG level (shouldnt use this and should use logging on production servers)
// Set to 0 to turn off
theBox::setDebug(1);

// The homepage controller and action it should go to.  Do not change this structure, just change the values of home and index
theBox::setHome(array('controller'=>'home', 'action'=>'index'));

// What database to use on your app, set to false if no database is needed
theBox::useDatabase('default');
