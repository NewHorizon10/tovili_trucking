var cors            = require('cors')
const express       = require('express')
const bodyParser    = require('body-parser')

const app = express()
require('dotenv').config({ path: '.env' })
//constants
var config = require('./conf/config');
//Port
port = config.port;
//var io = require('socket.io').listen(app.listen(port));
var io = require('socket.io')(app.listen(port));
console.log('API server started on: ' + port);

var connection = require('./conf/db');
app.use(cors())
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
require('./chat')(app, io,connection);
