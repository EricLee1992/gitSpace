var fs = require('fs');
//异步、非阻塞读取文件
fs.readFile('hello.txt', function (err, data) {
	if (err) {
		return console.err(err);
	}
	console.log(data.toString());
});

//同步、阻塞读取文件
var data = fs.readFileSync('hello.txt');

console.log(data.toString());
console.log('end');