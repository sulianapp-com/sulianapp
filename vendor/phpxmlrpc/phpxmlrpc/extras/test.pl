#!/usr/local/bin/perl

use Frontier::Client;

my $serverURL='http://phpxmlrpc.sourceforge.net/server.php';

# try the simplest example

my $client = Frontier::Client->new( 'url' => $serverURL,
		'debug' => 0, 'encoding' => 'iso-8859-1' );
my $resp = $client->call("examples.getStateName", 32);

print "Got '${resp}'\n";

# now send a mail to nobody in particular

$resp = $client->call("mail.send", ("edd", "Test",  
	"Bonjour. Je m'appelle Gérard. Mañana. ", "freddy", "", "", 
	'text/plain; charset="iso-8859-1"'));

if ($resp->value()) {
	print "Mail sent OK.\n";
} else {
	print "Error sending mail.\n";
}

# test echoing of characters works fine

$resp = $client->call("examples.echo", 'Three "blind" mice - ' . 
	"See 'how' they run");
print $resp . "\n";

# test name and age example. this exercises structs and arrays 

$resp = $client->call("examples.sortByAge", 
											[ { 'name' => 'Dave', 'age' => 35},
												{ 'name' => 'Edd', 'age' => 45 },
												{ 'name' => 'Fred', 'age' => 23 },
												{ 'name' => 'Barney', 'age' => 36 } ] );

my $e;
foreach $e (@$resp) {
	print $$e{'name'} . ", " . $$e{'age'} . "\n";
}

# test base64

$resp = $client->call("examples.decode64", 
											$client->base64("TWFyeSBoYWQgYSBsaXR0bGUgbGFtYiBTaGUgd" .
																			"GllZCBpdCB0byBhIHB5bG9u"));

print $resp . "\n";
