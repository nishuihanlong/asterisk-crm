#!/usr/bin/perl
use strict;
use Socket;
use DBI;

# Config;
my $log_life = 180;

my	$create_table = qq~CREATE TABLE events(
`id` INT(16) PRIMARY KEY AUTO_INCREMENT NOT NULL,
`timestamp` DATETIME,
`event` TEXT,
INDEX `timestamp` (`timestamp`)
) ENGINE = MyISAM;~;



# AUTO FLASH
$|=1;

#CONFIG FILES
my $dbname = 'asterisk';
my $dbhost = '127.0.0.1';
my $dbport = '3306';
my $dbuser = 'asteriskuser';
my $dbpasswd = 'movingon';

my $asterisk = 'localhost';
my $asteriskport = 5038;
my $asteriskuser = 'admin';
my $asterisksecret = 'amp111';


#CONNECT MYSQL SERVER
my $dbh = &connect_mysql(dbname=>$dbname,
						dbhost=>$dbhost,
						dbport=>$dbport,
						dbuser=>$dbuser,
						dbpasswd=>$dbpasswd);
# if try to auto create table
# &auto_create_table();

#CONNECT
my $SOCK = &connect_ami(host=>$asterisk,
						port=>$asteriskport,
						user=>$asteriskuser,
						secret=>$asterisksecret);
#READ
my	$response;
while (my $line = <$SOCK>) {
	#LAST LINE
	if ($line eq "\r\n") {
		warn "RECEIVE : $response-----------\n" if ($ARGV[0] eq '-v');
		&putdb($response);
		undef($response);
	} else {
		$line =~ s/\r/ /g;
		$line =~ s/\n/ /g;
		$response .= $line if $line;
	}
}

# DISCONNECT ALL
close($SOCK);
$dbh->disconnect();




sub putdb
{
my	$response = shift;
	return if ($response eq '');

	#if try to reconnect database
	if (!$dbh->ping) {
		warn "Reconnect database\n";
		my $dbh = &connect_mysql(dbname=>$dbname,
								dbhost=>$dbhost,
								dbport=>$dbport,
								dbuser=>$dbuser,
								dbpasswd=>$dbpasswd);
	}

	# Delete old
my	$timestamp = time();	$timestamp -= $log_life;
my	@datetime = localtime($timestamp);	$datetime[5] += 1900;	$datetime[4]++;
	$dbh->do("DELETE FROM events WHERE timestamp <= '$datetime[5]-$datetime[4]-$datetime[3] $datetime[2]:$datetime[1]:$datetime[0]'")
		or die $dbh->errstr;

	# Insert new
	$dbh->do("INSERT INTO events(timestamp,event) VALUES(now(),".$dbh->quote($response).")") or die $dbh->errstr;

return();
}

sub auto_create_table
{
my	$sth = $dbh->prepare("show tables like 'events'");
	$sth->execute or die $dbh->errstr;
my	$row = $sth->fetchrow_arrayref();
	$sth->finish;

	#if to create table
	if ($row->[0] eq '') {
		$dbh->do($create_table) or die $dbh->errstr;
		warn "Auto Created table\n";
	}
return();
}

sub connect_ami
{
my	%info = @_;

#CONNECT
my	($SOCK,$host,$addr,$msg);
	$host = inet_aton($info{'host'});
	socket($SOCK, AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	$addr = sockaddr_in($info{'port'},$host);

	connect($SOCK,$addr) or die "Can't Connect to Asterisk Manager Port : $!";

	$msg = <$SOCK>;
	if ($msg !~ /Asterisk Call Manager/) {
		die "Connect not ok!";exit;
	}

	#LOGIN IN
	send($SOCK, "ACTION: LOGIN\r\nUSERNAME: $info{'user'}\r\nSECRET: $info{'secret'}\r\nEVENTS: ON\r\n\r\n", 0);

return($SOCK);
}

sub connect_mysql
{
my	%info = @_;
my	$dbh = DBI->connect("DBI:mysql:database=$info{'dbname'};host=$info{'dbhost'};port=$info{'dbport'}",$info{'dbuser'},
			$info{'dbpasswd'}) or die "Can't Connect Database Server: $!";
return($dbh);
}
