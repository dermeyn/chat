Chat
====

Have chats in your ownCloud!

## Installation
1.	Clone the repo to the apps directory of your owncloud installation
2.	`git submodule init && git submodule update`
3. 	Activate the chat app
4.	Launch `./occ chat:boot` via php from the commandline
5.	When using nginx you may have to add something like this to your config (you need nginx>=1.3.13):
	```
	location /chat/websocket/ {
		proxy_set_header Host $host;
		proxy_set_header x-Real-IP $remote_addr;
		proxy_set_header x-forwarded-for $proxy_add_x_forwarded_for;
		proxy_pass http://localhost:3582;
		proxy_redirect default;
		proxy_http_version 1.1;
		proxy_set_header Upgrade $http_upgrade;
		proxy_set_header Connection "upgrade";
	}
	```
6. 	Go to the chat app in owncloud. You should see 'Connected to the server'
7. 	Fill in the username of an other user
8. 	Click on the left bar on the conversation
9. 	Start chatting!


## Built-in API
*Note: the API is just a set of commands used by the server and the client*

 Action  | JSON Request Data   | JSON Possible Response Data  
 --- | --- | ---
 greet | `{status: “command”, data: {type: “greet”, param: {user: “foo”}}}` | `{status:  “success”}` `{status: “error”, data: {msg: “NotOCUser” }}`
 join | `{status: “command”, data: {type: “join”, param: {user: “foo”, room: “bar”, timestamp: “”}}}` | `{status: “success”}`
 invite | `{status: “command”, data: {type: “invite”, param: { user: “foo”, room: “bar”, timestamp: “” usertoinvite: “”}}}` | `{status: “success”} {status: “error”, data: {msg: “usernotonline”}}` `{status: “error”, data: {msg: “usernotexists”}}`
leave | `{status: “command”, data: {type: “leave”, param: {user: “foo”, room: “bar”}}}` | `{status: “success”}` `{status: “error”, data: {msg: “roomdontexists”}}`
getUser | `{status: “command”, data: {type: “getusers”, param: {room: “bar”}}}` | `{status: “success”, data: {param: {users: [foo, bar, foobar]}}}`
send | `{status: “command”, data: {type: “send”, param: {room: “bar”, msg: “foobar”}}}` | `{status: “success”}`
