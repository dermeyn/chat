<?php
namespace OCA\Chat\Db;

use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Core\Api;


class UserMapper extends Mapper {


    public function __construct(API $api) {
      parent::__construct($api, 'chat_users_in_conversation'); // tablename is news_feeds
    }

    public function findByConversation($conversationId){
    	$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `conversation_id` = ? ';
    	
    	$result = $this->execute($sql, array($conversationId));
    	
  		$feeds = array();
        while($row = $result->fetchRow()){
        	$feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
	    }

    	return $feeds;
    }

	public function findByUser($user){
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `user` = ? ';
    	
    	$result = $this->execute($sql, array($user));
    	
  		$feeds = array();
        while($row = $result->fetchRow()){
        	$feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
	    }

    	return $feeds;
	}
	
		
	public function findBySessionId($sessionID){
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` ' .
    			'WHERE `session_id` = ? ';
    	
    	$result = $this->execute($sql, array($sessionID));
    	
  		$feeds = array();
        while($row = $result->fetchRow()){
        	$feed = new User();
            $feed->fromRow($row);
            array_push($feeds, $feed);
	    }

    	return $feeds;
	}
	
	public function deleteBySessionId($conversationID, $sessionID){
		$sql = 'DELETE FROM `' . $this->getTableName() . '` WHERE `conversation_id` = ? AND `session_id` = ?';
        $this->execute($sql, array($conversationID, $sessionID));
	}
	
}