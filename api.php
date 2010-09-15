<h1>node(1) -- evented I/O for V8 JavaScript</h1>

<h2>Sinopsis</h2>

<p>Un ejemplo de un web server escrito con Node el cual responde con 'Hola Mundo':</p>

<pre><code>var http = require('http');

http.createServer(function (request, response) {
  response.writeHead(200, {'Content-Type': 'text/plain'});
  response.end('Hola mundo\n');
}).listen(8124);

console.log('Server corriendo en http://127.0.0.1:8124/');
</code></pre>

<p>Para correr el server, copie el código dentro de un archivo llamado <code>ejemplo.js</code> y ejecútelo con el programa node</p>

<pre><code>&gt; node ejemplo.js
Server corriendo en http://127.0.0.1:8124/
</code></pre>

<p>Todos los ejemplos en esta documentación puede ser corridos en forma similar.</p>

<h2>Módulos Estándar</h2>

<p>Node viene con un número de módulos que se compilan en el proceso, mayoría de los cuales se documentan a continuación.
La manera más común para usar estos módulos es con <code>require('name')</code> y luego asignar el valor devuelto a una variable local con el mismo nombre que el módulo.</p>

<p>Ejemplo:
    var sys = require('sys');</p>

<p>Es posible extender node con otros módulos. Ver <code>'Modulos'</code></p>

<h2>Buffers</h2>

<p>Javascript es un Unicode amigable pero no es agradable para datos binarios. Cuando
se trata de streams TCP o de un sistema de archivos es necesario manejar bytes.
Node tiene muchas estrategias para manipular, crear y consumir bytes.</p>

<p>Los datos en bruto se almacenan en instancias de la clase <code>Buffer</code>.
Un <code>Buffer</code> es similar a un array de enteros pero el cual corresponde a una asignación de memoria en bruto fuera de la pila de V8.
Un <code>Buffer</code> no pude ser redimensionado.</p>

<p>El <code>Buffer</code> es un objeto global.</p>

<p>La conversión entre Buffers y objetos string de javascript requiere un método explícito de codificación.
Aquí están las diferentes codificaciones de strings;</p>

<ul>
<li><p><code>'ascii'</code> - sólo para 7 bit de datos ASCII.  Este método de codificación es muy rápido, y se desechará el bit más alto si se setea.</p></li>
<li><p><code>'utf8'</code> - caracteres Unicode.  Muchas páginas webs y otros formatos de documentos utilizan UTF-8.</p></li>
<li><p><code>'base64'</code> - codificacion de cadena en Base64.</p></li>
<li><p><code>'binary'</code> - Una manera de codificar un dato binario en bruto dentro de strings es utilizando sólo los primeros 8 bits de cada caracter.
Este método de codificación es despreciado y debería ser evitado a favor de los objectos <code>Buffer</code> cuando sea posible.
Esta codificación será removida en futuras versiones de Node.</p></li>
</ul>

<h3>new Buffer(size)</h3>

<p>Asigna un nuevo buffer de <code>size</code> de octetos/bytes.</p>

<h3>new Buffer(array)</h3>

<p>Asigna un nuevo buffer utilizando un <code>array</code> de octetos/bytes.</p>

<h3>new Buffer(str, encoding='utf8')</h3>

<p>Asigna un nuevo buffer asignando el <code>str</code> dado.</p>

<h3>buffer.write(string, offset=0, encoding='utf8')</h3>

<p>Escribe <code>string</code> al buffer con <code>offset</code> utilizando la codificación dada.
Retorna el número de octetos/bytes escritos. Si <code>buffer</code> no contiene espacio suficiente para que entre el string completo escribirá una cantidad parcial del string.
En el caso de codificación <code>utf8</code>, el método escribirña caracteres parciales.</p>

<p>Ejemplo: escribe una cadena utf8 dentro de un bufer, luego lo imprime</p>

<pre><code>buf = new Buffer(256);
len = buf.write('\u00bd + \u00bc = \u00be', 0);
console.log(len + " bytes: " + buf.toString('utf8', 0, len));

// 12 bytes: ½ + ¼ = ¾
</code></pre>

<h3>buffer.toString(encoding, start=0, end=buffer.length)</h3>

<p>Decodifica y retorna un string desde los datos del buffer codificando con <code>encoding</code> comenzando en <code>start</code> y finalizando en <code>end</code>.</p>

<p>Ver above el ejemplo <code>buffer.write()</code>.</p>

<h3>buffer[index]</h3>

<p>Toma y setea el octeto en <code>index</code>. Los valores se refierens a los bytes individuales, por lo que el rango permitido es entre <code>0x00</code> y <code>0xFF</code> en hexa o <code>0</code> y <code>255</code>.</p>

<p>Ejemplo: copia un string ASCII dentro del buffer, un byte a la vez.</p>

<pre><code>str = "node.js";
buf = new Buffer(str.length);

for (var i = 0; i &lt; str.length ; i++) {
  buf[i] = str.charCodeAt(i);
}

console.log(buf);

// node.js
</code></pre>

<h3>Buffer.byteLength(string, encoding='utf8')</h3>

<p>Da la real longitud del byte de un string.
No es lo mismo que <code>String.prototype.length</code> ya que retorna el número de <em>caracteres</em> en un string.</p>

<p>Ejemplo:</p>

<pre><code>str = '\u00bd + \u00bc = \u00be';

console.log(str + ": " + str.length + " characters, " +
  Buffer.byteLength(str, 'utf8') + " bytes");

// ½ + ¼ = ¾: 9 characters, 12 bytes
</code></pre>

<h3>buffer.length</h3>

<p>The size of the buffer in bytes.  Note that this is not necessarily the size
of the contents. <code>length</code> refers to the amount of memory allocated for the 
buffer object.  It does not change when the contents of the buffer are changed.</p>

<pre><code>buf = new Buffer(1234);

console.log(buf.length);
buf.write("some string", "ascii", 0);
console.log(buf.length);

// 1234
// 1234
</code></pre>

<h3>buffer.copy(targetBuffer, targetStart, sourceStart, sourceEnd=buffer.length)</h3>

<p>Does a memcpy() between buffers.</p>

<p>Example: build two Buffers, then copy <code>buf1</code> from byte 16 through byte 19
into <code>buf2</code>, starting at the 8th byte in <code>buf2</code>.</p>

<pre><code>buf1 = new Buffer(26);
buf2 = new Buffer(26);

for (var i = 0 ; i &lt; 26 ; i++) {
  buf1[i] = i + 97; // 97 is ASCII a
  buf2[i] = 33; // ASCII !
}

buf1.copy(buf2, 8, 16, 20);
console.log(buf2.toString('ascii', 0, 25));

// !!!!!!!!qrst!!!!!!!!!!!!!
</code></pre>

<h3>buffer.slice(start, end)</h3>

<p>Returns a new buffer which references the
same memory as the old, but offset and cropped by the <code>start</code> and <code>end</code>
indexes.</p>

<p><strong>Modifying the new buffer slice will modify memory in the original buffer!</strong></p>

<p>Example: build a Buffer with the ASCII alphabet, take a slice, then modify one byte
from the original Buffer.</p>

<pre><code>var buf1 = new Buffer(26);

for (var i = 0 ; i &lt; 26 ; i++) {
  buf1[i] = i + 97; // 97 is ASCII a
}

var buf2 = buf1.slice(0, 3);
console.log(buf2.toString('ascii', 0, buf2.length));
buf1[0] = 33;
console.log(buf2.toString('ascii', 0, buf2.length));

// abc
// !bc
</code></pre>

<h2>EventEmitter</h2>

<p>Muchos objetos en Node emiten eventos: un servidor TCP emite un evento
cada vez que hay un stream, un proceso hijo emite un evento al salir. 
Todos los objetos que emiten eventos son instancias de <code>events.EventEmitter</code>.</p>

<p>Los eventos se representan mediante una cadena con estilo camel case. Algunos
ejemplos podrían ser: 
<code>'stream'</code>, <code>'data'</code>, <code>'messageBegin'</code>.</p>

<p>De esta manera, podemos agregar funciones a los objetos, para ser ejecutadas
cuando un evento es emitido. Estas funciones son llamadas <em>listeners</em>.</p>

<p><code>require('events').EventEmitter</code> para acceder a la clase <code>EventEmitter</code>.</p>

<p>Todos los emisores de eventos (EventEmitters) emiten el evento <code>'newListener'</code>
al ser agregadas nuevas funciones listeners.</p>

<p>Cuando un <code>EventEmitter</code> encuentra un error, la acción usual a tomar es emitir
un evento <code>'error'</code>. Los eventos de error son especiales--si no hay un handler
para ellos, mostrarán un stack trace y detendrán el programa.</p>

<h3>Evento: 'newListener'</h3>

<p><code>function (event, listener) { }</code></p>

<p>Este evento se emite al ser agregado un nuevo listener.</p>

<h3>Evento: 'error'</h3>

<p><code>function (exception) { }</code></p>

<p>Este evento es emitido al ser encontrado un error. Este evento es
especial - cuando no existen funciones listeners que reciban el error,
Node terminará la ejecución y presentará el stack trace de las
excepciones.</p>

<h3>emitter.on(event, listener)</h3>

<p>Agrega una función listener al final del arreglo de funciones listeners
para el evento especificado.</p>

<pre><code>server.on('stream', function (stream) {
  console.log('someone connected!');
});
</code></pre>

<h3>emitter.removeListener(event, listener)</h3>

<p>Elimina una función listener del arreglo de funciones listeners para
el evento especificado.
<strong>Precaucíón</strong>: esto modifica los índices del array en el arreglo de
listeners detrás del listener. </p>

<pre><code>var callback = function(stream) {
  console.log('someone connected!');
};
server.on('stream', callback);
// ...
server.removeListener('stream', callback);
</code></pre>

<h3>emitter.removeAllListeners(event)</h3>

<p>Elimina todos los listeners del arreglo de listeners para el evento
especificado.</p>

<h3>emitter.listeners(event)</h3>

<p>Devuelve un arreglo de listeners para el evento especificado. Este arreglo
puede ser modificado. Ej.: para eliminar listeners. </p>

