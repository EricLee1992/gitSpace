var fs = require('fs');
fs.readFile('hello.txt', function (err, data) {
	if (err) {
		return console.err(err);
	}
	console.log(data.toString());
});

console.log('end');