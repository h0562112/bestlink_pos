var app = require('express')(); //引入express库
var cors = require('cors');
var http = require('http').Server(app); //将express注册到http中
var usocket = []; //全局变量
//new addition
var io = require('socket.io')(http);
//end

try{
	var WebSocket = require('ws');
	var loadIniFile = require('read-ini-file');
	var basic=loadIniFile.sync('./basic.ini');
	if(basic.type.main=='PosSystem'){
		var ini=loadIniFile.sync('../../database/initsetting.ini');
		var loginini=loadIniFile.sync('../../database/setup.ini');
	}
	else if(basic.type.main=='MainNidin'){
		var setup=loadIniFile.sync('../../fornidin/data/setup.ini');
		var nidinset=loadIniFile.sync('../../fornidin/data/nidinset.ini');
	}
	else{
	}
	//console.log('get ini file.');
}
catch (e){
	console.log('請安裝ws、read-ini-file。');
}

const corsOptions = {
  origin: '*',
  methods: 'GET,HEAD,PUT,PATCH,POST,DELETE,OPTIONS',
  allowedHeaders: ['Content-Type', 'Authorization'],
};

//app.use(cors(corsOptions));
app.use(cors());

//当访问根目录时，返回Hello World
/*app.get('/', function(req, res){
	res.sendFile(__dirname + '/index.html');
});*/

/*app.get('/message', function(req, res){
	//res.sendFile(__dirname + '/index.html');
	res.send('get message.');
});*/

var weconnect=function(){
	try{
		if(basic.type.main=='PosSystem'&&ini.nidin!==undefined&&ini.nidin.usenidin!==undefined&&ini.nidin.usenidin.replace(/"/g,'')==='1'){

			const buf=Buffer.from(loginini.nidin.id.replace(/"/g,'')+':'+loginini.nidin.pw.replace(/"/g,''));

			var ws=new WebSocket('wss://'+loginini.nidin.websocket.replace(/"/g,''),[],{
				headers:{'Authorization' : 'Basic '+buf.toString('base64')}
			});

			ws.on('open',function(msg){
				console.log('websocket connect');
			});

			ws.on('error',function(error){
				console.log('connectFailed.');
				console.log(error);
			});

			ws.on('message',function(data){
				console.log('received:',data);
				if(data.startsWith('"primus::ping::')){
					var pong=data.replace('ping','pong');
					console.log('send:',pong);
					ws.send(pong);
				}
				else{
					//usocket[name].emit("ding");
					console.log('get return message.');
					try{
						io.emit("ding");
						console.log('send ding to everyone');
					}
					catch(e){
						console.log(e);
					}
				}
			});

			ws.on('close',function(){
				console.log('close.');
				setTimeout(weconnect,10000);
			});
		}
		else if(basic.type.main=='MainNidin'&&setup.nodejsaddress.usenodejs.replace(/"/g,'')==='1'){
			const buf=Buffer.from(nidinset.init.id.replace(/"/g,'')+':'+nidinset.init.pw.replace(/"/g,''));

			var ws=new WebSocket('wss://'+nidinset.init.websocket.replace(/"/g,''),[],{
				headers:{'Authorization' : 'Basic '+buf.toString('base64')}
			});

			ws.on('open',function(msg){
				console.log('websocket connect');
			});

			ws.on('error',function(error){
				console.log('connectFailed.');
				console.log(error);
			});

			ws.on('message',function(data){
				console.log('received:',data);
				if(data.startsWith('"primus::ping::')){
					var pong=data.replace('ping','pong');
					console.log('send:',pong);
					ws.send(pong);
				}
				else{
					//usocket[name].emit("ding");
					console.log('get return message.');
					try{
						io.emit("ding");
						console.log('send ding to everyone');
					}
					catch(e){
						console.log(e);
					}
				}
			});

			ws.on('close',function(){
				console.log('close.');
				setTimeout(weconnect,10000);
			});
		}
		else{
			console.log('websocket connect fail.');
		}
	}
	catch(e){
		console.log(e);
	}
}
weconnect();

//new addition
io.on('connection', function(socket){
	var datetime=new Date();
	console.log(datetime.getFullYear()+'/'+(datetime.getMonth()+1)+'/'+datetime.getDate()+' '+('0'+datetime.getHours()).substr(-2)+':'+('0'+datetime.getMinutes()).substr(-2)+':'+('0'+datetime.getSeconds()).substr(-2)+' -- 一位使用者連線');

	//监听join事件
	socket.on("join", function (name) {
		usocket[name] = socket;
		io.emit("joinsuccess", name + ' login.'); //服务器通过广播将新用户发送给全体群聊成员
		//usocket[name].emit("joinsuccess", name + ' login.'); //服务器通过广播将新用户发送给全体群聊成员
		console.log(datetime.getFullYear()+'/'+(datetime.getMonth()+1)+'/'+datetime.getDate()+' '+('0'+datetime.getHours()).substr(-2)+':'+('0'+datetime.getMinutes()).substr(-2)+':'+('0'+datetime.getSeconds()).substr(-2)+' -- '+name + ' 登入.');
	});

	socket.on("secviewupdate",function(msgarray){
		if(typeof usocket['secview'+msgarray[0]]!=="undefined"){
			usocket['secview'+msgarray[0]].emit('secviewupdate',msgarray[1]);
			console.log(datetime.getFullYear()+'/'+(datetime.getMonth()+1)+'/'+datetime.getDate()+' '+('0'+datetime.getHours()).substr(-2)+':'+('0'+datetime.getMinutes()).substr(-2)+':'+('0'+datetime.getSeconds()).substr(-2)+' -- '+msgarray[0]+' 傳送訊息 "'+msgarray[1]+'" 給 secview'+msgarray[0]+'.');
		}
		else{
			console.log(datetime.getFullYear()+'/'+(datetime.getMonth()+1)+'/'+datetime.getDate()+' '+('0'+datetime.getHours()).substr(-2)+':'+('0'+datetime.getMinutes()).substr(-2)+':'+('0'+datetime.getSeconds()).substr(-2)+' -- secview'+msgarray[0]+' 尚未登入.');
		}
	});

	socket.on("identify",function(msgarray){
		usocket[msgarray[0]].emit('faceid',msgarray[1]);
		console.log(msgarray[0]+':'+msgarray[1]);
	});

	//new addition
	socket.on("message", function (msg) {
		if(msg=='i am secview.'){
			io.emit("message", 'hello secview. i am server'); //将新消息广播出去
		}
		else{
			io.emit("message", msg); //将新消息广播出去
		}
		console.log(datetime.getFullYear()+'/'+(datetime.getMonth()+1)+'/'+datetime.getDate()+' '+('0'+datetime.getHours()).substr(-2)+':'+('0'+datetime.getMinutes()).substr(-2)+':'+('0'+datetime.getSeconds()).substr(-2)+' -- '+msg);
	});

	//測試：手機點餐觸發點
	socket.on('sendorderweblist',function(name){
		usocket[name].emit('getorderweblist','not empty');
	});

	socket.on("disconnect", function(name){
		io.emit();
	});	
});
//end

//启动监听，监听3000端口
http.listen(3700, function(){
	console.log('listening on *:3700');
});