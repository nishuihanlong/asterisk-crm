#!/usr/bin/perl
use FindBin qw($Bin);
use lib "$Bin/lib";
use POSIX 'setsid';
use strict;
use DBI;
use Config::IniFiles;
use Data::Dumper;

my $conf_file = "$Bin/astercc.conf" ;
# read parameter from conf file
my $cfg = new Config::IniFiles -file => $conf_file;
if (not defined $cfg) {
	exit(1);
}

my %dbInfo = (
        dbtype => trim($cfg->val('database', 'dbtype')),
        dbhost => trim($cfg->val('database', 'dbhost')),
        dbname => trim($cfg->val('database', 'dbname')),
		dbport  => trim($cfg->val('database', 'dbport')),
 		dbuser  => trim($cfg->val('database', 'username')),
 		dbpasswd  => trim($cfg->val('database', 'password'))
   );

my $dbprefix = '';

my $debug = trim($cfg->val('database', 'debug'));

my $pidFile = "/var/run/processmonitors.pid";

$| =1 ;

if ($ARGV[0] eq '-v'){		# print version
	print "processmonitors version 0.01-091114\n";
	print "copyright \@2009\n";
	exit;
}elsif ($ARGV[0] eq '-t'){	 # test database & asterisk connection 
	&connection_test;
	exit;
}elsif ($ARGV[0] eq '-k'){
    if (open(MYFILE, $pidFile)) {
	    # here's what to do if the file opened successfully

		my $line = <MYFILE>;
		my $res;
		my $res = `kill -9 $line 2>&1`; 
		if ($res eq '') {
			print "processmonitors process: $line is killed. \n";
		}else{
			print "$res \n";
			print "cant kill processmonitors process. \n";
			exit;
		}
		unlink $pidFile;
    }else{
		print "cant find $pidFile. \n";
	}
	exit;
}elsif  ($ARGV[0] eq '-s'){
    if (open(MYFILE, $pidFile)) {
	    # here's what to do if the file opened successfully

		my $line = <MYFILE>;
		my $res;
		my $res = `ps  --pid=$line 2>&1`; 
		if ($res =~ /\n(.*)\n/) {
			print "processmonitors status: [start]\n";
		}else{
			print "processmonitors status: [stop]\n";
		}
    }else{
		print "cant find $pidFile, processmonitors may not start \n";
	}
	exit;
}elsif  ($ARGV[0] eq '-h'){
	print "********* processmonitors parameters *********\n";
	print "    -h show help message\n";
	print "    -i parse all queue logs in the log file\n";
	print "    -d start as a daemon\n";
	print "    -s show processmonitors status\n";
	print "    -k stop processmonitors\n";
	print "    -v show processmonitors version \n";
	exit;
}


if (-e $pidFile){
    if (open(MYFILE, $pidFile)) {
		my $line = <MYFILE>;
		my $res;
		my $res = `ps  --pid=$line 2>&1`; 
		if ($res =~ /\n(.*)\n/) {
			print "processmonitors daemon is still running. Please stop first.\n"; #If no please del $pidFile \n";
			exit;
		}else{
			unlink $pidFile;
		}
    }
}

if (!&connection_test){
	print("Connection failed, please check the log file for detail.\n");
	exit;
}

if ($ARGV[0] eq '-d'){
	# run background
	my $daemon=1;
	my $pid=&become_daemon;

	open PIDFILE, ">$pidFile" or die "can't open $pidFile: $!\n";
	print PIDFILE $pid;
	close PIDFILE;
}


my $dbh = &connect_mysql(%dbInfo);

my $query = "SELECT * FROM monitorrecord WHERE processed = 'no'";
my $rows = &executeQuery($query,'rows');

