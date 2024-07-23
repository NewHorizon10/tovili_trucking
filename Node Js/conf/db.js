let mysql = require('mysql');
let config = require('./config');
const util = require('util');


const pool = mysql.createPool({
    host     :  config.db.host,
    user     : config.db.user,
    password : config.db.pass,
    database : config.db.dbname,
    connectTimeout: 30000,
    multipleStatements: true,
});

function makeDb() {
  return {
    query(sql, args) {
      return util.promisify(pool.query).call(pool, sql, args);
    },
    close() {
      return util.promisify(pool.end).call(pool);
    }
  };
}

const connection = makeDb();
module.exports = connection;