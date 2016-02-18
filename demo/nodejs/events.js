var events = require('events');
var eventEmitter = new events.EventEmitter();

//创建事件
var handler = function() {
	console.log('连接成功！');
	eventEmitter.emit('succ');
}

//绑定连接事件
eventEmitter.on('connect', handler);

eventEmitter.on('succ', function() {
	console.log('执行成功！');
});

eventEmitter.emit('connect');

console.log('完毕！');