<script src="ot.js"></script>
<script src="server.js"></script>
<script>
var server = new ot.Server("lorem ipsum");
server.broadcast = function (operation) {
  // you have to broadcast the operation to all connected
  // clients including the one that the operation came from
};

// when you receive an operation as a JSON string from one of the clients, do:
function onReceiveOperation (json) {
  var operation = ot.Operation.fromJSON(JSON.parse(json));
  console.log(json+" "+operation);
}
</script>
