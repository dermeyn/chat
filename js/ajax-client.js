var sessionID = generateSessionID();
console.log(sessionID);
alert(sessionID);
$(document).ready(function(){
	greet(function(msg){
		throwSuccess('Connected to the server');
		
		// Get existing conversations which the user are joined
		//getConversations(function(conversations){
			//$.each(conversations, function(index, conversation){
		//		joinConversation(conversation, 'test' + index);
			//});
	//	});
		
		/*
		 * Long Polling Function
		 */
	
		longPoll();
		function longPoll(){
			console.log('ongPoll Called');
			getPushMessge(function(commands){
				deletePushMessage(commands.ids, function(){
				$.each(commands.data, function(index, command){
					handlePushMessage(command);
				});
				longPoll();
				});
			});
		}
		
		function handlePushMessage(command){
			if (command.data.type === "invite"){
				onInvite(command.data.param);
			} else if (command.data.type === "send"){
				onChatMessage(command.data.param);                        
			} /*else if (msg.data.type === "left"){
				var conversationID = msg.data.param.conversationID;
				getUsers(server, conversationID, function(msg){
					if (msg.data.param.users.length <= 1){
						deleteConversation(conversationID);
					} 
				});
			}*/
		}
	
	
	/*
	
		handlePushMessage();
		function handlePushMessage(){
			getPushMessge(function(msg){
   				if (msg.data.type === "invite"){
					onInvite(msg.data.param);
				} else if (msg.data.type === "send"){
					onChatMessage(msg.data.param);                        
				} /*else if (msg.data.type === "left"){
					var conversationID = msg.data.param.conversationID;
					getUsers(server, conversationID, function(msg){
						if (msg.data.param.users.length <= 1){
							deleteConversation(conversationID);
						} 
					});
				}*/
		/*
    			deletePushMessage(msg.id, function(){
        			handlePushMessage();
    			});
			});		
		}
		*/
		window.addEventListener("beforeunload", function (e) {
		  	var confirmationMessage = "\o/";
		
		  
		  	quit(function(){
		  		(e || window.event).returnValue = confirmationMessage;     //Gecko + IE

		  		return confirmationMessage;                                //Webkit, Safari, Chrome etc.
		  	});
		});
		
		/*
		 * UI Event Handler Functions
		 */		
		$('#createConverstation').click(function(){
			initConversation();   
		});

		$("body").on("click", ".conversation", function(event){
			$('#' + $(this).data('conversationid')).fadeIn();
		});

		$("body").on("keypress", ".messagefield", function(e){
			if(e.which === 13) {
				sendChatMessage($(this).val(), $(this).data('conversationid'), function(msg){
				});
				$(this).val('');
			}
		});

		$('body').on("click", ".hide", function(){
			hideConversation($(this).data('conversationid')); 
		});

		$('body').on('click', '.leave', function(){
			var conversationID = $(this).data('conversationid');
			leave(server, conversationID, OC.currentUser, function(msg){
				$('#' + conversationID).remove();
				$('#conversation' + conversationID).remove();
			});
		});

		$('#user').keypress(function(e){
			if (e.which === 13){
				initConversation();
			}
		});

		$('body').on('keypress', '.invitefield', function(event){
			if(event.which === 13){
				invite($(this).val(), $(this).data('conversationid'), function(msg){
				});
				$(this).val('');
			} 
		});
	});
});


/*
 * Conversation functions
 */
function initConversation(){
	var userToInvite = $('#user').val();
	$('#user').val('');

	if(userToInvite !== ''){
		if(userToInvite === OC.currentUser){
			throwError('USER-EQAUL-TO-USER-TO-INVITE');
		} else {
			
			var conversationID = generateConversationID();
			join(conversationID, function(msg){
				invite(userToInvite, conversationID,  function(msg){

					var chat_template = '<section id="*CONVERSATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONVERSATIONID*"></div><input class="messagefield" data-CONVERSATIONID="*CONVERSATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conversationID="*CONVERSATIONID*" type="text" ><button class="leave" data-CONVERSATIONID="*CONVERSATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conversationID="*CONVERSATIONID*" title="Hide this window">Hide</button></footer> ';
					var chat = chat_template.replace('*USER*', userToInvite).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID);
					$('#chats').append(chat);
					$('#conversations').append('<li id="conversation' + conversationID +'" data-displayed="true" data-conversationID="' + conversationID + '" data-user="' + userToInvite + '" class="conversation">'  + userToInvite + '</li>');
				});
			});
		}
	} else {
		throwError('USER-TO-INVITE-EMPTY');
	}
}

function joinConversation(conversationID, conversationName){
	join(conversationID, function(msg){
		// TODO use octemplate here to make this more readable
		var chat_template = '<section id="*CONVERSATIONID*"class="chatContainer"><h3>*USER*</h3><div class="chatLeft"><div class="chatText" id="chatText*CONVERSATIONID*"></div><input class="messagefield" data-CONVERSATIONID="*CONVERSATIONID*" type="text"  class="message"><footer><input class="invitefield" data-conversationID="*CONVERSATIONID*" type="text" ><button class="leave" data-CONVERSATIONID="*CONVERSATIONID*" title="Leave this conversation">Leave</button><button class="hide" data-conversationID="*CONVERSATIONID*" title="Hide this window">Hide</button></footer> ';
		var chat = chat_template.replace('*USER*', conversationName).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID).replace('*CONVERSATIONID*', conversationID);
		$('#chats').append(chat);
		$('#conversations').append('<li id="conversation' + conversationID +'" data-displayed="true" data-conversationID="' + conversationID + '" data-user="' + conversationName + '" class="conversation">'  + conversationName + '</li>');
	});
}

/*
 * On push message functions
 */
function onInvite(param){
	joinConversation(param.conversationID, param.user);
}

function onChatMessage(param){
	$('#chatText' + param.conversationID).append("<div class='chatmsg'>"+param.user+": "+param.msg+"</div>");
}

/*
 * Send command to server functions
 */
function greet(success){
	sendMSG('greet', {}, success, function(errorMsg){
   		throwError(errorMsg);
	});
}

function invite(userToInvite, conversationID, success){
	sendMSG('invite', {conversationID : conversationID, timestamp : (new Date).getTime(), usertoinvite : userToInvite},success, function(errorMsg){
		throwError(errorMsg);
	});
}

function join(conversationID, success){
	sendMSG('join', {conversationID : conversationID,  timestamp : (new Date).getTime()}, success);
}

function quit(success){
	$.ajax({
		type: "POST",
		url: OC.Router.generate("command_quit"),
		data: { user: OC.currentUser, sessionID : sessionID},
		async: false,
	}).done(success);
}


function sendChatMessage(message, conversationID, callback){
	sendMSG('send', {conversationID : conversationID, msg : message}, function(msg){
		callback(msg);
	});
}

function getConversations(callback){
	sendMSG('get_conversations', {}, function(data){
		callback(data.data.param.conversations);
	});
}
