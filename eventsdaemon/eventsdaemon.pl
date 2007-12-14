#!/usr/bin/perl
use strict;
use Socket;
use DBI;
use POSIX 'setsid';

my $asterisk = 'localhost';
my $asteriskport = 5038;
my $asteriskuser = '';
my $asterisksecret = '';

my $dbhost = '';
my $dbname = '';
my $dbport = '';
my $dbuser = '';
my $dbpasswd = '';

my $log_life = 180;

$SIG{__DIE__}=\&log_die;
$SIG{__WARN__}=\&log_warn;

$|=1;

################
my $pid_file="/tmp/$asterisk.pid";
my $pid=$$;
my $daemon=0;
if ($ARGV[0] eq '-d'){
	  $daemon=1;
      $pid=&become_daemon;
}
open (HDW,">",$pid_file) or die "[EMERG] $!\n";
print HDW $pid;
close HDW;

#CONNECT

my $SOCK = &connect_ami(host=>$asterisk,
			port=>$asteriskport,
			user=>$asteriskuser,
		      secret=>$asterisksecret);

my $dbh = &connect_mysql(dbname=>$dbname,
			dbhost=>$dbhost,
    			dbport=>$dbport,
                        dbuser=>$dbuser,
    			dbpasswd=>$dbpasswd);

#&auto_create_table();

#Get message
my	$response;
while (my $line = <$SOCK>) {
	#LAST LINE
	if ($line eq "\r\n") {
		#warn "RECEIVE : $response-----------\n" if ($ARGV[0] eq '-v');
		&putdb($response);
		undef($response);
	} else {
		$line =~ s/\r/ /g;
		$line =~ s/\n/ /g;
		$response .= $line if $line;
	}
}
close($SOCK);

########################################
sub connect_ami
{
my	%info = @_;

#CONNECT
my     ($SOCK,$host,$addr,$msg);
	$host = inet_aton($info{'host'});
	socket($SOCK, AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	$addr = sockaddr_in($info{'port'},$host);

	warn '[warn] Connect to Asterisk now';
        foreach my $failed (0..3)
                {
                    if($failed >= 3){die '[Sorry] I try my best Connect to Asterisk Manager Port ,But I am tired!';}
                    elsif(connect($SOCK,$addr)){last;}
                    else
                    {
                    warn "[Sorry] Can not Connect to Asterisk Manager Port $!";
                    #sleep 180;
                    }
                }

	warn '[warn] Connect successful, Waiting for the message!';
        $msg = <$SOCK>;       
	if ($msg !~ /Asterisk Call Manager/) {die "[Sorry] Connect failed! Message is $msg";}

	#LOGIN IN
        warn '[warn] Login in Asterisk Manager';
	send($SOCK, "ACTION: LOGIN\r\nUSERNAME: $info{'user'}\r\nSECRET: $info{'secret'}\r\nEVENTS: ON\r\n\r\n", 0);
        $msg = <$SOCK>;
        if ($msg =~ /Error/) {die '[Sorry] Login in failed! Maybe your name or password is error!';}
return($SOCK);
}
##############################################

sub connect_mysql
{
my	%info = @_;
my	$dbh = DBI->connect("DBI:mysql:database=$info{'dbname'};host=$info{'dbhost'};port=$info{'dbport'}",$info{'dbuser'},$info{'dbpasswd'}) or die "Can't Connect Database Server: $!";
return($dbh);
}

###############################################

sub auto_create_table
{
my	$sth = $dbh->prepare("show tables like 'events'");
	$sth->execute or die $dbh->errstr;
my	$row = $sth->fetchrow_arrayref();
	$sth->finish;

	#if to create table
	if ($row->[0] eq '') {
                warn "Auto Created table";
		$dbh->do(qq~CREATE TABLE events(
                        `id` INT(16) PRIMARY KEY AUTO_INCREMENT NOT NULL,
                        `timestamp` DATETIME,
                        `event` TEXT,
                        INDEX `timestamp` (`timestamp`)) ENGINE = MyISAM;~) or die $dbh->errstr;
		
	}
return();
}

#######################################}
sub putdb
{
my	$response = shift;
	return if ($response eq '');

	#if try to reconnect database
	if (!$dbh->ping) {
	     warn "Reconnect database";
	     $dbh = &connect_mysql(dbname=>$dbname,
                                   dbhost=>$dbhost,
                                   dbport=>$dbport,
                                   dbuser=>$dbuser,
                                   dbpasswd=>$dbpasswd);
	}

	# Delete old
    if($log_life>0){
        my $timestamp = time();	$timestamp -= $log_life;
        my @datetime = localtime($timestamp);	$datetime[5] += 1900;	$datetime[4]++;
	$dbh->do("DELETE FROM events WHERE timestamp <= '$datetime[5]-$datetime[4]-$datetime[3] $datetime[2]:$datetime[1]:$datetime[0]'") or die $dbh->errstr;
        }
	#Insert new
	$dbh->do("INSERT INTO events(timestamp,event) VALUES(now(),".$dbh->quote($response).")") or die $dbh->errstr;

return();
}

##########################################

sub become_daemon {
    die "Can't fork" unless defined (my $child = fork);
    exit 0 if $child;
    setsid();
    open( STDIN, "</dev/null" );
    open( STDOUT, ">/dev/null" );
    open( STDERR, ">&STDOUT" );
    chdir '/';
    umask(0);
   $ENV{PATH} = '/bin:/sbin:/usr/bin:/usr/sbin';
    return $$;
}

#############################################
sub log_die
{
my $message =shift;
my $time=scalar localtime;
open (HDW,'>>log.txt');
print HDW $time," ",$message;
close HDW;
exit;
#die @_;
}

###############################################
sub log_warn
{
my $message =shift;
print $message;
my $time=scalar localtime;
open (HDW,'>>log.txt');
print HDW $time," ",$message;
close HDW;
}

############################
END{
	if($daemon)
		{
		warn('eventsdaemon will in daemon start!');
		}
	else{
		warn('eventsdaemon is exit now !');
		}
	}