while ( my $ref = $rows->fetchrow_hashref() ) {
	my $orifile = "$ref->{'filename'}.$ref->{'fileformat'}";
	my $mp3file = "$ref->{'filename'}.mp3";
	my $execstr = '';
	if( -e "$ref->{'filename'}.$ref->{'fileformat'}" ){
		if($ref->{'fileformat'} eq 'wav'){
			$execstr = "lame --cbr -m m -t -F ".$orifile." ".$mp3file." 2>&1";

			system($execstr);
			
			if( -e $mp3file ){
				unlink($orifile);
				$query = "UPDATE monitorrecord SET fileformat = 'mp3',processed = 'yes' WHERE id = $ref->{'id'}";
				&executeQuery($query,'');
				&debug("$orifile converted to mp3");
			}
		}else{
			$query = "UPDATE monitorrecord SET processed = 'yes' WHERE id = $ref->{'id'}";
			&executeQuery($query,'');
		}

	}elsif( -e "$ref->{'filename'}-in.$ref->{'fileformat'}" && -e "$ref->{'filename'}-out.$ref->{'fileformat'}"){
		$execstr = "sox -m $ref->{'filename'}-in.$ref->{'fileformat'} $ref->{'filename'}-out.$ref->{'fileformat'} $orifile";
		system($execstr);
		if($ref->{'fileformat'} eq 'wav'){
			$execstr = "lame --cbr -m m -t -F ".$orifile." ".$mp3file." 2>&1";
			system($execstr);

			if( -e $mp3file ){
				unlink($orifile);
				unlink("$ref->{'filename'}-in.$ref->{'fileformat'}");
				unlink("$ref->{'filename'}-out.$ref->{'fileformat'}");

				$query = "UPDATE monitorrecord SET fileformat = 'mp3' , processed = 'yes' WHERE id = $ref->{'id'}";
				&executeQuery($query,'');
				&debug("$orifile mixed and converted to mp3");
			}
		}else{
			$query = "UPDATE monitorrecord SET processed = 'yes' WHERE id = $ref->{'id'}";
			&executeQuery($query,'');
		}

	}else{
		#file not found or only in or out
		$query = "UPDATE monitorrecord SET fileformat = 'error' , processed = 'yes' WHERE id = $ref->{'id'}";
		&executeQuery($query,'');
	}
}

unlink($pidFile);
exit;


sub connect_mysql
{
	my	%info = @_;
	my	$dbh = DBI->connect("DBI:mysql:database=$info{'dbname'};host=$info{'dbhost'};port=$info{'dbport'}",$info{'dbuser'},$info{'dbpasswd'});
	return($dbh);
}

sub connection_test{
	my $result = 1;

	&debug("Connecting to $dbInfo{'dbtype'} database on $dbInfo{'dbhost'}:");
	my $dbh = &connect_mysql(%dbInfo);
	if( !$dbh ){
		&debug("Database connection unsuccessful. Please check your login details. ".$DBI::errstr);
		$result = 0;
	}else{
		&debug("Database connection successful.");
	}
	return $result;
}

sub executeQuery
{
	my	$query = shift;
	return if ($query eq '');

	my	$queryType = shift;

	if (!$dbh->ping) {
		 &debug("Reconnect database");
		 $dbh = &connect_mysql(%dbInfo);
	}

	if ($debug > 10) {
		&debug("$query");
	}

	if ($queryType eq '') {
			my $affect = $dbh->do($query) or &debug($dbh->errstr."($query)");
			if ($affect eq '0E0'){
				return 0;
			}else{
				return $affect;
			}
	}elsif ($queryType eq 'rows'){
			my $rows = $dbh->prepare($query);
			$rows->execute() or &debug($dbh->errstr);
			return $rows;
	}elsif ($queryType eq 'insert'){
		$dbh->do($query) or &debug($dbh->errstr);
		return $dbh->{q{mysql_insertid}};
	}
}

sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub become_daemon {
    die "Can't fork" unless defined (my $child = fork);
    exit 0 if $child;#kill父进程
    setsid();
    open( STDIN, "</dev/null" );
    open( STDOUT, ">/dev/null" );
    open( STDERR, ">&STDOUT" );

	$SIG{__WARN__} = sub {
		&debug ("NOTE! " . join(" ", @_));
	};

	$SIG{__DIE__} = sub { 
		&debug ("FATAL! " . join(" ", @_));
		unlink $pidFile;
		exit;
	};

	$SIG{HUP} = $SIG{INT} = $SIG{TERM} = sub {
		# Any sort of death trigger results in death of all
		my $sig = shift;
		$SIG{$sig} = 'IGNORE';
		die "killed by $sig\n";
		exit;
	};

    umask(0);
	#$ENV{PATH} = '/bin:/sbin:/usr/bin:/usr/sbin';
    return $$;
}

sub debug{
	my $message = shift;
	my $time=scalar localtime;
	if ($debug > 0) {
		if ($ARGV[0] eq '-d'){		# output to file
			open (HDW,">>$Bin/processmonitorslog.txt");
			print HDW $time," ",$message,"\n";
			close HDW;
		}else{
			print $time," ",$message,"\n";
		}
	}
}