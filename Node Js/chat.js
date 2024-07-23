var userDefinedFunction = require('./lib/user_defined_function.js');
var config = require('./conf/config')
const request    = require('request')
/**
 * Export a function, so that we can pass the app,io and connection instances from server.js
 *
 * @param app As Express Object
 * @param io As Socket Io Object
 * @param connection as Mysql Object
 *
 * @return void. 
 */
module.exports = function(app,io,connection){
	app.get('/', function(req, response){
		response.send({
			status 	: "success",
		});
	});


	
	app.post('/save-chat-history', function(req, response){
		var message  			= req.body.message;
		var sender_id  			= req.body.sender_id;
		var receiver_id  		= req.body.receiver_id;
		var channel_id  		= req.body.channel_id;
		console.log(req.body,'req')
		connection.query("SELECT name,email,phone_number,image from users where id='"+sender_id+"'", function (err, res) {
			if(!err){ 
				var created_at = userDefinedFunction.databaseFormat();
				connection.query("INSERT INTO chats (sender_id,receiver_id,channel_id,message,created_at,updated_at) values("+sender_id+",'"+receiver_id+"','"+channel_id+"','"+message+"','"+created_at+"','"+created_at+"')",function(err, rows, fields){
					if(!err){
						if(res[0]["image"] == "" || res[0]["image"] == "null" || res[0]["image"] == "NULL" || res[0]["image"] == null){
							var sender_image1	=	config.db.profile_no_image_http_path;
						}else {
							var sender_image1	=	config.db.profile_image_http_path+res[0]["image"];
						}
						
						io.in("user_"+receiver_id).emit('chatRoom',{
							message: message,
							sender_id: sender_id,
							receiver_id: receiver_id,
							sender_name: res[0]["name"],
							sender_image: sender_image1,
							datetime: created_at,
							date: "En este momento",
							channel_id: channel_id,
							message_id:rows.insertId
						});
						
						response.send({
							status 	: "success",
						});
					}else{
						response.send({
							status 	: "error",
						});
					}
				});
			}else{
				response.send({
					status 	: "error",
				});
			}
		});
	});

	
	var chat = io.on('connection', function (socket){
		socket.on('readmsg', function(data){
			connection.query("UPDATE chats SET is_read = 2 WHERE id = "+data.id, function(err, rows) {});
		});

		socket.on('loginChatRoom', function(data){
			console.log(data.room,'login1');
			socket.room				= data.room;
			socket.user_id			= data.room;
			socket.join(data.room);
			
			connection.query("UPDATE users SET is_online = 'online' WHERE id = "+data.room, function(err, rows) {});
			var is_online = 1;
			io.sockets.emit('is_user_online', {is_online: is_online,user_id:data.room});
		});



		

		socket.on('sendEmitMessage', function(data){
			console.log(data.data,'sdlfksakjdf')
	
			var image_data				=	data.data.image_data;
			var sender_name				=	data.data.sender_name;
			var sender_image			=	data.data.sender_image;
			var seller_id				=	data.data.receiver_id;
			var message					=	data.data.message;
			var size					=	data.data.size;
			var property_id				=	data.data.property_id;
			var original_name			=	data.data.original_name;
			var sender_id				=	data.data.sender_id;
			var is_online				=	data.data.is_online;
			
			console.log("user_"+seller_id);
			io.in("user_"+seller_id).emit('sendEmitMessageResponce',{
				image_data					: image_data,
				sender_image				: sender_image,
				sender_name					: sender_name,
				seller_id					: seller_id,
				message						: message,
				sender_id					: sender_id,
				size						: size,
				original_name				: original_name,
				is_online					: is_online,
				property_id					: property_id,
			});

		});
		
		socket.on('callDisconnect', function(data){
			
			var disconnect_id					=	data.data.disconnect_id;
			var session_id						=	data.data.session_id;
			var disconnect_name					=	data.data.disconnect_name;
			var call_by							=	data.data.call_by;
			var call_to							=	data.data.call_to;
			var is_owner_out					=	data.data.is_owner_out;
			var diconnectBy						=	data.data.diconnectBy;
			var rating_popup					=	data.data.rating_popup;


			io.in("user_"+call_by).emit('callDisconnectEmitResonse',{
				disconnect_id					: disconnect_id,
				session_id						: session_id,
				disconnect_name					: disconnect_name,
				call_by							: call_by,
				call_to							: call_to,
				is_owner_out					: is_owner_out,
				diconnectBy						: diconnectBy,
				rating_popup					: rating_popup,
				
			});

			io.in("user_"+call_to).emit('callDisconnectEmitResonse',{
				disconnect_id					: disconnect_id,
				session_id						: session_id,
				disconnect_name					: disconnect_name,
				call_by							: call_by,
				call_to							: call_to,
				is_owner_out					: is_owner_out,
				diconnectBy						: diconnectBy,
				rating_popup					: rating_popup,
				
			});

		});
		
		
		/**
		 * Function is called when any user disconnect with socket
		 *
		 * @param data as Data
		 *
		 * @return void. 
		 */	
		socket.on('disconnect', function(data) {
			socket.disconnect(socket.room);
		});// end disconnect
	});
};