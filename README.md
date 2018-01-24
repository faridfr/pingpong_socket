# ping pong socket
Ping Pong game with php socket and html canvas

# Demo
![Demo](http://s8.picofile.com/file/8317586650/pingpong.png)

# Persian document
You can read this document in persian with [This Link](https://ufile.io/b5psn)

# Usage
First , you should copy files in your localhost ( that can be create with Xampp ) .<br>
Then just open `server.php` and `index.php` files to edit server and port address .<br>
Run your server.php file in browser .<br>
Then you can open index.php file and enjoy from game ( game starts after second user ) .<br>

# General structure
First , visitors connect to server with this code in index.php and server determine turn and side of user .

```javascript
$(document).ready(function(){
	var wsUri = "ws://127.0.0.1:65522/pong/server.php"; 	
	websocket = new WebSocket(wsUri);
	websocket.onopen = function(ev) { 
        toastr["info"]("socket is ok !")
	}

	
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; 
		var umsg = msg.message; 
		var uname = msg.name;
		var go = msg.gameover;
		if(type == 'system')
		{
			 if(turn=='') 
             {
                turn = uname;
                toastr["info"]("You are : "+turn+" side . Hope enjoy")
             }
             if(go==1) gameover=0;
             else gameover=1;

		}
		else {
			eval(uname+'_racket').y = umsg;
		}

	};
	websocket.onerror	= function(ev){toastr["error"](ev.data)};
	websocket.onclose 	= function(ev){toastr["error"]('Close')};
});
```


# Canvas components
All the elements in the game defined in this code .

```javascript
function startGame() {
    right_racket = new component(65, 65, "images/right_racket.png", right_line-10, center_line,"image");
    left_racket = new component(65, 65, "images/left_racket.png",left_line-30, center_line,"image");
    ball = new component(25, 25, "images/ball.png", eval(side+'_line'), center_line+20 ,"image");
    right_score = new component("30px", "IranYekan", "red", 310, 140, "text");
    left_score = new component("30px", "IranYekan", "blue", 180, 140, "text");
    sound_strike 	= new sound("sounds/strike.mp3");
    sound_gameover   = new sound("sounds/gameover.mp3");
    sound_shoes1	= new sound("sounds/shoes2.mp3");
    sound_shoes2	= new sound("sounds/shoes1.mp3");
    myGameArea.start();
}
```

 For a better understanding of html canvas games , please [read this tutorial by w3schools](https://www.w3schools.com/graphics/game_canvas.asp)
 
 
 I just send the rackets position in socket . you can send ball and results too .
 
 ```javascript
function send_packet(ja){
		var msg = {
		message: ja,
		name: turn,
        };
		websocket.send(JSON.stringify(msg));
	}
  ```
  
 You can change game speed in this line :
 
 ```javascript
 this.interval = setInterval(updateGameArea, 10);
 ```
 
 Thanks .

Code by : Farid Froozan [(FaridFr.ir)](http://faridfr.ir) as university project ( Computer Networks )

