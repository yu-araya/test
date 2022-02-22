<?php
/**
 * SQL Dump element. Dumps out SQL log information
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Elements
 * @since         CakePHP(tm) v 1.3
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

if (Configure::read('debug') < 2) {
	return false;
}
$sources = ConnectionManager::configured();
foreach ($sources as $source):
	$db = ConnectionManager::get($source);
	if (!method_exists($db, 'logQueries')):
		continue;
	endif;
	$db->logQueries(true);
endforeach;
