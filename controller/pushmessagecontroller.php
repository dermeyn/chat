<?php
/**
 * ownCloud - Chat app
 *
 * @author Tobia De Koninck (LEDfan)
 * @copyright 2013 Tobia De Koninck tobia@ledfan.be
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Chat\Controller;

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Http\Request;
use \OCA\AppFramework\Http\Response;
use \OCA\AppFramework\Http\JSONResponse;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Appframework\Db\DoesNotExistException;
use \OCA\Chat\Commands\GetPushMessage;


class PushMessageController extends Controller {

	public function __construct(API $api, Request $request){
		parent::__construct($api, $request);
	}

	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * 
	 */
	public function get() {
		session_write_close();	// Very important! http://stackoverflow.com/questions/11946638/long-polling-blocks-my-other-ajax-requests
		$getPushMesage = new GetPushMessage($this->api, $this->getParams());
		$pushMessages = $getPushMesage->execute();		
		
		$commands = array();
		$ids = array();
		foreach($pushMessages as $pushMessage){
			$command = array();
			$command['status'] = 'command';
			$command['id'] = $pushMessage->getId();
			$command['data'] = json_decode($pushMessage->getCommand());
			$commands[] = $command;
			$ids[] = $pushMessage->getId();
		}
		return new JSONResponse(array('status' => 'command', 'data' => $commands, 'ids' => $ids));
	}
	
	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function delete(){
		$ids = $this->params('ids');
		foreach($ids as $id){		
			$pushMessage = new PushMessage();
			$pushMessage->setId($id);
			$mapper = new PushMessageMapper($this->api);
			$mapper->delete($pushMessage);
		}
		return new JSONResponse(array('status' => 'success'));
	}

}
