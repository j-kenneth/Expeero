<?php
/**
 * Class implements mock for session handler.
 * Used during items price cacluation functionality with shopping cart.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.4
 */

class WC_Dummy_Session_Handler extends WC_Session_Handler
{
	public function has_session(){
		return false;
	}
}
