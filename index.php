<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
</head>
<body onload="startGame()">

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-left",
  "preventDuplicates": true,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}
</script>

<script language="javascript" type="text/javascript">

var turn = '',gameover=1;

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
</script>


<style>
canvas {
    background-image: url('images/table.jpg');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: bottom;
}
</style>


<script>

var left_racket , right_racket , ball , side='left' , direction_set = 0 , right_score , left_score , sound_strike , sound_gameover , sound_shoes1 , sound_shoes2 ;
var top_line = 235 , bottom_line = 450 , right_line = 445 , left_line = 40 , center_line = 320 , center = 240 ;

function get_random_direction (){
	var random = Math.floor((Math.random() * 2) + 1);
	return random;
}

function sound(src) {
    this.sound = document.createElement("audio");
    this.sound.src = src;
    this.sound.setAttribute("preload", "auto");
    this.sound.setAttribute("controls", "none");
    this.sound.style.display = "none";
    document.body.appendChild(this.sound);
    this.play = function(){
        this.sound.play();
    }
    this.stop = function(){
        this.sound.pause();
    }
}

function change_side (){
    
    right_racket.y = center_line;
    left_racket.y = center_line;
    ball.y = center_line;
    // ball.x = eval(side+"_line");


    if(side=='right'){
        ball.x = eval(side+"_line")-10;
    }
    else ball.x = eval(side+"_line")+10;


	// alert(side+' Goal');
    toastr['success'](side+' Goal');
	if(side=='left') 
        side='right';
	else 
        side='left';

    gameover=1;
    setTimeout(function() {
        gameover = 0;
    }, 2000);


}

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

var myGameArea = {
    canvas : document.createElement("canvas"),
    start : function() {
        this.canvas.width = 500;
        this.canvas.height = 600;
        this.context = this.canvas.getContext("2d");
        document.body.insertBefore(this.canvas, document.body.childNodes[0]);
        this.interval = setInterval(updateGameArea, 10);
         window.addEventListener('keydown', function (e) {
            myGameArea.key = e.keyCode;
           
        })
        window.addEventListener('keyup', function (e) {
            myGameArea.key = false;
        })
    },
    clear : function() {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }
}

function component(width, height, color, x, y,type) {
	this.type = type;
	if (type == "image") {
	    this.image = new Image();
	    this.image.src = color;
	  }
	  if(type=='text')
	  	this.text = 0;
    this.width = width;
    this.height = height;
    this.speedX = 0;
    this.speedY = 0;    
    this.x = x;
    this.y = y;    
    this.update = function(){
        ctx = myGameArea.context;
       if (this.type == "text") {
	      ctx.font = this.width + " " + this.height;
	      ctx.fillStyle = color;
	      ctx.fillText(this.text, this.x, this.y);
    	}
        if (type == "image") {
     	 ctx.drawImage(this.image, 
        this.x, 
        this.y,

        this.width, this.height);
	    } else {
	      ctx.fillStyle = color;
	      ctx.fillRect(this.x, this.y, this.width, this.height);
	    }

    }
    this.newPos = function() {
        this.x += this.speedX;
        this.y += this.speedY;        
    }
}

function updateGameArea() {

    myGameArea.clear();

    eval(turn+'_racket').speedX = 0;
    eval(turn+'_racket').speedY = 0;    
    if (myGameArea.key && myGameArea.key == 38) {
        eval(turn+'_racket').speedY = -1; sound_shoes1.play();
    }
    if (myGameArea.key && myGameArea.key == 40) {
        eval(turn+'_racket').speedY = 1; sound_shoes2.play();
    }
    eval(turn+'_racket').newPos();  
    
    if(right_score.text==15 || left_score.text==15)
    {
        gameover=1;
        toastr['success']('Left : '+left_score.text+" -- Right : "+right_score.text);
    }

    if(gameover==0) 
    {
	    if(direction_set==0){
	    	    direction = 1;
	    	    direction_set = 1 ;
	    }
    	    else {

    	    	if ( ball.x+5 == right_line && Math.abs(right_racket.y-ball.y)<25) 
    	    	{ if(side=='left') side='right'; else side='left';   sound_strike.play(); }

    	    	else if ( ball.x+5 == right_line && Math.abs(right_racket.y-ball.y)>=25) 
    	    	{ 
    	    	// left goal 
   		left_score.text += 1;
   		sound_gameover.play();
        
   		change_side();
   		// alert('left goal');
    	    	}

    	    	if ( ball.x+5 == left_line && Math.abs(left_racket.y-ball.y)<25) 
    	    	{ if(side=='left') side='right'; else side='left';   sound_strike.play(); }

    	    	else if ( ball.x+5 == left_line && Math.abs(left_racket.y-ball.y)>=25) 
    	    	{ 
    	    	// right goal 
  		right_score.text += 1;
  		sound_gameover.play();
       
  		change_side();
   		// alert('right goal');
    	    	}

    	    	if ( ball.y == top_line ) { if(direction==1) direction=2; else direction=1; }
    	    	if ( ball.y == bottom_line ) { if(direction==1) direction=2; else direction=1; }
    	    	if ( ball.x < center+70 && ball.x > center-70 ) { 
    	    		ball.width = 27;
    	    		ball.height = 27;
    	    	}
    	    	else { ball.width = 25;  ball.height = 25; }

	    if(side=='right'){
			switch (direction){
				case 1: 
					// 45 degree to top left
					ball.x -= 1;
					ball.y -= 1;
				break;
				case 2:
					// 45 degree to bottom left
					ball.x -= 1;
					ball.y += 1;
				break;
			}
		}
		else {
			switch (direction){
				case 1: 
					// 45 degree to top right
					ball.x += 1;
					ball.y -= 1;
				break;
				case 2:
					// 45 degree to bottom right
					ball.x += 1;
					ball.y += 1;
				break;
			}
		}
		}
	}
    // right_racket.x += 1;
    // left_racket.x += 1;    
    // left_racket.y += 1;        
    // ball.x -= 1;        
    // ball.y -= 1;            
    right_racket.update();
    left_racket.update();        
    ball.update();
    right_score.update();
    left_score.update();
    send_packet(eval(turn+'_racket').y);
}


function send_packet(ja){
		var msg = {
		message: ja,
		name: turn,
        };
		websocket.send(JSON.stringify(msg));
	}
</script>
<p style="left:160px; position:relative; margin-top:0px; width:300px;">Powered by : Farid Froozan</p>
</body>
</html>