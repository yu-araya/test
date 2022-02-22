<?php
namespace App\View\Helper;

use App\View\Helper\AppHelper;

class DateHelper extends AppHelper
{

	function formatDate($value, $format = 'Y-m-d')
	{
		return (new \Datetime($value))->format($format);
	}

	function formatDatetime($value, $format = 'Y-m-d H:i:s')
	{
		return (new \Datetime($value))->format($format);
	}
}
?>
