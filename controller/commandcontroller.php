<?php

namespace OCA\Chat\Controller;

use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Http\JSONResponse;
use \OCA\AppFramework\Core\API;
use \OCA\Chat\Db\PushMessage;
use \OCA\Chat\Db\PushMessageMapper;
use \OCA\Chat\Db\Conversation;
use \OCA\Chat\Db\ConversationMapper;
use \OCA\Chat\Db\User;
use \OCA\Chat\Db\UserMapper;
use \OCA\Chat\Db\UserOnline;
use \OCA\Chat\Db\UserOnlineMapper;

class CommandController extends Controller {

	

    /**
     * @param Request $request an instance of the request
     * @param API $api an api wrapper instance
     */
    public function __construct($api, $request){
        parent::__construct($api, $request);
    }
  
    /**
     * @CSRFExemption
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function greet(){
    	if(in_array($this->params('user'), \OCP\User::getUsers())){   		
   		
    		$userOnline = new UserOnline();
    		$userOnline->setUser($this->params('user'));
    		$mapper = new UserOnlineMapper($this->api);
    		$mapper->insert($userOnline);
    		
    		
    		return new JSONResponse(array('status' => 'success'));
    	} else {
    		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'NO-OC-USER')));
    	}
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function join(){
   		if(in_array($this->params('user'), \OCP\User::getUsers())){ 

	   		$userMapper = new UserMapper($this->api);
	   		$users = $userMapper->findByConversation($this->params('conversationID'));
	   		
	   		if (count($users) === 0){
	   			$conversation = new Conversation();
	   			$conversation->setConversationId($this->params('conversationID'));
	   			$mapper = new ConversationMapper($this->api); 
	   			$mapper->insert($conversation);
	   			 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);
	   			
	   			return new JSONResponse(array('status' => 'success'));
	   		} else { 
	   			$user = new User();
	   			$user->setConversationId($this->params('conversationID'));
	   			$user->setUser($this->params('user'));
	   			$userMapper = new UserMapper($this->api);
	   			$userMapper->insert($user);
	   			
	   			return new JSONResponse(array('status' => 'success'));
	   		} 
	   	} else {
	   		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'NO-OC-USER')));
	   	}
   		
   	}
   	
    

   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function invite(){
   		$userOnlineMapper = new UserOnlineMapper($this->api);
   		$usersOnline = $userOnlineMapper->getOnlineUsers();

   		if($this->params('user') !== $this->params('usertoinvite')){
	    	if(in_array($this->params('usertoinvite'), \OCP\User::getUsers())){
				if(in_array($this->params('usertoinvite'), $usersOnline)){
					$pushMessage = new PushMessage();
					$pushMessage->setSender($this->params('user'));
					$pushMessage->setReceiver($this->params('usertoinvite'));
					$pushMessage->setCommand(json_encode(array('type' => 'invite',
																'param' => array(	'user' => $this->params('user'),	
																					'conversationID' => $this->params('conversationID'),
																					'usertoinvite' => $this->params('usertoinvite')))));
					$mapper = new PushMessageMapper($this->api);
					$mapper->insert($pushMessage);		
					return new JSONResponse(array('status' => 'success'));
				} else {
					return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-TO-INVITE-NOT-ONLINE')));
				}   			
	   		} else { 
	    		return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-TO-INVITE-NOT-OC-USER')));
	   		}
   		} else {
   			return new JSONResponse(array('status' => 'error', 'data' => array('msg' => 'USER-EQAUL-TO-USER-TO-INVITE')));
   		}
   	}
   	
    // Functions below are place holders
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function leave(){
   		return new JSONResponse(array('status' => $this->params('user'),
   				'conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function getusers(){
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   		));
   	}
   	
   	/**
   	 * @CSRFExemption
   	 * @IsAdminExemption
   	 * @IsSubAdminExemption
   	 */
   	public function send(){
   		$api = new API();
   		  		
   		return new JSONResponse(array('conversationID' => $this->params('conversationID'),
   										'msg' => $this->params('msg'),
   		));
   	}
}