<pre><code>server.on('stream', function (stream) {
  console.log('someone connected!');
});
console.log(sys.inspect(server.listeners('stream'));
// [ [Function] ]
</code></pre>

<h3>emitter.emit(event, [arg1], [arg2], [...])</h3>

<p>Ejecuta cada uno de los listeners de acuerdo al orden de los argumentos.</p>

<h2>Streams</h2>

<p>Un stream es una interface abstracta implementada por varios objetos en Node.
Por ejemplo, una solicitud a un servidor HTTP es un stream, como lo es stdout.
Los streams son de escritura, lectura o ambos. Todos los streams son instancias
de <code>EventEmitter</code>.</p>

<h2>Stream de lectura</h2>

<p>Un <code>Stream de lectura</code> tiene los siguientes métodos, miembros y eventos.</p>

<h3>Evento: 'data'</h3>

<p><code>function (data) { }</code></p>

<p>El evento <code>'data'</code> emite un <code>Buffer</code> (predeterminado) o una cadena si fue utilizado
<code>setEncoding()</code>.</p>

<h3>Evento: 'end'</h3>

<p><code>function () { }</code></p>

<p>Es emitido cuando el stream ha recibido un EOF (FIN en terminología TCP).
Indica que no habrán más eventos <code>'data'</code>. En caso que el stream sea de escritura, 
se podrá continuar escribiendo.</p>

<h3>Evento: 'error'</h3>

<p><code>function (exception) { }</code></p>

<p>Es emitido si hubo un error al recibir datos. </p>

<h3>Evento: 'close'</h3>

<p><code>function () { }</code>
Es emitido cuando el descriptor de archivos subyacente debe ser cerrado. No
todos los streams lo emitirán. Por ejemplo, una solicitud HTTP no emitirá
<code>'close'</code>.</p>

<h3>Evento: 'fd'</h3>

<p><code>function (fd) { }</code></p>

<p>Es emitido cuando el archivo descriptor es recibido en el stream. Solo streams
de Unix soportan esta funcionalidad; los demás, simplemente nunca emitirán este
evento.</p>

<p>&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;&lt;</p>

<h3>stream.readable</h3>

<p>A boolean that is <code>true</code> by default, but turns <code>false</code> after an <code>'error'</code>
occured, the stream came to an <code>'end'</code>, or <code>destroy()</code> was called.</p>

<h3>stream.setEncoding(encoding)</h3>

<p>Makes the data event emit a string instead of a <code>Buffer</code>. <code>encoding</code> can be
<code>'utf8'</code>, <code>'ascii'</code>, or <code>'base64'</code>.</p>

<h3>stream.pause()</h3>

<p>Pauses the incoming <code>'data'</code> events.</p>

<h3>stream.resume()</h3>

<p>Resumes the incoming <code>'data'</code> events after a <code>pause()</code>.</p>

<h3>stream.destroy()</h3>

<p>Closes the underlying file descriptor. Stream will not emit any more events.</p>

<h2>Writable Stream</h2>

<p>A <code>Writable Stream</code> has the following methods, members, and events.</p>

<h3>Event: 'drain'</h3>

<p><code>function () { }</code></p>

<p>Emitted after a <code>write()</code> method was called that returned <code>false</code> to
indicate that it is safe to write again.</p>

<h3>Event: 'error'</h3>

<p><code>function (exception) { }</code></p>

<p>Emitted on error with the exception <code>exception</code>.</p>

<h3>Event: 'close'</h3>

<p><code>function () { }</code></p>

<p>Emitted when the underlying file descriptor has been closed.</p>

<h3>stream.writeable</h3>

<p>A boolean that is <code>true</code> by default, but turns <code>false</code> after an <code>'error'</code>
occurred or <code>end()</code> / <code>destroy()</code> was called.</p>

<h3>stream.write(string, encoding='utf8', [fd])</h3>

<p>Writes <code>string</code> with the given <code>encoding</code> to the stream.  Returns <code>true</code> if
the string has been flushed to the kernel buffer.  Returns <code>false</code> to
indicate that the kernel buffer is full, and the data will be sent out in
the future. The <code>'drain'</code> event will indicate when the kernel buffer is
empty again. The <code>encoding</code> defaults to <code>'utf8'</code>.</p>

<p>If the optional <code>fd</code> parameter is specified, it is interpreted as an integral
file descriptor to be sent over the stream. This is only supported for UNIX
streams, and is silently ignored otherwise. When writing a file descriptor in
this manner, closing the descriptor before the stream drains risks sending an
invalid (closed) FD.</p>

<h3>stream.write(buffer)</h3>

<p>Same as the above except with a raw buffer.</p>

<h3>stream.end()</h3>

<p>Terminates the stream with EOF or FIN.</p>

<h3>stream.end(string, encoding)</h3>

<p>Sends <code>string</code> with the given <code>encoding</code> and terminates the stream with EOF
or FIN. This is useful to reduce the number of packets sent.</p>

<h3>stream.end(buffer)</h3>

<p>Same as above but with a <code>buffer</code>.</p>

<h3>stream.destroy()</h3>

<p>Closes the underlying file descriptor. Stream will not emit any more events.</p>

<h2>Global Objects</h2>

<p>These object are available in the global scope and can be accessed from anywhere.</p>

<h3>global</h3>

<p>The global namespace object.</p>

<h3>process</h3>

<p>The process object. See the <code>'process object'</code> section.</p>

<h3>require()</h3>

<p>To require modules. See the <code>'Modules'</code> section.</p>

<h3>require.paths</h3>

<p>An array of search paths for <code>require()</code>.  This array can be modified to add custom paths.</p>

<p>Example: add a new path to the beginning of the search list</p>

<pre><code>require.paths.unshift('/usr/local/node');
console.log(require.paths);
// /usr/local/node,/Users/mjr/.node_libraries
</code></pre>

<h3>__filename</h3>

<p>The filename of the script being executed.  This is the absolute path, and not necessarily
the same filename passed in as a command line argument.</p>

<p>Example: running <code>node example.js</code> from <code>/Users/mjr</code></p>

<pre><code>console.log(__filename);
// /Users/mjr/example.js
</code></pre>

<h3>__dirname</h3>

<p>The dirname of the script being executed.</p>

<p>Example: running <code>node example.js</code> from <code>/Users/mjr</code></p>

<pre><code>console.log(__dirname);
// /Users/mjr
</code></pre>

<h3>module</h3>

<p>A reference to the current module (of type <code>process.Module</code>). In particular
<code>module.exports</code> is the same as the <code>exports</code> object. See <code>src/process.js</code>
for more information.</p>

<h2>process</h2>

<p>The <code>process</code> object is a global object and can be accessed from anywhere.
It is an instance of <code>EventEmitter</code>.</p>

<h3>Event: 'exit'</h3>

<p><code>function () {}</code></p>

<p>Emitted when the process is about to exit.  This is a good hook to perform
constant time checks of the module's state (like for unit tests).  The main
event loop will no longer be run after the 'exit' callback finishes, so
timers may not be scheduled.</p>

<p>Example of listening for <code>exit</code>:</p>

<pre><code>process.on('exit', function () {
  process.nextTick(function () {
   console.log('This will not run');
  });
  console.log('About to exit.');
});
</code></pre>

<h3>Event: 'uncaughtException'</h3>

<p><code>function (err) { }</code></p>

<p>Emitted when an exception bubbles all the way back to the event loop. If a
listener is added for this exception, the default action (which is to print
a stack trace and exit) will not occur.</p>

<p>Example of listening for <code>uncaughtException</code>:</p>

<pre><code>process.on('uncaughtException', function (err) {
  console.log('Caught exception: ' + err);
});

setTimeout(function () {
  console.log('This will still run.');
}, 500);

// Intentionally cause an exception, but don't catch it.
nonexistentFunc();
console.log('This will not run.');
</code></pre>

<p>Note that <code>uncaughtException</code> is a very crude mechanism for exception
handling.  Using try / catch in your program will give you more control over
your program's flow.  Especially for server programs that are designed to
stay running forever, <code>uncaughtException</code> can be a useful safety mechanism.</p>

<h3>Signal Events</h3>

<p><code>function () {}</code></p>

<p>Emitted when the processes receives a signal. See sigaction(2) for a list of
standard POSIX signal names such as SIGINT, SIGUSR1, etc.</p>

<p>Example of listening for <code>SIGINT</code>:</p>

<pre><code>var stdin = process.openStdin();

process.on('SIGINT', function () {
  console.log('Got SIGINT.  Press Control-D to exit.');
});
</code></pre>

<p>An easy way to send the <code>SIGINT</code> signal is with <code>Control-C</code> in most terminal
programs.</p>

<h3>process.stdout</h3>

<p>A <code>Writable Stream</code> to <code>stdout</code>.</p>

<p>Example: the definition of <code>console.log</code></p>

<pre><code>console.log = function (d) {
  process.stdout.write(d + '\n');
};
</code></pre>

<h3>process.openStdin()</h3>

<p>Opens the standard input stream, returns a <code>Readable Stream</code>.</p>

<p>Example of opening standard input and listening for both events:</p>

<pre><code>var stdin = process.openStdin();

stdin.setEncoding('utf8');

stdin.on('data', function (chunk) {
  process.stdout.write('data: ' + chunk);
});

stdin.on('end', function () {
  process.stdout.write('end');
});
</code></pre>

<h3>process.argv</h3>

<p>An array containing the command line arguments.  The first element will be
'node', the second element will be the name of the JavaScript file.  The
next elements will be any additional command line arguments.</p>

<pre><code>// print process.argv
process.argv.forEach(function (val, index, array) {
  console.log(index + ': ' + val);
});
</code></pre>

<p>This will generate:</p>

<pre><code>$ node process-2.js one two=three four
0: node
1: /Users/mjr/work/node/process-2.js
2: one
3: two=three
4: four
</code></pre>

<h3>process.execPath</h3>

<p>This is the absolute pathname of the executable that started the process.</p>

<p>Example:</p>

<pre><code>/usr/local/bin/node
</code></pre>

<h3>process.chdir(directory)</h3>

<p>Changes the current working directory of the process or throws an exception if that fails.</p>

<pre><code>console.log('Starting directory: ' + process.cwd());
try {
  process.chdir('/tmp');
  console.log('New directory: ' + process.cwd());
}
catch (err) {
  console.log('chdir: ' + err);
}
</code></pre>

<h3>process.compile(code, filename)</h3>

<p>Similar to <code>eval</code> except that you can specify a <code>filename</code> for better
error reporting and the <code>code</code> cannot see the local scope.  The value of <code>filename</code>
will be used as a filename if a stack trace is generated by the compiled code.</p>

<p>Example of using <code>process.compile</code> and <code>eval</code> to run the same code:</p>

<pre><code>var localVar = 123,
    compiled, evaled;

compiled = process.compile('localVar = 1;', 'myfile.js');
console.log('localVar: ' + localVar + ', compiled: ' + compiled);
evaled = eval('localVar = 1;');
console.log('localVar: ' + localVar + ', evaled: ' + evaled);

// localVar: 123, compiled: 1
// localVar: 1, evaled: 1
</code></pre>

<p><code>process.compile</code> does not have access to the local scope, so <code>localVar</code> is unchanged.
<code>eval</code> does have access to the local scope, so <code>localVar</code> is changed.</p>

<p>In case of syntax error in <code>code</code>, <code>process.compile</code> exits node.</p>

<p>See also: <code>Script</code></p>

<h3>process.cwd()</h3>

<p>Returns the current working directory of the process.</p>

<pre><code>console.log('Current directory: ' + process.cwd());
</code></pre>

<h3>process.env</h3>

<p>An object containing the user environment. See environ(7).</p>

<h3>process.exit(code=0)</h3>

<p>Ends the process with the specified <code>code</code>.  If omitted, exit uses the 
'success' code <code>0</code>.</p>

<p>To exit with a 'failure' code:</p>

<pre><code>process.exit(1);
</code></pre>

<p>The shell that executed node should see the exit code as 1.</p>

<h3>process.getgid()</h3>

<p>Gets the group identity of the process. (See getgid(2).)  This is the numerical group id, not the group name.</p>

<pre><code>console.log('Current gid: ' + process.getgid());
</code></pre>

<h3>process.setgid(id)</h3>

<p>Sets the group identity of the process. (See setgid(2).)  This accepts either a numerical ID or a groupname string.  If a groupname is specified, this method blocks while resolving it to a numerical ID.</p>

<pre><code>console.log('Current gid: ' + process.getgid());
try {
  process.setgid(501);
  console.log('New gid: ' + process.getgid());
}
catch (err) {
  console.log('Failed to set gid: ' + err);
}
</code></pre>

<h3>process.getuid()</h3>

<p>Gets the user identity of the process. (See getuid(2).)  This is the numerical userid, not the username.</p>

<pre><code>console.log('Current uid: ' + process.getuid());
</code></pre>

<h3>process.setuid(id)</h3>

<p>Sets the user identity of the process. (See setuid(2).)  This accepts either a numerical ID or a username string.  If a username is specified, this method blocks while resolving it to a numerical ID.</p>

<pre><code>console.log('Current uid: ' + process.getuid());
try {
  process.setuid(501);
  console.log('New uid: ' + process.getuid());
}
catch (err) {
  console.log('Failed to set uid: ' + err);
}
</code></pre>

<h3>process.version</h3>

<p>A compiled-in property that exposes <code>NODE_VERSION</code>.</p>

<pre><code>console.log('Version: ' + process.version);
</code></pre>

<h3>process.installPrefix</h3>

<p>A compiled-in property that exposes <code>NODE_PREFIX</code>.</p>

<pre><code>console.log('Prefix: ' + process.installPrefix);
</code></pre>

<h3>process.kill(pid, signal='SIGINT')</h3>

<p>Send a signal to a process. <code>pid</code> is the process id and <code>signal</code> is the
string describing the signal to send.  Signal names are strings like
'SIGINT' or 'SIGUSR1'.  If omitted, the signal will be 'SIGINT'.
See kill(2) for more information.</p>

<p>Note that just because the name of this function is <code>process.kill</code>, it is
really just a signal sender, like the <code>kill</code> system call.  The signal sent
may do something other than kill the target process.</p>

<p>Example of sending a signal to yourself:</p>

<pre><code>process.on('SIGHUP', function () {
  console.log('Got SIGHUP signal.');
});

setTimeout(function () {
  console.log('Exiting.');
  process.exit(0);
}, 100);

process.kill(process.pid, 'SIGHUP');
</code></pre>

<h3>process.pid</h3>

<p>The PID of the process.</p>

<pre><code>console.log('This process is pid ' + process.pid);
</code></pre>

<h3>process.title</h3>

<p>Getter/setter to set what is displayed in 'ps'.</p>

<h3>process.platform</h3>

<p>What platform you're running on. <code>'linux2'</code>, <code>'darwin'</code>, etc.</p>

<pre><code>console.log('This platform is ' + process.platform);
</code></pre>

<h3>process.memoryUsage()</h3>

<p>Returns an object describing the memory usage of the Node process.</p>

<pre><code>var sys = require('sys');

console.log(sys.inspect(process.memoryUsage()));
</code></pre>

<p>This will generate:</p>

<pre><code>{ rss: 4935680
, vsize: 41893888
, heapTotal: 1826816
, heapUsed: 650472
}
</code></pre>

<p><code>heapTotal</code> and <code>heapUsed</code> refer to V8's memory usage.</p>

<h3>process.nextTick(callback)</h3>

<p>On the next loop around the event loop call this callback.
This is <em>not</em> a simple alias to <code>setTimeout(fn, 0)</code>, it's much more
efficient.</p>

<pre><code>process.nextTick(function () {
  console.log('nextTick callback');
});
</code></pre>

<h3>process.umask([mask])</h3>

<p>Sets or reads the process's file mode creation mask. Child processes inherit
the mask from the parent process. Returns the old mask if <code>mask</code> argument is
given, otherwise returns the current mask.</p>

<pre><code>var oldmask, newmask = 0644;

oldmask = process.umask(newmask);
console.log('Changed umask from: ' + oldmask.toString(8) +
            ' to ' + newmask.toString(8));
</code></pre>

<h2>sys</h2>

<p>These functions are in the module <code>'sys'</code>. Use <code>require('sys')</code> to access
them.</p>

<h3>sys.print(string)</h3>

<p>Like <code>console.log()</code> but without the trailing newline.</p>

<pre><code>require('sys').print('String with no newline');
</code></pre>

<h3>sys.debug(string)</h3>

<p>A synchronous output function. Will block the process and
output <code>string</code> immediately to <code>stderr</code>.</p>

<pre><code>require('sys').debug('message on stderr');
</code></pre>

<h3>sys.log(string)</h3>

<p>Output with timestamp on <code>stdout</code>.</p>

<pre><code>require('sys').log('Timestmaped message.');
</code></pre>

<h3>sys.inspect(object, showHidden=false, depth=2)</h3>

<p>Return a string representation of <code>object</code>, which is useful for debugging.</p>

<p>If <code>showHidden</code> is <code>true</code>, then the object's non-enumerable properties will be
shown too.</p>

<p>If <code>depth</code> is provided, it tells <code>inspect</code> how many times to recurse while
formatting the object. This is useful for inspecting large complicated objects.</p>

<p>The default is to only recurse twice.  To make it recurse indefinitely, pass
in <code>null</code> for <code>depth</code>.</p>

<p>Example of inspecting all properties of the <code>sys</code> object:</p>

<pre><code>var sys = require('sys');

console.log(sys.inspect(sys, true, null));
</code></pre>

<h3>sys.pump(readableStream, writeableStream, [callback])</h3>

<p>Experimental</p>

<p>Read the data from <code>readableStream</code> and send it to the <code>writableStream</code>.
When <code>writeableStream.write(data)</code> returns <code>false</code> <code>readableStream</code> will be
paused until the <code>drain</code> event occurs on the <code>writableStream</code>. <code>callback</code> gets
an error as its only argument and is called when <code>writableStream</code> is closed or
when an error occurs.</p>

<h2>Timers</h2>

<h3>setTimeout(callback, delay, [arg], [...])</h3>

<p>To schedule execution of <code>callback</code> after <code>delay</code> milliseconds. Returns a
<code>timeoutId</code> for possible use with <code>clearTimeout()</code>. Optionally, you can
also pass arguments to the callback.</p>

<h3>clearTimeout(timeoutId)</h3>

<p>Prevents a timeout from triggering.</p>

<h3>setInterval(callback, delay, [arg], [...])</h3>

<p>To schedule the repeated execution of <code>callback</code> every <code>delay</code> milliseconds.
Returns a <code>intervalId</code> for possible use with <code>clearInterval()</code>. Optionally,
you can also pass arguments to the callback.</p>

<h3>clearInterval(intervalId)</h3>

<p>Stops a interval from triggering.</p>

<h2>Child Processes</h2>

<p>Node provides a tri-directional <code>popen(3)</code> facility through the <code>ChildProcess</code>
class.</p>

<p>It is possible to stream data through the child's <code>stdin</code>, <code>stdout</code>, and
<code>stderr</code> in a fully non-blocking way.</p>

<p>To create a child process use <code>require('child_process').spawn()</code>.</p>

<p>Child processes always have three streams associated with them. <code>child.stdin</code>,
<code>child.stdout</code>, and <code>child.stderr</code>.</p>

<p><code>ChildProcess</code> is an <code>EventEmitter</code>.</p>

<h3>Event:  'exit'</h3>

<p><code>function (code, signal) {}</code></p>

<p>This event is emitted after the child process ends. If the process terminated
normally, <code>code</code> is the final exit code of the process, otherwise <code>null</code>. If
the process terminated due to receipt of a signal, <code>signal</code> is the string name
of the signal, otherwise <code>null</code>.</p>

<p>After this event is emitted, the <code>'output'</code> and <code>'error'</code> callbacks will no
longer be made.</p>

<p>See <code>waitpid(2)</code>.</p>

<h3>child.stdin</h3>

<p>A <code>Writable Stream</code> that represents the child process's <code>stdin</code>.
Closing this stream via <code>end()</code> often causes the child process to terminate.</p>

<h3>child.stdout</h3>

<p>A <code>Readable Stream</code> that represents the child process's <code>stdout</code>.</p>

<h3>child.stderr</h3>

<p>A <code>Readable Stream</code> that represents the child process's <code>stderr</code>.</p>

<h3>child.pid</h3>

<p>The PID of the child process.</p>

<p>Example:</p>

<pre><code>var spawn = require('child_process').spawn,
    grep  = spawn('grep', ['ssh']);

console.log('Spawned child pid: ' + grep.pid);
grep.stdin.end();
</code></pre>

<h3>child_process.spawn(command, args=[], [options])</h3>

<p>Launches a new process with the given <code>command</code>, with  command line arguments in <code>args</code>.
If omitted, <code>args</code> defaults to an empty Array.</p>

<p>The third argument is used to specify additional options, which defaults to:</p>

<pre><code>{ cwd: undefined
, env: process.env,
, customFds: [-1, -1, -1]
}
</code></pre>

<p><code>cwd</code> allows you to specify the working directory from which the process is spawned.
Use <code>env</code> to specify environment variables that will be visible to the new process.
With <code>customFds</code> it is possible to hook up the new process' [stdin, stout, stderr] to
existing streams; <code>-1</code> means that a new stream should be created.</p>

<p>Example of running <code>ls -lh /usr</code>, capturing <code>stdout</code>, <code>stderr</code>, and the exit code:</p>

<pre><code>var sys   = require('sys'),
    spawn = require('child_process').spawn,
    ls    = spawn('ls', ['-lh', '/usr']);

ls.stdout.on('data', function (data) {
  sys.print('stdout: ' + data);
});

ls.stderr.on('data', function (data) {
  sys.print('stderr: ' + data);
});

ls.on('exit', function (code) {
  console.log('child process exited with code ' + code);
});
</code></pre>

<p>Example: A very elaborate way to run 'ps ax | grep ssh'</p>

<pre><code>var sys   = require('sys'),
    spawn = require('child_process').spawn,
    ps    = spawn('ps', ['ax']),
    grep  = spawn('grep', ['ssh']);

ps.stdout.on('data', function (data) {
  grep.stdin.write(data);
});

ps.stderr.on('data', function (data) {
  sys.print('ps stderr: ' + data);
});

ps.on('exit', function (code) {
  if (code !== 0) {
    console.log('ps process exited with code ' + code);
  }
  grep.stdin.end();
});

grep.stdout.on('data', function (data) {
  sys.print(data);
});

grep.stderr.on('data', function (data) {
  sys.print('grep stderr: ' + data);
});

grep.on('exit', function (code) {
  if (code !== 0) {
    console.log('grep process exited with code ' + code);
  }
});
</code></pre>

<p>Example of checking for failed exec:</p>

<pre><code>var spawn = require('child_process').spawn,
    child = spawn('bad_command');

child.stderr.on('data', function (data) {
  if (/^execvp\(\)/.test(data.asciiSlice(0,data.length))) {
    console.log('Failed to start child process.');
  }
});
</code></pre>

<p>See also: <code>child_process.exec()</code></p>

<h3>child_process.exec(command, [options], callback)</h3>

<p>High-level way to execute a command as a child process, buffer the
output, and return it all in a callback.</p>

<pre><code>var sys   = require('sys'),
    exec  = require('child_process').exec,
    child;

child = exec('cat *.js bad_file | wc -l', 
  function (error, stdout, stderr) {
    sys.print('stdout: ' + stdout);
    sys.print('stderr: ' + stderr);
    if (error !== null) {
      console.log('exec error: ' + error);
    }
});
</code></pre>

<p>The callback gets the arguments <code>(error, stdout, stderr)</code>. On success, <code>error</code>
will be <code>null</code>.  On error, <code>error</code> will be an instance of <code>Error</code> and <code>err.code</code>
will be the exit code of the child process, and <code>err.signal</code> will be set to the
signal that terminated the process.</p>

<p>There is a second optional argument to specify several options. The default options are</p>

<pre><code>{ encoding: 'utf8'
, timeout: 0
, maxBuffer: 200*1024
, killSignal: 'SIGKILL'
, cwd: null
, env: null
}
</code></pre>

<p>If <code>timeout</code> is greater than 0, then it will kill the child process
if it runs longer than <code>timeout</code> milliseconds. The child process is killed with
<code>killSignal</code> (default: <code>'SIGKILL'</code>). <code>maxBuffer</code> specifies the largest
amount of data allowed on stdout or stderr - if this value is exceeded then
the child process is killed.</p>

<h3>child.kill(signal='SIGTERM')</h3>

<p>Send a signal to the child process. If no argument is given, the process will
be sent <code>'SIGTERM'</code>. See <code>signal(7)</code> for a list of available signals.</p>

<pre><code>var spawn = require('child_process').spawn,
    grep  = spawn('grep', ['ssh']);

grep.on('exit', function (code, signal) {
  console.log('child process terminated due to receipt of signal '+signal);
});

// send SIGHUP to process
grep.kill('SIGHUP');
</code></pre>

<p>Note that while the function is called <code>kill</code>, the signal delivered to the child
process may not actually kill it.  <code>kill</code> really just sends a signal to a process.</p>

<p>See <code>kill(2)</code></p>

<h2>Script</h2>

<p><code>Script</code> class compiles and runs JavaScript code. You can access this class with:</p>

<pre><code>var Script = process.binding('evals').Script;
</code></pre>

<p>New JavaScript code can be compiled and run immediately or compiled, saved, and run later.</p>

<h3>Script.runInThisContext(code, [filename])</h3>

<p>Similar to <code>process.compile</code>.  <code>Script.runInThisContext</code> compiles <code>code</code> as if it were loaded from <code>filename</code>,
runs it and returns the result. Running code does not have access to local scope. <code>filename</code> is optional.</p>

<p>Example of using <code>Script.runInThisContext</code> and <code>eval</code> to run the same code:</p>

<pre><code>var localVar = 123,
    usingscript, evaled,
    Script = process.binding('evals').Script;

usingscript = Script.runInThisContext('localVar = 1;',
  'myfile.js');
console.log('localVar: ' + localVar + ', usingscript: ' +
  usingscript);
evaled = eval('localVar = 1;');
console.log('localVar: ' + localVar + ', evaled: ' +
  evaled);

// localVar: 123, usingscript: 1
// localVar: 1, evaled: 1
</code></pre>

<p><code>Script.runInThisContext</code> does not have access to the local scope, so <code>localVar</code> is unchanged.
<code>eval</code> does have access to the local scope, so <code>localVar</code> is changed.</p>

<p>In case of syntax error in <code>code</code>, <code>Script.runInThisContext</code> emits the syntax error to stderr
and throws.an exception.</p>

<h3>Script.runInNewContext(code, [sandbox], [filename])</h3>

<p><code>Script.runInNewContext</code> compiles <code>code</code> to run in <code>sandbox</code> as if it were loaded from <code>filename</code>,
then runs it and returns the result. Running code does not have access to local scope and
the object <code>sandbox</code> will be used as the global object for <code>code</code>.
<code>sandbox</code> and <code>filename</code> are optional.</p>

<p>Example: compile and execute code that increments a global variable and sets a new one.
These globals are contained in the sandbox.</p>

<pre><code>var sys = require('sys'),
    Script = process.binding('evals').Script,
    sandbox = {
      animal: 'cat',
      count: 2
    };

Script.runInNewContext(
  'count += 1; name = "kitty"', sandbox, 'myfile.js');
console.log(sys.inspect(sandbox));

// { animal: 'cat', count: 3, name: 'kitty' }
</code></pre>

<p>Note that running untrusted code is a tricky business requiring great care.  To prevent accidental
global variable leakage, <code>Script.runInNewContext</code> is quite useful, but safely running untrusted code
requires a separate process.</p>

<p>In case of syntax error in <code>code</code>, <code>Script.runInThisContext</code> emits the syntax error to stderr
and throws an exception.</p>

<h3>new Script(code, [filename])</h3>

<p><code>new Script</code> compiles <code>code</code> as if it were loaded from <code>filename</code>,
but does not run it. Instead, it returns a <code>Script</code> object representing this compiled code.
This script can be run later many times using methods below.
The returned script is not bound to any global object.
It is bound before each run, just for that run. <code>filename</code> is optional.</p>

<p>In case of syntax error in <code>code</code>, <code>new Script</code> emits the syntax error to stderr
and throws an exception.</p>

<h3>script.runInThisContext()</h3>

<p>Similar to <code>Script.runInThisContext</code> (note capital 'S'), but now being a method of a precompiled Script object.
<code>script.runInThisContext</code> runs the code of <code>script</code> and returns the result.
Running code does not have access to local scope, but does have access to the <code>global</code> object
(v8: in actual context).</p>

<p>Example of using <code>script.runInThisContext</code> to compile code once and run it multiple times:</p>

<pre><code>var Script = process.binding('evals').Script,
    scriptObj, i;

globalVar = 0;

scriptObj = new Script('globalVar += 1', 'myfile.js');

for (i = 0; i &lt; 1000 ; i += 1) {
  scriptObj.runInThisContext();
}

console.log(globalVar);

// 1000
</code></pre>

<h3>script.runInNewContext([sandbox])</h3>

<p>Similar to <code>Script.runInNewContext</code> (note capital 'S'), but now being a method of a precompiled Script object.
<code>script.runInNewContext</code> runs the code of <code>script</code> with <code>sandbox</code> as the global object and returns the result.
Running code does not have access to local scope. <code>sandbox</code> is optional.</p>

<p>Example: compile code that increments a global variable and sets one, then execute this code multiple times.
These globals are contained in the sandbox.</p>

<pre><code>var sys = require('sys'),
    Script = process.binding('evals').Script,
    scriptObj, i,
    sandbox = {
      animal: 'cat',
      count: 2
    };

scriptObj = new Script(
    'count += 1; name = "kitty"', 'myfile.js');

for (i = 0; i &lt; 10 ; i += 1) {
  scriptObj.runInNewContext(sandbox);
}

console.log(sys.inspect(sandbox));

// { animal: 'cat', count: 12, name: 'kitty' }
</code></pre>

<p>Note that running untrusted code is a tricky business requiring great care.  To prevent accidental
global variable leakage, <code>script.runInNewContext</code> is quite useful, but safely running untrusted code
requires a separate process.</p>

<h2>File System</h2>

<p>File I/O is provided by simple wrappers around standard POSIX functions.  To
use this module do <code>require('fs')</code>. All the methods have asynchronous and
synchronous forms. </p>

<p>The asynchronous form always take a completion callback as its last argument.
The arguments passed to the completion callback depend on the method, but the
first argument is always reserved for an exception. If the operation was
completed successfully, then the first argument will be <code>null</code> or <code>undefined</code>.</p>

<p>Here is an example of the asynchronous version:</p>

<pre><code>var fs = require('fs');

fs.unlink('/tmp/hello', function (err) {
  if (err) throw err;
  console.log('successfully deleted /tmp/hello');
});
</code></pre>

<p>Here is the synchronous version:</p>

<pre><code>var fs = require('fs');

fs.unlinkSync('/tmp/hello')
console.log('successfully deleted /tmp/hello');
</code></pre>

<p>With the asynchronous methods there is no guaranteed ordering. So the
following is prone to error:</p>

<pre><code>fs.rename('/tmp/hello', '/tmp/world', function (err) {
  if (err) throw err;
  console.log('renamed complete');
});
fs.stat('/tmp/world', function (err, stats) {
  if (err) throw err;
  console.log('stats: ' + JSON.stringify(stats));
});
</code></pre>

<p>It could be that <code>fs.stat</code> is executed before <code>fs.rename</code>.
The correct way to do this is to chain the callbacks.</p>

<pre><code>fs.rename('/tmp/hello', '/tmp/world', function (err) {
  if (err) throw err;
  fs.stat('/tmp/world', function (err, stats) {
    if (err) throw err;
    console.log('stats: ' + JSON.stringify(stats));
  });
});
</code></pre>

<p>In busy processes, the programmer is <em>strongly encouraged</em> to use the
asynchronous versions of these calls. The synchronous versions will block
the entire process until they complete--halting all connections.</p>

<h3>fs.rename(path1, path2, [callback])</h3>

<p>Asynchronous rename(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.renameSync(path1, path2)</h3>

<p>Synchronous rename(2).</p>

<h3>fs.truncate(fd, len, [callback])</h3>

<p>Asynchronous ftruncate(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.truncateSync(fd, len)</h3>

<p>Synchronous ftruncate(2).</p>

<h3>fs.chmod(path, mode, [callback])</h3>

<p>Asynchronous chmod(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.chmodSync(path, mode)</h3>

<p>Synchronous chmod(2).</p>

<h3>fs.stat(path, [callback])</h3>

<p>Asynchronous stat(2). The callback gets two arguments <code>(err, stats)</code> where <code>stats</code> is a <code>fs.Stats</code> object. It looks like this:</p>

<pre><code>{ dev: 2049
, ino: 305352
, mode: 16877
, nlink: 12
, uid: 1000
, gid: 1000
, rdev: 0
, size: 4096
, blksize: 4096
, blocks: 8
, atime: '2009-06-29T11:11:55Z'
, mtime: '2009-06-29T11:11:40Z'
, ctime: '2009-06-29T11:11:40Z' 
}
</code></pre>

<p>See the <code>fs.Stats</code> section below for more information.</p>

<h3>fs.lstat(path, [callback])</h3>

<p>Asynchronous lstat(2). The callback gets two arguments <code>(err, stats)</code> where <code>stats</code> is a <code>fs.Stats</code> object.</p>

<h3>fs.fstat(fd, [callback])</h3>

<p>Asynchronous fstat(2). The callback gets two arguments <code>(err, stats)</code> where <code>stats</code> is a <code>fs.Stats</code> object.</p>

<h3>fs.statSync(path)</h3>

<p>Synchronous stat(2). Returns an instance of <code>fs.Stats</code>.</p>

<h3>fs.lstatSync(path)</h3>

<p>Synchronous lstat(2). Returns an instance of <code>fs.Stats</code>.</p>

<h3>fs.fstatSync(fd)</h3>

<p>Synchronous fstat(2). Returns an instance of <code>fs.Stats</code>.</p>

<h3>fs.link(srcpath, dstpath, [callback])</h3>

<p>Asynchronous link(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.linkSync(dstpath, srcpath)</h3>

<p>Synchronous link(2).</p>

<h3>fs.symlink(linkdata, path, [callback])</h3>

<p>Asynchronous symlink(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.symlinkSync(linkdata, path)</h3>

<p>Synchronous symlink(2).</p>

<h3>fs.readlink(path, [callback])</h3>

<p>Asynchronous readlink(2). The callback gets two arguments <code>(err, resolvedPath)</code>. </p>

<h3>fs.readlinkSync(path)</h3>

<p>Synchronous readlink(2). Returns the resolved path.</p>

<h3>fs.realpath(path, [callback])</h3>

<p>Asynchronous realpath(2).  The callback gets two arguments <code>(err, resolvedPath)</code>.</p>

<h3>fs.realpathSync(path)</h3>

<p>Synchronous realpath(2). Returns the resolved path.</p>

<h3>fs.unlink(path, [callback])</h3>

<p>Asynchronous unlink(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.unlinkSync(path)</h3>

<p>Synchronous unlink(2).</p>

<h3>fs.rmdir(path, [callback])</h3>

<p>Asynchronous rmdir(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.rmdirSync(path)</h3>

<p>Synchronous rmdir(2).</p>

<h3>fs.mkdir(path, mode, [callback])</h3>

<p>Asynchronous mkdir(2). No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.mkdirSync(path, mode)</h3>

<p>Synchronous mkdir(2).</p>

<h3>fs.readdir(path, [callback])</h3>

<p>Asynchronous readdir(3).  Reads the contents of a directory.
The callback gets two arguments <code>(err, files)</code> where <code>files</code> is an array of
the names of the files in the directory excluding <code>'.'</code> and <code>'..'</code>.</p>

<h3>fs.readdirSync(path)</h3>

<p>Synchronous readdir(3). Returns an array of filenames excluding <code>'.'</code> and
<code>'..'</code>.</p>

<h3>fs.close(fd, [callback])</h3>

<p>Asynchronous close(2).  No arguments other than a possible exception are given to the completion callback.</p>

<h3>fs.closeSync(fd)</h3>

<p>Synchronous close(2).</p>

<h3>fs.open(path, flags, mode=0666, [callback])</h3>

<p>Asynchronous file open. See open(2). Flags can be 'r', 'r+', 'w', 'w+', 'a',
or 'a+'. The callback gets two arguments <code>(err, fd)</code>. </p>

<h3>fs.openSync(path, flags, mode=0666)</h3>

<p>Synchronous open(2). </p>

<h3>fs.write(fd, buffer, offset, length, position, [callback])</h3>

<p>Write <code>buffer</code> to the file specified by <code>fd</code>.</p>

<p><code>offset</code> and <code>length</code> determine the part of the buffer to be written.</p>

<p><code>position</code> refers to the offset from the beginning of the file where this data
should be written. If <code>position</code> is <code>null</code>, the data will be written at the
current position.
See pwrite(2).</p>

<p>The callback will be given two arguments <code>(err, written)</code> where <code>written</code>
specifies how many <em>bytes</em> were written.</p>

<h3>fs.write(fd, str, position, encoding='utf8', [callback])</h3>

<p>Write the entire string <code>str</code> using the given <code>encoding</code> to the file specified
by <code>fd</code>.</p>

<p><code>position</code> refers to the offset from the beginning of the file where this data
should be written. If <code>position</code> is <code>null</code>, the data will be written at the
current position.
See pwrite(2).</p>

<p>The callback will be given two arguments <code>(err, written)</code> where <code>written</code>
specifies how many <em>bytes</em> were written.</p>

<h3>fs.writeSync(fd, buffer, offset, length, position)</h3>

<p>Synchronous version of buffer-based <code>fs.write()</code>. Returns the number of bytes written.</p>

<h3>fs.writeSync(fd, str, position, encoding='utf8')</h3>

<p>Synchronous version of string-based <code>fs.write()</code>. Returns the number of bytes written.</p>

<h3>fs.read(fd, buffer, offset, length, position, [callback])</h3>

<p>Read data from the file specified by <code>fd</code>.</p>

<p><code>buffer</code> is the buffer that the data will be written to.</p>

<p><code>offset</code> is offset within the buffer where writing will start.</p>

<p><code>length</code> is an integer specifying the number of bytes to read.</p>

<p><code>position</code> is an integer specifying where to begin reading from in the file.
If <code>position</code> is <code>null</code>, data will be read from the current file position.</p>

<p>The callback is given the two arguments, <code>(err, bytesRead)</code>.</p>

<h3>fs.read(fd, length, position, encoding, [callback])</h3>

<p>Read data from the file specified by <code>fd</code>.</p>

<p><code>length</code> is an integer specifying the number of bytes to read.</p>

<p><code>position</code> is an integer specifying where to begin reading from in the file.
If <code>position</code> is <code>null</code>, data will be read from the current file position.</p>

<p><code>encoding</code> is the desired encoding of the string of data read in from <code>fd</code>.</p>

<p>The callback is given the three arguments, <code>(err, str, bytesRead)</code>.</p>

<h3>fs.readSync(fd, buffer, offset, length, position)</h3>

<p>Synchronous version of buffer-based <code>fs.read</code>. Returns the number of <code>bytesRead</code>.</p>

<h3>fs.readSync(fd, length, position, encoding)</h3>

<p>Synchronous version of string-based <code>fs.read</code>. Returns the number of <code>bytesRead</code>.</p>

<h3>fs.readFile(filename, [encoding], [callback])</h3>

<p>Asynchronously reads the entire contents of a file. Example:</p>

<pre><code>fs.readFile('/etc/passwd', function (err, data) {
  if (err) throw err;
  console.log(data);
});
</code></pre>

<p>The callback is passed two arguments <code>(err, data)</code>, where <code>data</code> is the
contents of the file.</p>

<p>If no encoding is specified, then the raw buffer is returned.</p>

<h3>fs.readFileSync(filename, [encoding])</h3>

<p>Synchronous version of <code>fs.readFile</code>. Returns the contents of the <code>filename</code>.</p>

<p>If <code>encoding</code> is specified then this function returns a string. Otherwise it
returns a buffer.</p>

<h3>fs.writeFile(filename, data, encoding='utf8', [callback])</h3>

<p>Asynchronously writes data to a file. <code>data</code> can be a string or a buffer.</p>

<p>Example:</p>

<pre><code>fs.writeFile('message.txt', 'Hello Node', function (err) {
  if (err) throw err;
  console.log('It\'s saved!');
});
</code></pre>

<h3>fs.writeFileSync(filename, data, encoding='utf8')</h3>

<p>The synchronous version of <code>fs.writeFile</code>.</p>

<h3>fs.watchFile(filename, [options], listener)</h3>

<p>Watch for changes on <code>filename</code>. The callback <code>listener</code> will be called each
time the file changes.</p>

<p>The second argument is optional. The <code>options</code> if provided should be an object
containing two members a boolean, <code>persistent</code>, and <code>interval</code>, a polling
value in milliseconds. The default is <code>{persistent: true, interval: 0}</code>.</p>

<p>The <code>listener</code> gets two arguments the current stat object and the previous
stat object:</p>

<pre><code>fs.watchFile(f, function (curr, prev) {
  console.log('the current mtime is: ' + curr.mtime);
  console.log('the previous mtime was: ' + prev.mtime);
});
</code></pre>

<p>These stat objects are instances of <code>fs.Stat</code>. </p>

<h3>fs.unwatchFile(filename)</h3>

<p>Stop watching for changes on <code>filename</code>.</p>

<h2>fs.Stats</h2>

<p>Objects returned from <code>fs.stat()</code> and <code>fs.lstat()</code> are of this type.</p>

<ul>
<li><code>stats.isFile()</code></li>
<li><code>stats.isDirectory()</code></li>
<li><code>stats.isBlockDevice()</code></li>
<li><code>stats.isCharacterDevice()</code></li>
<li><code>stats.isSymbolicLink()</code> (only valid with  <code>fs.lstat()</code>)</li>
<li><code>stats.isFIFO()</code></li>
<li><code>stats.isSocket()</code></li>
</ul>

<h2>fs.ReadStream</h2>

<p><code>ReadStream</code> is a <code>Readable Stream</code>.</p>

<h3>fs.createReadStream(path, [options])</h3>

<p>Returns a new ReadStream object (See <code>Readable Stream</code>).</p>

<p><code>options</code> is an object with the following defaults:</p>

<pre><code>{ 'flags': 'r'
, 'encoding': null
, 'mode': 0666
, 'bufferSize': 4 * 1024
}
</code></pre>

<p><code>options</code> can include <code>start</code> and <code>end</code> values to read a range of bytes from
the file instead of the entire file.  Both <code>start</code> and <code>end</code> are inclusive and
start at 0.  When used, both the limits must be specified always.</p>

<p>An example to read the last 10 bytes of a file which is 100 bytes long:</p>

<pre><code>fs.createReadStream('sample.txt', {start: 90, end: 99});
</code></pre>

<h2>fs.WriteStream</h2>

<p><code>WriteStream</code> is a <code>Writable Stream</code>.</p>

<h3>Event: 'open'</h3>

<p><code>function (fd) { }</code></p>

<p><code>fd</code> is the file descriptor used by the WriteStream.</p>

<h3>fs.createWriteStream(path, [options])</h3>

<p>Returns a new WriteStream object (See <code>Writable Stream</code>).</p>

<p><code>options</code> is an object with the following defaults:</p>

<pre><code>{ 'flags': 'w'
, 'encoding': null
, 'mode': 0666
}
</code></pre>

<h2>HTTP</h2>

<p>To use the HTTP server and client one must <code>require('http')</code>.</p>

<p>The HTTP interfaces in Node are designed to support many features
of the protocol which have been traditionally difficult to use.
In particular, large, possibly chunk-encoded, messages. The interface is
careful to never buffer entire requests or responses--the
user is able to stream data.</p>

<p>HTTP message headers are represented by an object like this:</p>

<pre><code>{ 'content-length': '123'
, 'content-type': 'text/plain'
, 'stream': 'keep-alive'
, 'accept': '*/*'
}
</code></pre>

<p>Keys are lowercased. Values are not modified.</p>

<p>In order to support the full spectrum of possible HTTP applications, Node's
HTTP API is very low-level. It deals with stream handling and message
parsing only. It parses a message into headers and body but it does not
parse the actual headers or the body.</p>

<p>HTTPS is supported if OpenSSL is available on the underlying platform.</p>

<h2>http.Server</h2>

<p>This is an <code>EventEmitter</code> with the following events:</p>

<h3>Event: 'request'</h3>

<p><code>function (request, response) { }</code></p>

<p><code>request</code> is an instance of <code>http.ServerRequest</code> and <code>response</code> is
 an instance of <code>http.ServerResponse</code></p>

<h3>Event: 'connection'</h3>

<p><code>function (stream) { }</code></p>

<p>When a new TCP stream is established. <code>stream</code> is an object of type
 <code>net.Stream</code>. Usually users will not want to access this event. The
 <code>stream</code> can also be accessed at <code>request.connection</code>.</p>

<h3>Event: 'close'</h3>

<p><code>function (errno) { }</code></p>

<p>Emitted when the server closes. </p>

<h3>Event: 'request'</h3>

<p><code>function (request, response) {}</code></p>

<p>Emitted each time there is request. Note that there may be multiple requests
per connection (in the case of keep-alive connections).</p>

<h3>Event: 'upgrade'</h3>

<p><code>function (request, socket, head)</code></p>

<p>Emitted each time a client requests a http upgrade. If this event isn't
listened for, then clients requesting an upgrade will have their connections
closed.</p>

<ul>
<li><code>request</code> is the arguments for the http request, as it is in the request event.</li>
<li><code>socket</code> is the network socket between the server and client.</li>
<li><code>head</code> is an instance of Buffer, the first packet of the upgraded stream, this may be empty.</li>
</ul>

<p>After this event is emitted, the request's socket will not have a <code>data</code>
event listener, meaning you will need to bind to it in order to handle data
sent to the server on that socket.</p>

<h3>Event: 'clientError'</h3>

<p><code>function (exception) {}</code></p>

<p>If a client connection emits an 'error' event - it will forwarded here.</p>

<h3>http.createServer(requestListener)</h3>

<p>Returns a new web server object.</p>

<p>The <code>requestListener</code> is a function which is automatically
added to the <code>'request'</code> event.</p>

<h3>server.listen(port, [hostname], [callback])</h3>

<p>Begin accepting connections on the specified port and hostname.  If the
hostname is omitted, the server will accept connections directed to any
IPv4 address (<code>INADDR_ANY</code>).</p>

<p>To listen to a unix socket, supply a filename instead of port and hostname.</p>

<p>This function is asynchronous. The last parameter <code>callback</code> will be called
when the server has been bound to the port.</p>

<h3>server.listen(path, [callback])</h3>

<p>Start a UNIX socket server listening for connections on the given <code>path</code>.</p>

<p>This function is asynchronous. The last parameter <code>callback</code> will be called
when the server has been bound.</p>

<h3>server.setSecure(credentials)</h3>

<p>Enables HTTPS support for the server, with the crypto module credentials specifying the private key and certificate of the server, and optionally the CA certificates for use in client authentication.</p>

<p>If the credentials hold one or more CA certificates, then the server will request for the client to submit a client certificate as part of the HTTPS connection handshake. The validity and content of this can be accessed via verifyPeer() and getPeerCertificate() from the server's request.connection.</p>

<h3>server.close()</h3>

<p>Stops the server from accepting new connections.</p>

<h2>http.ServerRequest</h2>

<p>This object is created internally by a HTTP server--not by
the user--and passed as the first argument to a <code>'request'</code> listener.</p>

<p>This is an <code>EventEmitter</code> with the following events:</p>

<h3>Event: 'data'</h3>

<p><code>function (chunk) { }</code></p>

<p>Emitted when a piece of the message body is received.</p>

<p>Example: A chunk of the body is given as the single
argument. The transfer-encoding has been decoded.  The
body chunk is a string.  The body encoding is set with
<code>request.setBodyEncoding()</code>.</p>

<h3>Event: 'end'</h3>

<p><code>function () { }</code></p>

<p>Emitted exactly once for each message. No arguments.  After
emitted no other events will be emitted on the request.</p>

<h3>request.method</h3>

<p>The request method as a string. Read only. Example:
<code>'GET'</code>, <code>'DELETE'</code>.</p>

<h3>request.url</h3>

<p>Request URL string. This contains only the URL that is
present in the actual HTTP request. If the request is:</p>

<pre><code>GET /status?name=ryan HTTP/1.1\r\n
Accept: text/plain\r\n
\r\n
</code></pre>

<p>Then <code>request.url</code> will be:</p>

<pre><code>'/status?name=ryan'
</code></pre>

<p>If you would like to parse the URL into its parts, you can use
<code>require('url').parse(request.url)</code>.  Example:</p>

<pre><code>node&gt; require('url').parse('/status?name=ryan')
{ href: '/status?name=ryan'
, search: '?name=ryan'
, query: 'name=ryan'
, pathname: '/status'
}
</code></pre>

<p>If you would like to extract the params from the query string,
you can use the <code>require('querystring').parse</code> function, or pass
<code>true</code> as the second argument to <code>require('url').parse</code>.  Example:</p>

<pre><code>node&gt; require('url').parse('/status?name=ryan', true)
{ href: '/status?name=ryan'
, search: '?name=ryan'
, query: { name: 'ryan' }
, pathname: '/status'
}
</code></pre>

<h3>request.headers</h3>

<p>Read only.</p>

<h3>request.httpVersion</h3>

<p>The HTTP protocol version as a string. Read only. Examples:
<code>'1.1'</code>, <code>'1.0'</code>.
Also <code>request.httpVersionMajor</code> is the first integer and
<code>request.httpVersionMinor</code> is the second.</p>

<h3>request.setEncoding(encoding=null)</h3>

<p>Set the encoding for the request body. Either <code>'utf8'</code> or <code>'binary'</code>. Defaults
to <code>null</code>, which means that the <code>'data'</code> event will emit a <code>Buffer</code> object..</p>

<h3>request.pause()</h3>

<p>Pauses request from emitting events.  Useful to throttle back an upload.</p>

<h3>request.resume()</h3>

<p>Resumes a paused request.</p>

<h3>request.connection</h3>

<p>The <code>net.Stream</code> object associated with the connection.</p>

<p>With HTTPS support, use request.connection.verifyPeer() and
request.connection.getPeerCertificate() to obtain the client's
authentication details.</p>

<h2>http.ServerResponse</h2>

<p>This object is created internally by a HTTP server--not by the user. It is
passed as the second parameter to the <code>'request'</code> event. It is a <code>Writable Stream</code>.</p>

<h3>response.writeHead(statusCode, [reasonPhrase], [headers])</h3>

<p>Sends a response header to the request. The status code is a 3-digit HTTP
status code, like <code>404</code>. The last argument, <code>headers</code>, are the response headers.
Optionally one can give a human-readable <code>reasonPhrase</code> as the second
argument.</p>

<p>Example:</p>

<pre><code>var body = 'hello world';
response.writeHead(200, {
  'Content-Length': body.length,
  'Content-Type': 'text/plain'
});
</code></pre>

<p>This method must only be called once on a message and it must
be called before <code>response.end()</code> is called.</p>

<h3>response.write(chunk, encoding='utf8')</h3>

<p>This method must be called after <code>writeHead</code> was
called. It sends a chunk of the response body. This method may
be called multiple times to provide successive parts of the body.</p>

<p><code>chunk</code> can be a string or a buffer. If <code>chunk</code> is a string,
the second parameter specifies how to encode it into a byte stream.
By default the <code>encoding</code> is <code>'utf8'</code>.</p>

<p><strong>Note</strong>: This is the raw HTTP body and has nothing to do with
higher-level multi-part body encodings that may be used.</p>

<p>The first time <code>response.write()</code> is called, it will send the buffered
header information and the first body to the client. The second time
<code>response.write()</code> is called, Node assumes you're going to be streaming
data, and sends that separately. That is, the response is buffered up to the
first chunk of body.</p>

<h3>response.end([data], [encoding])</h3>

<p>This method signals to the server that all of the response headers and body
has been sent; that server should consider this message complete.
The method, <code>response.end()</code>, MUST be called on each
response.</p>

<p>If <code>data</code> is specified, it is equivalent to calling <code>response.write(data, encoding)</code>
followed by <code>response.end()</code>.</p>

<h2>http.Client</h2>

<p>An HTTP client is constructed with a server address as its
argument, the returned handle is then used to issue one or more
requests.  Depending on the server connected to, the client might
pipeline the requests or reestablish the stream after each
stream. <em>Currently the implementation does not pipeline requests.</em></p>

<p>Example of connecting to <code>google.com</code>:</p>

<pre><code>var http = require('http');
var google = http.createClient(80, 'www.google.com');
var request = google.request('GET', '/',
  {'host': 'www.google.com'});
request.end();
request.on('response', function (response) {
  console.log('STATUS: ' + response.statusCode);
  console.log('HEADERS: ' + JSON.stringify(response.headers));
  response.setEncoding('utf8');
  response.on('data', function (chunk) {
    console.log('BODY: ' + chunk);
  });
});
</code></pre>

<p>There are a few special headers that should be noted.</p>

<ul>
<li><p>The 'Host' header is not added by Node, and is usually required by
website.</p></li>
<li><p>Sending a 'Connection: keep-alive' will notify Node that the connection to
the server should be persisted until the next request.</p></li>
<li><p>Sending a 'Content-length' header will disable the default chunked encoding.</p></li>
</ul>

<h3>Event: 'upgrade'</h3>

<p><code>function (request, socket, head)</code></p>

<p>Emitted each time a server responds to a request with an upgrade. If this event
isn't being listened for, clients receiving an upgrade header will have their
connections closed.</p>

<p>See the description of the <code>upgrade</code> event for <code>http.Server</code> for further details.</p>

<h3>http.createClient(port, host='localhost', secure=false, [credentials])</h3>

<p>Constructs a new HTTP client. <code>port</code> and
<code>host</code> refer to the server to be connected to. A
stream is not established until a request is issued.</p>

<p><code>secure</code> is an optional boolean flag to enable https support and <code>credentials</code> is an optional credentials object from the crypto module, which may hold the client's private key, certificate, and a list of trusted CA certificates.</p>

<p>If the connection is secure, but no explicit CA certificates are passed in the credentials, then node.js will default to the publicly trusted list of CA certificates, as given in http://mxr.mozilla.org/mozilla/source/security/nss/lib/ckfw/builtins/certdata.txt</p>

<h3>client.request(method='GET', path, [request_headers])</h3>

<p>Issues a request; if necessary establishes stream. Returns a <code>http.ClientRequest</code> instance.</p>

<p><code>method</code> is optional and defaults to 'GET' if omitted.</p>

<p><code>request_headers</code> is optional.
Additional request headers might be added internally
by Node. Returns a <code>ClientRequest</code> object.</p>

<p>Do remember to include the <code>Content-Length</code> header if you
plan on sending a body. If you plan on streaming the body, perhaps
set <code>Transfer-Encoding: chunked</code>.</p>

<p><em>NOTE</em>: the request is not complete. This method only sends the header of
the request. One needs to call <code>request.end()</code> to finalize the request and
retrieve the response.  (This sounds convoluted but it provides a chance for
the user to stream a body to the server with <code>request.write()</code>.)</p>

<h3>client.verifyPeer()</h3>

<p>Returns true or false depending on the validity of the server's certificate in the context of the defined or default list of trusted CA certificates.</p>

<h3>client.getPeerCertificate()</h3>

<p>Returns a JSON structure detailing the server's certificate, containing a dictionary with keys for the certificate 'subject', 'issuer', 'valid_from' and 'valid_to'</p>

<h2>http.ClientRequest</h2>

<p>This object is created internally and returned from the <code>request()</code> method
of a <code>http.Client</code>. It represents an <em>in-progress</em> request whose header has
already been sent.</p>

<p>To get the response, add a listener for <code>'response'</code> to the request object.
<code>'response'</code> will be emitted from the request object when the response
headers have been received.  The <code>'response'</code> event is executed with one
argument which is an instance of <code>http.ClientResponse</code>.</p>

<p>During the <code>'response'</code> event, one can add listeners to the
response object; particularly to listen for the <code>'data'</code> event. Note that
the <code>'response'</code> event is called before any part of the response body is received,
so there is no need to worry about racing to catch the first part of the
body. As long as a listener for <code>'data'</code> is added during the <code>'response'</code>
event, the entire body will be caught.</p>

<pre><code>// Good
request.on('response', function (response) {
  response.on('data', function (chunk) {
    console.log('BODY: ' + chunk);
  });
});

// Bad - misses all or part of the body
request.on('response', function (response) {
  setTimeout(function () {
    response.on('data', function (chunk) {
      console.log('BODY: ' + chunk);
    });
  }, 10);
});
</code></pre>

<p>This is a <code>Writable Stream</code>.</p>

<p>This is an <code>EventEmitter</code> with the following events:</p>

<h3>Event 'response'</h3>

<p><code>function (response) { }</code></p>

<p>Emitted when a response is received to this request. This event is emitted only once. The
<code>response</code> argument will be an instance of <code>http.ClientResponse</code>.</p>

<h3>request.write(chunk, encoding='utf8')</h3>

<p>Sends a chunk of the body.  By calling this method
many times, the user can stream a request body to a
server--in that case it is suggested to use the
<code>['Transfer-Encoding', 'chunked']</code> header line when
creating the request.</p>

<p>The <code>chunk</code> argument should be an array of integers
or a string.</p>

<p>The <code>encoding</code> argument is optional and only
applies when <code>chunk</code> is a string.</p>

<h3>request.end([data], [encoding])</h3>

<p>Finishes sending the request. If any parts of the body are
unsent, it will flush them to the stream. If the request is
chunked, this will send the terminating <code>'0\r\n\r\n'</code>.</p>

<p>If <code>data</code> is specified, it is equivalent to calling <code>request.write(data, encoding)</code>
followed by <code>request.end()</code>.</p>

<h2>http.ClientResponse</h2>

<p>This object is created when making a request with <code>http.Client</code>. It is
passed to the <code>'response'</code> event of the request object.</p>

<p>The response implements the <code>Readable Stream</code> interface.</p>

<h3>Event: 'data'</h3>

<p><code>function (chunk) {}</code></p>

<p>Emitted when a piece of the message body is received.</p>

<pre><code>Example: A chunk of the body is given as the single
argument. The transfer-encoding has been decoded.  The
body chunk a String.  The body encoding is set with
`response.setBodyEncoding()`.
</code></pre>

<h3>Event: 'end'</h3>

<p><code>function () {}</code></p>

<p>Emitted exactly once for each message. No arguments. After
emitted no other events will be emitted on the response.</p>

<h3>response.statusCode</h3>

<p>The 3-digit HTTP response status code. E.G. <code>404</code>.</p>

<h3>response.httpVersion</h3>

<p>The HTTP version of the connected-to server. Probably either
<code>'1.1'</code> or <code>'1.0'</code>.
Also <code>response.httpVersionMajor</code> is the first integer and
<code>response.httpVersionMinor</code> is the second.</p>

<h3>response.headers</h3>

<p>The response headers object.</p>

<h3>response.setEncoding(encoding=null)</h3>

<p>Set the encoding for the response body. Either <code>'utf8'</code>, <code>'ascii'</code>, or <code>'base64'</code>.
Defaults to <code>null</code>, which means that the <code>'data'</code> event will emit a <code>Buffer</code> object..</p>

<h3>response.pause()</h3>

<p>Pauses response from emitting events.  Useful to throttle back a download.</p>

<h3>response.resume()</h3>

<p>Resumes a paused response.</p>

<h3>response.client</h3>

<p>A reference to the <code>http.Client</code> that this response belongs to.</p>

<h2>net.Server</h2>

<p>This class is used to create a TCP or UNIX server.</p>

<p>Here is an example of a echo server which listens for connections
on port 8124:</p>

<pre><code>var net = require('net');
var server = net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.on('connect', function () {
    stream.write('hello\r\n');
  });
  stream.on('data', function (data) {
    stream.write(data);
  });
  stream.on('end', function () {
    stream.write('goodbye\r\n');
    stream.end();
  });
});
server.listen(8124, 'localhost');
</code></pre>

<p>To listen on the socket <code>'/tmp/echo.sock'</code>, the last line would just be
changed to</p>

<pre><code>server.listen('/tmp/echo.sock');
</code></pre>

<p>This is an <code>EventEmitter</code> with the following events:</p>

<h3>Event: 'connection'</h3>

<p><code>function (stream) {}</code></p>

<p>Emitted when a new connection is made. <code>stream</code> is an instance of
<code>net.Stream</code>.</p>

<h3>Event: 'close'</h3>

<p><code>function () {}</code></p>

<p>Emitted when the server closes.</p>

<h3>net.createServer(connectionListener)</h3>

<p>Creates a new TCP server. The <code>connectionListener</code> argument is
automatically set as a listener for the <code>'connection'</code> event.</p>

<h3>server.listen(port, [host], [callback])</h3>

<p>Begin accepting connections on the specified <code>port</code> and <code>host</code>.  If the
<code>host</code> is omitted, the server will accept connections directed to any
IPv4 address (<code>INADDR_ANY</code>).</p>

<p>This function is asynchronous. The last parameter <code>callback</code> will be called
when the server has been bound.</p>

<h3>server.listen(path, [callback])</h3>

<p>Start a UNIX socket server listening for connections on the given <code>path</code>.</p>

<p>This function is asynchronous. The last parameter <code>callback</code> will be called
when the server has been bound.</p>

<h3>server.listenFD(fd)</h3>

<p>Start a server listening for connections on the given file descriptor.</p>

<p>This file descriptor must have already had the <code>bind(2)</code> and <code>listen(2)</code> system
calls invoked on it.</p>

<h3>server.close()</h3>

<p>Stops the server from accepting new connections. This function is
asynchronous, the server is finally closed when the server emits a <code>'close'</code>
event.</p>

<h3>server.maxConnections</h3>

<p>Set this property to reject connections when the server's connection count gets high.</p>

<h3>server.connections</h3>

<p>The number of concurrent connections on the server.</p>

<h2>net.Stream</h2>

<p>This object is an abstraction of of a TCP or UNIX socket.  <code>net.Stream</code>
instance implement a duplex stream interface.  They can be created by the
user and used as a client (with <code>connect()</code>) or they can be created by Node
and passed to the user through the <code>'connection'</code> event of a server.</p>

<p><code>net.Stream</code> instances are EventEmitters with the following events:</p>

<h3>Event: 'connect'</h3>

<p><code>function () { }</code></p>

<p>Emitted when a stream connection successfully is established.
See <code>connect()</code>.</p>

<h3>Event: 'secure'</h3>

<p><code>function () { }</code></p>

<p>Emitted when a stream connection successfully establishes an SSL handshake with its peer.</p>

<h3>Event: 'data'</h3>

<p><code>function (data) { }</code></p>

<p>Emitted when data is received.  The argument <code>data</code> will be a <code>Buffer</code> or
<code>String</code>.  Encoding of data is set by <code>stream.setEncoding()</code>.
(See the section on <code>Readable Stream</code> for more information.)</p>

<h3>Event: 'end'</h3>

<p><code>function () { }</code></p>

<p>Emitted when the other end of the stream sends a FIN packet. After this is
emitted the <code>readyState</code> will be <code>'writeOnly'</code>. One should probably just
call <code>stream.end()</code> when this event is emitted.</p>

<h3>Event: 'timeout'</h3>

<p><code>function () { }</code></p>

<p>Emitted if the stream times out from inactivity. This is only to notify that
the stream has been idle. The user must manually close the connection.</p>

<p>See also: <code>stream.setTimeout()</code></p>

<h3>Event: 'drain'</h3>

<p><code>function () { }</code></p>

<p>Emitted when the write buffer becomes empty. Can be used to throttle uploads.</p>

<h3>Event: 'error'</h3>

<p><code>function (exception) { }</code></p>

<p>Emitted when an error occurs.  The <code>'close'</code> event will be called directly
following this event.</p>

<h3>Event: 'close'</h3>

<p><code>function (had_error) { }</code></p>

<p>Emitted once the stream is fully closed. The argument <code>had_error</code> is a boolean which says if
the stream was closed due to a transmission
error.</p>

<h3>net.createConnection(port, host='127.0.0.1')</h3>

<p>Construct a new stream object and opens a stream to the specified <code>port</code>
and <code>host</code>. If the second parameter is omitted, localhost is assumed.</p>

<p>When the stream is established the <code>'connect'</code> event will be emitted.</p>

<h3>stream.connect(port, host='127.0.0.1')</h3>

<p>Opens a stream to the specified <code>port</code> and <code>host</code>. <code>createConnection()</code>
also opens a stream; normally this method is not needed. Use this only if
a stream is closed and you want to reuse the object to connect to another
server.</p>

<p>This function is asynchronous. When the <code>'connect'</code> event is emitted the
stream is established. If there is a problem connecting, the <code>'connect'</code>
event will not be emitted, the <code>'error'</code> event will be emitted with 
the exception.</p>

<h3>stream.remoteAddress</h3>

<p>The string representation of the remote IP address. For example,
<code>'74.125.127.100'</code> or <code>'2001:4860:a005::68'</code>.</p>

<p>This member is only present in server-side connections.</p>

<h3>stream.readyState</h3>

<p>Either <code>'closed'</code>, <code>'open'</code>, <code>'opening'</code>, <code>'readOnly'</code>, or <code>'writeOnly'</code>.</p>

<h3>stream.setEncoding(encoding=null)</h3>

<p>Sets the encoding (either <code>'ascii'</code>, <code>'utf8'</code>, or <code>'base64'</code>) for data that is
received.</p>

<h3>stream.setSecure([credentials])</h3>

<p>Enables SSL support for the stream, with the crypto module credentials specifying the private key and certificate of the stream, and optionally the CA certificates for use in peer authentication.</p>

<p>If the credentials hold one ore more CA certificates, then the stream will request for the peer to submit a client certificate as part of the SSL connection handshake. The validity and content of this can be accessed via verifyPeer() and getPeerCertificate().</p>

<h3>stream.verifyPeer()</h3>

<p>Returns true or false depending on the validity of the peers's certificate in the context of the defined or default list of trusted CA certificates.</p>

<h3>stream.getPeerCertificate()</h3>

<p>Returns a JSON structure detailing the peer's certificate, containing a dictionary with keys for the certificate 'subject', 'issuer', 'valid_from' and 'valid_to'</p>

<h3>stream.write(data, encoding='ascii')</h3>

<p>Sends data on the stream. The second parameter specifies the encoding in
the case of a string--it defaults to ASCII because encoding to UTF8 is rather
slow.</p>

<p>Returns <code>true</code> if the entire data was flushed successfully to the kernel
buffer. Returns <code>false</code> if all or part of the data was queued in user memory.
<code>'drain'</code> will be emitted when the buffer is again free.</p>

<h3>stream.end([data], [encoding])</h3>

<p>Half-closes the stream. I.E., it sends a FIN packet. It is possible the
server will still send some data. After calling this <code>readyState</code> will be
<code>'readOnly'</code>.</p>

<p>If <code>data</code> is specified, it is equivalent to calling <code>stream.write(data, encoding)</code>
followed by <code>stream.end()</code>.</p>

<h3>stream.destroy()</h3>

<p>Ensures that no more I/O activity happens on this stream. Only necessary in
case of errors (parse error or so).</p>

<h3>stream.pause()</h3>

<p>Pauses the reading of data. That is, <code>'data'</code> events will not be emitted.
Useful to throttle back an upload.</p>

<h3>stream.resume()</h3>

<p>Resumes reading after a call to <code>pause()</code>.</p>

<h3>stream.setTimeout(timeout)</h3>

<p>Sets the stream to timeout after <code>timeout</code> milliseconds of inactivity on
the stream. By default <code>net.Stream</code> do not have a timeout.</p>

<p>When an idle timeout is triggered the stream will receive a <code>'timeout'</code>
event but the connection will not be severed. The user must manually <code>end()</code>
or <code>destroy()</code> the stream.</p>

<p>If <code>timeout</code> is 0, then the existing idle timeout is disabled.</p>

<h3>stream.setNoDelay(noDelay=true)</h3>

<p>Disables the Nagle algorithm. By default TCP connections use the Nagle
algorithm, they buffer data before sending it off. Setting <code>noDelay</code> will
immediately fire off data each time <code>stream.write()</code> is called.</p>

<h3>stream.setKeepAlive(enable=false, [initialDelay])</h3>

<p>Enable/disable keep-alive functionality, and optionally set the initial
delay before the first keepalive probe is sent on an idle stream.
Set <code>initialDelay</code> (in milliseconds) to set the delay between the last
data packet received and the first keepalive probe. Setting 0 for
initialDelay will leave the value unchanged from the default
(or previous) setting.</p>

<h2>Crypto</h2>

<p>Use <code>require('crypto')</code> to access this module.</p>

<p>The crypto module requires OpenSSL to be available on the underlying platform. It offers a way of encapsulating secure credentials to be used as part of a secure HTTPS net or http connection.</p>

<p>It also offers a set of wrappers for OpenSSL's hash, hmac, cipher, decipher, sign and verify methods.</p>

<h3>crypto.createCredentials(details)</h3>

<p>Creates a credentials object, with the optional details being a dictionary with keys:</p>

<p><code>key</code> : a string holding the PEM encoded private key</p>

<p><code>cert</code> : a string holding the PEM encoded certificate</p>

<p><code>ca</code> : either a string or list of strings of PEM encoded CA certificates to trust.</p>

<p>If no 'ca' details are given, then node.js will use the default publicly trusted list of CAs as given in 
http://mxr.mozilla.org/mozilla/source/security/nss/lib/ckfw/builtins/certdata.txt</p>

<h3>crypto.createHash(algorithm)</h3>

<p>Creates and returns a hash object, a cryptographic hash with the given algorithm which can be used to generate hash digests.</p>

<p><code>algorithm</code> is dependent on the available algorithms supported by the version of OpenSSL on the platform. Examples are sha1, md5, sha256, sha512, etc. On recent releases, <code>openssl list-message-digest-algorithms</code> will display the available digest algorithms.</p>

<h3>hash.update(data)</h3>

<p>Updates the hash content with the given <code>data</code>. This can be called many times with new data as it is streamed.</p>

<h3>hash.digest(encoding='binary')</h3>

<p>Calculates the digest of all of the passed data to be hashed. The <code>encoding</code> can be 'hex', 'binary' or 'base64'.</p>

<h3>crypto.createHmac(algorithm, key)</h3>

<p>Creates and returns a hmac object, a cryptographic hmac with the given algorithm and key.</p>

<p><code>algorithm</code> is dependent on the available algorithms supported by OpenSSL - see createHash above.
<code>key</code> is the hmac key to be used.</p>

<h3>hmac.update(data)</h3>

<p>Update the hmac content with the given <code>data</code>. This can be called many times with new data as it is streamed.</p>

<h3>hmac.digest(encoding='binary')</h3>

<p>Calculates the digest of all of the passed data to the hmac. The <code>encoding</code> can be 'hex', 'binary' or 'base64'.</p>

<h3>crypto.createCipher(algorithm, key)</h3>

<p>Creates and returns a cipher object, with the given algorithm and key.</p>

<p><code>algorithm</code> is dependent on OpenSSL, examples are aes192, etc. On recent releases, <code>openssl list-cipher-algorithms</code> will display the available cipher algorithms.</p>

<h3>cipher.update(data, input<em>encoding='binary', output</em>encoding='binary')</h3>

<p>Updates the cipher with <code>data</code>, the encoding of which is given in <code>input_encoding</code> and can be 'utf8', 'ascii' or 'binary'. The <code>output_encoding</code> specifies the output format of the enciphered data, and can be 'binary', 'base64'  or 'hex'.</p>

<p>Returns the enciphered contents, and can be called many times with new data as it is streamed.</p>

<h3>cipher.final(output_encoding='binary')</h3>

<p>Returns any remaining enciphered contents, with <code>output_encoding</code> being one of: 'binary', 'ascii' or 'utf8'.</p>

<h3>crypto.createDecipher(algorithm, key)</h3>

<p>Creates and returns a decipher object, with the given algorithm and key. This is the mirror of the cipher object above.</p>

<h3>decipher.update(data, input<em>encoding='binary', output</em>encoding='binary')</h3>

<p>Updates the decipher with <code>data</code>, which is encoded in 'binary', 'base64' or 'hex'. The <code>output_decoding</code> specifies in what format to return the deciphered plaintext - either 'binary', 'ascii' or 'utf8'.</p>

<h3>decipher.final(output_encoding='binary')</h3>

<p>Returns any remaining plaintext which is deciphered, with `output_encoding' being one of: 'binary', 'ascii' or 'utf8'.</p>

<h3>crypto.createSign(algorithm)</h3>

<p>Creates and returns a signing object, with the given algorithm. On recent OpenSSL releases, <code>openssl list-public-key-algorithms</code> will display the available signing algorithms. Examples are 'RSA-SHA256'.</p>

<h3>signer.update(data)</h3>

<p>Updates the signer object with data. This can be called many times with new data as it is streamed.</p>

<h3>signer.sign(private<em>key, output</em>format='binary')</h3>

<p>Calculates the signature on all the updated data passed through the signer. <code>private_key</code> is a string containing the PEM encoded private key for signing.</p>

<p>Returns the signature in <code>output_format</code> which can be 'binary', 'hex' or 'base64'</p>

<h3>crypto.createVerify(algorithm)</h3>

<p>Creates and returns a verification object, with the given algorithm. This is the mirror of the signing object above.</p>

<h3>verifier.update(data)</h3>

<p>Updates the verifyer object with data. This can be called many times with new data as it is streamed.</p>

<h3>verifier.verify(public<em>key, signature, signature</em>format='binary')</h3>

<p>Verifies the signed data by using the <code>public_key</code> which is a string containing the PEM encoded public key, and <code>signature</code>, which is the previously calculates signature for the data, in the <code>signature_format</code> which can be 'binary', 'hex' or 'base64'.</p>

<p>Returns true or false depending on the validity of the signature for the data and public key.</p>

<h2>DNS</h2>

<p>Use <code>require('dns')</code> to access this module.</p>

<p>Here is an example which resolves <code>'www.google.com'</code> then reverse
resolves the IP addresses which are returned.</p>

<pre><code>var dns = require('dns');

dns.resolve4('www.google.com', function (err, addresses) {
  if (err) throw err;

  console.log('addresses: ' + JSON.stringify(addresses));

  addresses.forEach(function (a) {
    dns.reverse(a, function (err, domains) {
      if (err) {
        console.log('reverse for ' + a + ' failed: ' +
          err.message);
      } else {
        console.log('reverse for ' + a + ': ' +
          JSON.stringify(domains));
      }
    });
  });
});
</code></pre>

<h3>dns.lookup(domain, family=null, callback)</h3>

<p>Resolves a domain (e.g. <code>'google.com'</code>) into the first found A (IPv4) or
AAAA (IPv6) record.</p>

<p>The callback has arguments <code>(err, address, family)</code>.  The <code>address</code> argument
is a string representation of a IP v4 or v6 address. The <code>family</code> argument
is either the integer 4 or 6 and denotes the family of <code>address</code> (not
neccessarily the value initially passed to <code>lookup</code>).</p>

<h3>dns.resolve(domain, rrtype='A', callback)</h3>

<p>Resolves a domain (e.g. <code>'google.com'</code>) into an array of the record types
specified by rrtype. Valid rrtypes are <code>A</code> (IPV4 addresses), <code>AAAA</code> (IPV6
addresses), <code>MX</code> (mail exchange records), <code>TXT</code> (text records), <code>SRV</code> (SRV
records), and <code>PTR</code> (used for reverse IP lookups).</p>

<p>The callback has arguments <code>(err, addresses)</code>.  The type of each item
in <code>addresses</code> is determined by the record type, and described in the
documentation for the corresponding lookup methods below.</p>

<p>On error, <code>err</code> would be an instanceof <code>Error</code> object, where <code>err.errno</code> is
one of the error codes listed below and <code>err.message</code> is a string describing
the error in English.</p>

<h3>dns.resolve4(domain, callback)</h3>

<p>The same as <code>dns.resolve()</code>, but only for IPv4 queries (<code>A</code> records). 
<code>addresses</code> is an array of IPv4 addresses (e.g. <br />
<code>['74.125.79.104', '74.125.79.105', '74.125.79.106']</code>).</p>

<h3>dns.resolve6(domain, callback)</h3>

<p>The same as <code>dns.resolve4()</code> except for IPv6 queries (an <code>AAAA</code> query).</p>

<h3>dns.resolveMx(domain, callback)</h3>

<p>The same as <code>dns.resolve()</code>, but only for mail exchange queries (<code>MX</code> records).</p>

<p><code>addresses</code> is an array of MX records, each with a priority and an exchange
attribute (e.g. <code>[{'priority': 10, 'exchange': 'mx.example.com'},...]</code>).</p>

<h3>dns.resolveTxt(domain, callback)</h3>

<p>The same as <code>dns.resolve()</code>, but only for text queries (<code>TXT</code> records).
<code>addresses</code> is an array of the text records available for <code>domain</code> (e.g.,
<code>['v=spf1 ip4:0.0.0.0 ~all']</code>).</p>

<h3>dns.resolveSrv(domain, callback)</h3>

<p>The same as <code>dns.resolve()</code>, but only for service records (<code>SRV</code> records).
<code>addresses</code> is an array of the SRV records available for <code>domain</code>. Properties
of SRV records are priority, weight, port, and name (e.g., 
<code>[{'priority': 10, {'weight': 5, 'port': 21223, 'name': 'service.example.com'}, ...]</code>).</p>

<h3>dns.reverse(ip, callback)</h3>

<p>Reverse resolves an ip address to an array of domain names.</p>

<p>The callback has arguments <code>(err, domains)</code>. </p>

<p>If there an an error, <code>err</code> will be non-null and an instanceof the Error
object.</p>

<p>Each DNS query can return an error code.</p>

<ul>
<li><code>dns.TEMPFAIL</code>: timeout, SERVFAIL or similar.</li>
<li><code>dns.PROTOCOL</code>: got garbled reply.</li>
<li><code>dns.NXDOMAIN</code>: domain does not exists.</li>
<li><code>dns.NODATA</code>: domain exists but no data of reqd type.</li>
<li><code>dns.NOMEM</code>: out of memory while processing.</li>
<li><code>dns.BADQUERY</code>: the query is malformed.</li>
</ul>

<h2>dgram</h2>

<p>Datagram sockets are available through <code>require('dgram')</code>.  Datagrams are most commonly 
handled as IP/UDP messages, but they can also be used over Unix domain sockets.</p>

<h3>Event: 'message'</h3>

<p><code>function (msg, rinfo) { }</code></p>

<p>Emitted when a new datagram is available on a socket.  <code>msg</code> is a <code>Buffer</code> and <code>rinfo</code> is
an object with the sender's address information and the number of bytes in the datagram.</p>

<h3>Event: 'listening'</h3>

<p><code>function () { }</code></p>

<p>Emitted when a socket starts listening for datagrams.  This happens as soon as UDP sockets
are created.  Unix domain sockets do not start listening until calling <code>bind()</code> on them.</p>

<h3>Event: 'close'</h3>

<p><code>function () { }</code></p>

<p>Emitted when a socket is closed with <code>close()</code>.  No new <code>message</code> events will be emitted
on this socket.</p>

<h3>dgram.createSocket(type, [callback])</h3>

<p>Creates a datagram socket of the specified types.  Valid types are:
<code>udp4</code>, <code>udp6</code>, and <code>unix_dgram</code>.  </p>

<p>Takes an optional callback which is added as a listener for <code>message</code> events.</p>

<h3>dgram.send(buf, offset, length, path, [callback])</h3>

<p>For Unix domain datagram sockets, the destination address is a pathname in the filesystem.
An optional callback may be supplied that is invoked after the <code>sendto</code> call is completed
by the OS.  It is not safe to re-use <code>buf</code> until the callback is invoked.  Note that 
unless the socket is bound to a pathname with <code>bind()</code> there is no way to receive messages
on this socket.</p>

<p>Example of sending a message to syslogd on OSX via Unix domain socket <code>/var/run/syslog</code>:</p>

<pre><code>var dgram = require('dgram');
var message = new Buffer("A message to log.");
var client = dgram.createSocket("unix_dgram");
client.send(message, 0, message.length, "/var/run/syslog",
  function (err, bytes) {
    if (err) {
      throw err;
    }
    console.log("Wrote " + bytes + " bytes to socket.");
});
</code></pre>

<h3>dgram.send(buf, offset, length, port, address, [callback])</h3>

<p>For UDP sockets, the destination port and IP address must be specified.  A string
may be supplied for the <code>address</code> parameter, and it will be resolved with DNS.  An 
optional callback may be specified to detect any DNS errors and when <code>buf</code> may be
re-used.  Note that DNS lookups will delay the time that a send takes place, at
least until the next tick.  The only way to know for sure that a send has taken place
is to use the callback.</p>

<p>Example of sending a UDP packet to a random port on <code>localhost</code>;</p>

<pre><code>var dgram = require('dgram');
var message = new Buffer("Some bytes");
var client = dgram.createSocket("udp4");
client.send(message, 0, message.length, 41234, "localhost");
client.close();
</code></pre>

<h3>dgram.bind(path)</h3>

<p>For Unix domain datagram sockets, start listening for incoming datagrams on a
socket specified by <code>path</code>. Note that clients may <code>send()</code> without <code>bind()</code>,
but no datagrams will be received without a <code>bind()</code>.</p>

<p>Example of a Unix domain datagram server that echoes back all messages it receives:</p>

<pre><code>var dgram = require("dgram");
var serverPath = "/tmp/dgram_server_sock";
var server = dgram.createSocket("unix_dgram");

server.on("message", function (msg, rinfo) {
  console.log("got: " + msg + " from " + rinfo.address);
  server.send(msg, 0, msg.length, rinfo.address);
});

server.on("listening", function () {
  console.log("server listening " + server.address().address);
})

server.bind(serverPath);
</code></pre>

<p>Example of a Unix domain datagram client that talks to this server:</p>

<pre><code>var dgram = require("dgram");
var serverPath = "/tmp/dgram_server_sock";
var clientPath = "/tmp/dgram_client_sock";

var message = new Buffer("A message at " + (new Date()));

var client = dgram.createSocket("unix_dgram");

client.on("message", function (msg, rinfo) {
  console.log("got: " + msg + " from " + rinfo.address);
});

client.on("listening", function () {
  console.log("client listening " + client.address().address);
  client.send(message, 0, message.length, serverPath);
});

client.bind(clientPath);
</code></pre>

<h3>dgram.bind(port, [address])</h3>

<p>For UDP sockets, listen for datagrams on a named <code>port</code> and optional <code>address</code>.  If
<code>address</code> is not specified, the OS will try to listen on all addresses.</p>

<p>Example of a UDP server listening on port 41234:</p>

<pre><code>var dgram = require("dgram");

var server = dgram.createSocket("udp4");
var messageToSend = new Buffer("A message to send");

server.on("message", function (msg, rinfo) {
  console.log("server got: " + msg + " from " +
    rinfo.address + ":" + rinfo.port);
});

server.on("listening", function () {
  var address = server.address();
  console.log("server listening " +
      address.address + ":" + address.port);
});

server.bind(41234);
// server listening 0.0.0.0:41234
</code></pre>

<h3>dgram.close()</h3>

<p>Close the underlying socket and stop listening for data on it.  UDP sockets 
automatically listen for messages, even if they did not call <code>bind()</code>.</p>

<h3>dgram.address()</h3>

<p>Returns an object containing the address information for a socket.  For UDP sockets, 
this object will contain <code>address</code> and <code>port</code>.  For Unix domain sockets, it will contain
only <code>address</code>.</p>

<h3>dgram.setBroadcast(flag)</h3>

<p>Sets or clears the <code>SO_BROADCAST</code> socket option.  When this option is set, UDP packets
may be sent to a local interface's broadcast address.</p>

<h3>dgram.setTTL(ttl)</h3>

<p>Sets the <code>IP_TTL</code> socket option.  TTL stands for "Time to Live," but in this context it
specifies the number of IP hops that a packet is allowed to go through.  Each router or 
gateway that forwards a packet decrements the TTL.  If the TTL is decremented to 0 by a
router, it will not be forwarded.  Changing TTL values is typically done for network 
probes or when multicasting.</p>

<p>The argument to <code>setTTL()</code> is a number of hops between 1 and 255.  The default on most
systems is 64.</p>

<h2>Assert</h2>

<p>This module is used for writing unit tests for your applications, you can
access it with <code>require('assert')</code>.</p>

<h3>assert.fail(actual, expected, message, operator)</h3>

<p>Tests if <code>actual</code> is equal to <code>expected</code> using the operator provided.</p>

<h3>assert.ok(value, [message])</h3>

<p>Tests if value is a <code>true</code> value, it is equivalent to <code>assert.equal(true, value, message);</code></p>

<h3>assert.equal(actual, expected, [message])</h3>

<p>Tests shallow, coercive equality with the equal comparison operator ( <code>==</code> ). </p>

<h3>assert.notEqual(actual, expected, [message])</h3>

<p>Tests shallow, coercive non-equality with the not equal comparison operator ( <code>!=</code> ).</p>

<h3>assert.deepEqual(actual, expected, [message])</h3>

<p>Tests for deep equality.</p>

<h3>assert.notDeepEqual(actual, expected, [message])</h3>

<p>Tests for any deep inequality. </p>

<h3>assert.strictEqual(actual, expected, [message])</h3>

<p>Tests strict equality, as determined by the strict equality operator ( <code>===</code> ) </p>

<h3>assert.notStrictEqual(actual, expected, [message])</h3>

<p>Tests strict non-equality, as determined by the strict not equal operator ( <code>!==</code> ) </p>

<h3>assert.throws(block, [error], [message])</h3>

<p>Expects <code>block</code> to throw an error.</p>

<h3>assert.doesNotThrow(block, [error], [message])</h3>

<p>Expects <code>block</code> not to throw an error.</p>

<h3>assert.ifError(value)</h3>

<p>Tests if value is not a false value, throws if it is a true value. Useful when testing the first argument, <code>error</code> in callbacks.</p>

<h2>Path</h2>

<p>This module contains utilities for dealing with file paths.  Use
<code>require('path')</code> to use it.  It provides the following methods:</p>

<h3>path.join([path1], [path2], [...])</h3>

<p>Join all arguments together and resolve the resulting path.</p>

<p>Example:</p>

<pre><code>node&gt; require('path').join(
...   '/foo', 'bar', 'baz/asdf', 'quux', '..')
'/foo/bar/baz/asdf'
</code></pre>

<h3>path.normalizeArray(arr)</h3>

<p>Normalize an array of path parts, taking care of <code>'..'</code> and <code>'.'</code> parts.</p>

<p>Example:</p>

<pre><code>path.normalizeArray(['', 
  'foo', 'bar', 'baz', 'asdf', 'quux', '..'])
// returns
[ '', 'foo', 'bar', 'baz', 'asdf' ]
</code></pre>

<h3>path.normalize(p)</h3>

<p>Normalize a string path, taking care of <code>'..'</code> and <code>'.'</code> parts.</p>

<p>Example:</p>

<pre><code>path.normalize('/foo/bar/baz/asdf/quux/..')
// returns
'/foo/bar/baz/asdf'
</code></pre>

<h3>path.dirname(p)</h3>

<p>Return the directory name of a path.  Similar to the Unix <code>dirname</code> command.</p>

<p>Example:</p>

<pre><code>path.dirname('/foo/bar/baz/asdf/quux')
// returns
'/foo/bar/baz/asdf'
</code></pre>

<h3>path.basename(p, [ext])</h3>

<p>Return the last portion of a path.  Similar to the Unix <code>basename</code> command.</p>

<p>Example:</p>

<pre><code>path.basename('/foo/bar/baz/asdf/quux.html')
// returns
'quux.html'

path.basename('/foo/bar/baz/asdf/quux.html', '.html')
// returns
'quux'
</code></pre>

<h3>path.extname(p)</h3>

<p>Return the extension of the path.  Everything after the last '.' in the last portion
of the path.  If there is no '.' in the last portion of the path or the only '.' is
the first character, then it returns an empty string.  Examples:</p>

<pre><code>path.extname('index.html')
// returns 
'.html'

path.extname('index')
// returns
''
</code></pre>

<h3>path.exists(p, [callback])</h3>

<p>Test whether or not the given path exists.  Then, call the <code>callback</code> argument with either true or false.  Example:</p>

<pre><code>path.exists('/etc/passwd', function (exists) {
  sys.debug(exists ? "it's there" : "no passwd!");
});
</code></pre>

<h2>URL</h2>

<p>This module has utilities for URL resolution and parsing.
Call <code>require('url')</code> to use it.</p>

<p>Parsed URL objects have some or all of the following fields, depending on
whether or not they exist in the URL string. Any parts that are not in the URL
string will not be in the parsed object. Examples are shown for the URL</p>

<p><code>'http://user:pass@host.com:8080/p/a/t/h?query=string#hash'</code></p>

<ul>
<li><p><code>href</code></p>

<p>The full URL that was originally parsed. Example:
<code>'http://user:pass@host.com:8080/p/a/t/h?query=string#hash'</code></p></li>
<li><p><code>protocol</code></p>

<p>The request protocol.  Example: <code>'http:'</code></p></li>
<li><p><code>host</code></p>

<p>The full host portion of the URL, including port and authentication information. Example:
<code>'user:pass@host.com:8080'</code></p></li>
<li><p><code>auth</code></p>

<p>The authentication information portion of a URL.  Example: <code>'user:pass'</code></p></li>
<li><p><code>hostname</code></p>

<p>Just the hostname portion of the host.  Example: <code>'host.com'</code></p></li>
<li><p><code>port</code></p>

<p>The port number portion of the host.  Example: <code>'8080'</code></p></li>
<li><p><code>pathname</code></p>

<p>The path section of the URL, that comes after the host and before the query, including the initial slash if present.  Example: <code>'/p/a/t/h'</code></p></li>
<li><p><code>search</code></p>

<p>The 'query string' portion of the URL, including the leading question mark. Example: <code>'?query=string'</code></p></li>
<li><p><code>query</code></p>

<p>Either the 'params' portion of the query string, or a querystring-parsed object. Example:
<code>'query=string'</code> or <code>{'query':'string'}</code></p></li>
<li><p><code>hash</code></p>

<p>The 'fragment' portion of the URL including the pound-sign. Example: <code>'#hash'</code></p></li>
</ul>

<p>The following methods are provided by the URL module:</p>

<h3>url.parse(urlStr, parseQueryString=false)</h3>

<p>Take a URL string, and return an object.  Pass <code>true</code> as the second argument to also parse
the query string using the <code>querystring</code> module.</p>

<h3>url.format(urlObj)</h3>

<p>Take a parsed URL object, and return a formatted URL string.</p>

<h3>url.resolve(from, to)</h3>

<p>Take a base URL, and a href URL, and resolve them as a browser would for an anchor tag.</p>

<h2>Query String</h2>

<p>This module provides utilities for dealing with query strings.  It provides the following methods:</p>

<h3>querystring.stringify(obj, sep='&amp;', eq='=', munge=true)</h3>

<p>Serialize an object to a query string.  Optionally override the default separator and assignment characters.
Example:</p>

<pre><code>querystring.stringify({foo: 'bar'})
// returns
'foo=bar'

querystring.stringify({foo: 'bar', baz: 'bob'}, ';', ':')
// returns
'foo:bar;baz:bob'
</code></pre>

<p>By default, this function will perform PHP/Rails-style parameter munging for arrays and objects used as
values within <code>obj</code>.
Example:</p>

<pre><code>querystring.stringify({foo: ['bar', 'baz', 'boz']})
// returns
'foo%5B%5D=bar&amp;foo%5B%5D=baz&amp;foo%5B%5D=boz'

querystring.stringify({foo: {bar: 'baz'}})
// returns
'foo%5Bbar%5D=baz'
</code></pre>

<p>If you wish to disable the array munging (e.g. when generating parameters for a Java servlet), you
can set the <code>munge</code> argument to <code>false</code>.
Example:</p>

<pre><code>querystring.stringify({foo: ['bar', 'baz', 'boz']}, '&amp;', '=', false)
// returns
'foo=bar&amp;foo=baz&amp;foo=boz'
</code></pre>

<p>Note that when <code>munge</code> is <code>false</code>, parameter names with object values will still be munged.</p>

<h3>querystring.parse(str, sep='&amp;', eq='=')</h3>

<p>Deserialize a query string to an object.  Optionally override the default separator and assignment characters.</p>

<pre><code>querystring.parse('a=b&amp;b=c')
// returns
{ 'a': 'b'
, 'b': 'c'
}
</code></pre>

<p>This function can parse both munged and unmunged query strings (see <code>stringify</code> for details).</p>

<h3>querystring.escape</h3>

<p>The escape function used by <code>querystring.stringify</code>, provided so that it could be overridden if necessary.</p>

<h3>querystring.unescape</h3>

<p>The unescape function used by <code>querystring.parse</code>, provided so that it could be overridden if necessary.</p>

<h2>REPL</h2>

<p>A Read-Eval-Print-Loop (REPL) is available both as a standalone program and easily
includable in other programs.  REPL provides a way to interactively run
JavaScript and see the results.  It can be used for debugging, testing, or
just trying things out.</p>

<p>By executing <code>node</code> without any arguments from the command-line you will be
dropped into the REPL. It has simplistic emacs line-editing.</p>

<pre><code>mjr:~$ node
Type '.help' for options.
node&gt; a = [ 1, 2, 3];
[ 1, 2, 3 ]
node&gt; a.forEach(function (v) {
...   console.log(v);
...   });
1
2
3
</code></pre>

<p>For advanced line-editors, start node with the environmental variable <code>NODE_NO_READLINE=1</code>.
This will start the REPL in canonical terminal settings which will allow you to use with <code>rlwrap</code>.</p>

<p>For example, you could add this to your bashrc file:</p>

<pre><code>alias node="env NODE_NO_READLINE=1 rlwrap node"
</code></pre>

<h3>repl.start(prompt='node> ', stream=process.openStdin())</h3>

<p>Starts a REPL with <code>prompt</code> as the prompt and <code>stream</code> for all I/O.  <code>prompt</code>
is optional and defaults to <code>node&gt;</code>.  <code>stream</code> is optional and defaults to 
<code>process.openStdin()</code>.</p>

<p>Multiple REPLs may be started against the same running instance of node.  Each
will share the same global object but will have unique I/O.</p>

<p>Here is an example that starts a REPL on stdin, a Unix socket, and a TCP socket:</p>

<pre><code>var net = require("net"),
    repl = require("repl");

connections = 0;

repl.start("node via stdin&gt; ");

net.createServer(function (socket) {
  connections += 1;
  repl.start("node via Unix socket&gt; ", socket);
}).listen("/tmp/node-repl-sock");

net.createServer(function (socket) {
  connections += 1;
  repl.start("node via TCP socket&gt; ", socket);
}).listen(5001);
</code></pre>

<p>Running this program from the command line will start a REPL on stdin.  Other
REPL clients may connect through the Unix socket or TCP socket. <code>telnet</code> is useful
for connecting to TCP sockets, and <code>socat</code> can be used to connect to both Unix and
TCP sockets.</p>

<p>By starting a REPL from a Unix socket-based server instead of stdin, you can 
connect to a long-running node process without restarting it.</p>

<h3>REPL Features</h3>

<p>Inside the REPL, Control+D will exit.  Multi-line expressions can be input.</p>

<p>The special variable <code>_</code> (underscore) contains the result of the last expression.</p>

<pre><code>node&gt; [ "a", "b", "c" ]
[ 'a', 'b', 'c' ]
node&gt; _.length 
3
node&gt; _ += 1
4
</code></pre>

<p>The REPL provides access to any variables in the global scope. You can expose a variable 
to the REPL explicitly by assigning it to the <code>context</code> object associated with each
<code>REPLServer</code>.  For example:</p>

<pre><code>// repl_test.js
var repl = require("repl"),
    msg = "message";

repl.start().context.m = msg;
</code></pre>

<p>Things in the <code>context</code> object appear as local within the REPL:</p>

<pre><code>mjr:~$ node repl_test.js 
node&gt; m
'message'
</code></pre>

<p>There are a few special REPL commands:</p>

<ul>
<li><p><code>.break</code> - While inputting a multi-line expression, sometimes you get lost or just don't care 
about completing it.  <code>.break</code> will start over.</p></li>
<li><p><code>.clear</code> - Resets the <code>context</code> object to an empty object and clears any multi-line expression.</p></li>
<li><p><code>.exit</code> - Close the I/O stream, which will cause the REPL to exit.</p></li>
<li><p><code>.help</code> - Show this list of special commands.</p></li>
</ul>

<h2>Modules</h2>

<p>Node uses the CommonJS module system.</p>

<p>Node has a simple module loading system.  In Node, files and modules are in
one-to-one correspondence.  As an example, <code>foo.js</code> loads the module
<code>circle.js</code> in the same directory.</p>

<p>The contents of <code>foo.js</code>:</p>

<pre><code>var circle = require('./circle');
console.log( 'The area of a circle of radius 4 is '
           + circle.area(4));
</code></pre>

<p>The contents of <code>circle.js</code>:</p>

<pre><code>var PI = 3.14;

exports.area = function (r) {
  return PI * r * r;
};

exports.circumference = function (r) {
  return 2 * PI * r;
};
</code></pre>

<p>The module <code>circle.js</code> has exported the functions <code>area()</code> and
<code>circumference()</code>.  To export an object, add to the special <code>exports</code>
object.  (Alternatively, one can use <code>this</code> instead of <code>exports</code>.) Variables
local to the module will be private. In this example the variable <code>PI</code> is
private to <code>circle.js</code>. The function <code>puts()</code> comes from the module <code>'sys'</code>,
which is a built-in module. Modules which are not prefixed by <code>'./'</code> are
built-in module--more about this later.</p>

<p>A module prefixed with <code>'./'</code> is relative to the file calling <code>require()</code>.
That is, <code>circle.js</code> must be in the same directory as <code>foo.js</code> for
<code>require('./circle')</code> to find it.</p>

<p>Without the leading <code>'./'</code>, like <code>require('assert')</code> the module is searched
for in the <code>require.paths</code> array. <code>require.paths</code> on my system looks like
this: </p>

<p><code>[ '/home/ryan/.node_libraries' ]</code></p>

<p>That is, when <code>require('assert')</code> is called Node looks for: </p>

<ul>
<li>1: <code>/home/ryan/.node_libraries/assert.js</code></li>
<li>2: <code>/home/ryan/.node_libraries/assert.node</code></li>
<li>3: <code>/home/ryan/.node_libraries/assert/index.js</code></li>
<li>4: <code>/home/ryan/.node_libraries/assert/index.node</code></li>
</ul>

<p>interrupting once a file is found. Files ending in <code>'.node'</code> are binary Addon
Modules; see 'Addons' below. <code>'index.js'</code> allows one to package a module as
a directory.</p>

<p><code>require.paths</code> can be modified at runtime by simply unshifting new
paths onto it, or at startup with the <code>NODE_PATH</code> environmental
variable (which should be a list of paths, colon separated).</p>

<h2>Addons</h2>

<p>Addons are dynamically linked shared objects. They can provide glue to C and
C++ libraries. The API (at the moment) is rather complex, involving
knowledge of several libraries:</p>

<ul>
<li><p>V8 JavaScript, a C++ library. Used for interfacing with JavaScript:
creating objects, calling functions, etc.  Documented mostly in the
<code>v8.h</code> header file (<code>deps/v8/include/v8.h</code> in the Node source tree).</p></li>
<li><p>libev, C event loop library. Anytime one needs to wait for a file
descriptor to become readable, wait for a timer, or wait for a signal to
received one will need to interface with libev.  That is, if you perform
any I/O, libev will need to be used.  Node uses the <code>EV_DEFAULT</code> event
loop.  Documentation can be found http:/cvs.schmorp.de/libev/ev.html[here].</p></li>
<li><p>libeio, C thread pool library. Used to execute blocking POSIX system
calls asynchronously. Mostly wrappers already exist for such calls, in
<code>src/file.cc</code> so you will probably not need to use it. If you do need it,
look at the header file <code>deps/libeio/eio.h</code>.</p></li>
<li><p>Internal Node libraries. Most importantly is the <code>node::ObjectWrap</code>
class which you will likely want to derive from.</p></li>
<li><p>Others. Look in <code>deps/</code> for what else is available.</p></li>
</ul>

<p>Node statically compiles all its dependencies into the executable. When
compiling your module, you don't need to worry about linking to any of these
libraries.</p>

<p>To get started let's make a small Addon which does the following except in
C++:</p>

<pre><code>exports.hello = 'world';
</code></pre>

<p>To get started we create a file <code>hello.cc</code>:</p>

<pre><code>#include &lt;v8.h&gt;

using namespace v8;

extern "C" void
init (Handle&lt;Object&gt; target) 
{
  HandleScope scope;
  target-&gt;Set(String::New("hello"), String::New("World"));
}
</code></pre>

<p>This source code needs to be built into <code>hello.node</code>, the binary Addon. To
do this we create a file called <code>wscript</code> which is python code and looks
like this:</p>

<pre><code>srcdir = '.'
blddir = 'build'
VERSION = '0.0.1'

def set_options(opt):
  opt.tool_options('compiler_cxx')

def configure(conf):
  conf.check_tool('compiler_cxx')
  conf.check_tool('node_addon')

def build(bld):
  obj = bld.new_task_gen('cxx', 'shlib', 'node_addon')
  obj.target = 'hello'
  obj.source = 'hello.cc'
</code></pre>

<p>Running <code>node-waf configure build</code> will create a file
<code>build/default/hello.node</code> which is our Addon.</p>

<p><code>node-waf</code> is just http://code.google.com/p/waf/[WAF], the python-based build system. <code>node-waf</code> is
provided for the ease of users.</p>

<p>All Node addons must export a function called <code>init</code> with this signature:</p>

<pre><code>extern 'C' void init (Handle&lt;Object&gt; target)
</code></pre>

<p>For the moment, that is all the documentation on addons. Please see
<a href="http://github.com/ry/node_postgres">http://github.com/ry/node_postgres</a> for a real example.</p>

<h2>Appendix - Third Party Modules</h2>

<p>There are many third party modules for Node. At the time of writing, August
2010, the master repository of modules is
http://github.com/ry/node/wiki/modules[the wiki page].</p>

<p>This appendix is intended as a SMALL guide to new-comers to help them
quickly find what are considered to be quality modules. It is not intended
to be a complete list.  There may be better more complete modules found
elsewhere.</p>

<ul>
<li><p>Module Installer: <a href="http://github.com/isaacs/npm">npm</a></p></li>
<li><p>HTTP Middleware: <a href="http://github.com/senchalabs/connect">Connect</a></p></li>
<li><p>Web Framework: <a href="http://github.com/visionmedia/express">Express</a></p></li>
<li><p>Web Sockets: <a href="http://github.com/LearnBoost/Socket.IO-node">Socket.IO</a></p></li>
<li><p>HTML Parsing: <a href="http://github.com/aredridel/html5">HTML5</a></p></li>
<li><p><a href="http://github.com/agnat/node_mdns">mDNS/Zeroconf/Bonjour</a></p></li>
<li><p><a href="http://github.com/ry/node-amqp">RabbitMQ, AMQP</a></p></li>
<li><p><a href="http://github.com/felixge/node-mysql">mysql</a></p></li>
<li><p>Serialization: <a href="http://github.com/pgriess/node-msgpack">msgpack</a></p></li>
<li><p>Scraping: <a href="http://github.com/silentrob/Apricot">Apricot</a></p></li>
<li><p>Debugger: <a href="http://github.com/smtlaissezfaire/ndb">ndb</a> is a CLI debugger
<a href="http://github.com/dannycoates/node-inspector">inspector</a> is a web based
tool.</p></li>
<li><p><a href="http://github.com/mranney/node_pcap">pcap binding</a></p></li>
<li><p><a href="http://github.com/mscdex/node-ncurses">ncurses</a></p></li>
<li><p>Testing/TDD/BDD: <a href="http://vowsjs.org/">vows</a>,
<a href="http://github.com/visionmedia/expresso">expresso</a>,
<a href="http://github.com/tmpvar/mjsunit.runner">mjsunit.runner</a></p></li>
</ul>

<p>Patches to this list are welcome.</p>
