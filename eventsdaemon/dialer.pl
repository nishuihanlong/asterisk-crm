#!/usr/bin/perl
#!/opt/ActivePerl-5.8/bin/perl
use strict;
use Socket;
use DBI;
use Time::Local;
use Config::IniFiles;
use Digest::MD5 qw(md5_hex);
use POSIX 'setsid';
use FindBin qw($Bin);
use POSIX qw(strftime);
use Data::Dumper;
use Asterisk::Manager;
use Net::Telnet;
use threads; 
use threads::shared;
use Errno qw(EAGAIN); 
#use English '-no_match_vars'; 

#	TO-DO
#
#
# flash the buffer immediately
$| =1 ;


# read parameter from conf file
my $conf_file = "$Bin/astercc.conf";
my $cfg = new Config::IniFiles -file => $conf_file;

if (not defined $cfg) {
	print "Failed to parse $conf_file: \n";
	foreach(@Config::IniFiles::errors) {
		print "Error: $_\n" ;
	}
	exit(1);
}

my %astInfo = (
        asterisk => $cfg->val('asterisk', 'server'),
        asteriskport => $cfg->val('asterisk', 'port'),
        asteriskuser => $cfg->val('asterisk', 'username'),
		asterisksecret  => $cfg->val('asterisk', 'secret'),
   );

my %dbInfo = (
        dbtype => $cfg->val('database', 'dbtype'),
        dbhost => $cfg->val('database', 'dbhost'),
        dbname => $cfg->val('database', 'dbname'),
		dbport  => $cfg->val('database', 'dbport'),
 		dbuser  => $cfg->val('database', 'username'),
 		dbpasswd  => $cfg->val('database', 'password')
   );

#---------------------GOLBAL VARIABLE--------------------------------
my $licenceto = $cfg->val('licence', 'licenceto');
my $key = $cfg->val('licence', 'key');
my $debug =$cfg->val('database', 'debug');
my $max_channel_num = $cfg->val('system', 'max_channels');


my $SOCK;

my $dbh = &connect_mysql(%dbInfo);
my $avaiable_channel_num;
my $curid = 0;
my $pidFile = "/var/run/astercrm-dialer-$astInfo{'asterisk'}.pid";

#my $items = 20; #需要处理的任务数 
my $maxchild = 65; #最多线程数（1-65），perl最多允许64个子线程，加上主线程因此最多65个线程 
my $pid; 
my $main_pid; 
my $forks: shared = 1; #当前线程数 
#print "start\n"; 
my $item: shared = 0; #当前处理任务序号,起始序号为0 
my $myid = 1; #当前线程序号 

sub subprocess; 
#print "PID = $PID";
#exit;

#---------------------END GOLBAL VARIABLE--------------------------------

if ($ARGV[0] eq '-v'){		# print version
	print "astercrm dialer version 0.01-080420\n";
	print "copyright @2007-2008\n";
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
			print "astercrm dialer process: $line is killed. \n";
		}else{
			print "$res \n";
			print "cant kill astercrm dialer process. \n";
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
			print "astercrm dialer status: [start]\n";
		}else{
			print "astercrm dialer status: [stop]\n";
		}
    }else{
		print "cant find $pidFile, astercrm dialer may not start \n";
	}
	exit;
}elsif  ($ARGV[0] eq '-h'){
	print "********* astercrm dialer parameters *********\n";
	print "    -h show help message\n";
	print "    -d start as a daemon\n";
	print "    -s show astercrm dialer status\n";
	print "    -k stop astercrm dialer\n";
	print "    -v show astercrm dialer version \n";
	exit;
}

# 检查是否已经有正在运行的进程

if (-e $pidFile){
    if (open(MYFILE, $pidFile)) {
		my $line = <MYFILE>;
		my $res;
		my $res = `ps  --pid=$line 2>&1`; 
		if ($res =~ /\n(.*)\n/) {
			print "astercrm dialer is still running. Please stop first.\n"; #If no please del $pidFile \n";
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
	$main_pid=&become_daemon;

	open PIDFILE, ">$pidFile" or die "can't open $pidFile: $!\n";
	print PIDFILE $main_pid;
	close PIDFILE;
}


# run background

&debug("[DAEMON START FORK]");

while (1) {
	
	# need to check if there's free channel in system
	# we could get the value via AMI or astercc
	my $cur_channel_num = 4;
	#&ami_getActiveChannel();
	$avaiable_channel_num = $max_channel_num - $cur_channel_num;
	&debug("avaiable channel numuber: $avaiable_channel_num");
	#}

	# get all records in 1 minutes before dialtime
	my $date_hd = strftime "%Y-%m-%d %H:%M:%S", localtime(time + 60);

#	my $query = "SELECT * FROM diallist WHERE trytime < '3' ";
	my $query = "SELECT * FROM diallist WHERE dialtime <= '$date_hd' AND trytime < '3' ORDER BY dialtime ASC LIMIT 0, $avaiable_channel_num ";
	my $rows = &executeQuery($query,'rows');
#	print $rows;
	while (my $ref = $rows->fetchrow_hashref()) {
		# check account balance $ref->{'accountid'}
#		my $query = " SELECT accountBalance, resellerId FROM tblAccount WHERE accountId = $ref->{'accountid'}";
#		my $rows_tmp = &executeQuery($query,'rows');
#		if (my $ref_tmp = $rows_tmp->fetchrow_hashref()) {
#			if ($ref_tmp->{'accountBalance'} >= 0 ) {
#				# check reseller balance
#				my $query = " SELECT accountBalance, resellerId FROM tblAccount WHERE accountId = $ref_tmp->{'resellerId'}";
#				my $rows_tmp = &executeQuery($query,'rows');
#				if (my $ref_tmp = $rows_tmp->fetchrow_hashref()) {
#					if ($ref_tmp->{'accountBalance'} < 0 ) {
#
#					}else{
#						# no enough credit, skip to next one
#						last;
#					}
#				}
#			}else{
#				# no enough credit, skip to next one
#				last;
#			}
#		}
		# TO DO
		$query = "SELECT extension FROM astercrm_account WHERE username = '$ref->{'creby'}'";
		my $account_row = &executeQuery($query,'rows');
		my $account_res = $account_row->fetchrow_hashref();
		
		my $callerid = $account_res->{'extension'};
		if ($account_res->{'extension'} eq '') {
			$callerid = '0000';
		}

		$query = "SELECT * FROM campaign WHERE id = '$ref->{'campaignid'}'";
		my $campaign_row = &executeQuery($query,'rows');
		my $campaign_res = $campaign_row->fetchrow_hashref();
		
		my $exten = $campaign_res->{'inexten'};
		if ($campaign_res->{'inexten'} eq '') {
			$exten = $ref->{'dialnumber'};
		}
		my $incontext = $campaign_res->{'incontext'};
		my $outcontext = $campaign_res->{'outcontext'};

		my %param;
		#$param{'Channel'} = "Local/$ref->{'dialnumber'}\@from-sipuser";
		$param{'Channel'} = "Local/$ref->{'dialnumber'}\@$outcontext";
		$param{'Exten'} = $exten;
		$param{'Context'} = $incontext;
		$param{'Priority'} = "1";
		$param{'Timeout'} = 30000;
		$param{'CallerID'} = $callerid;
		$param{'Variable'} = "campaignid=$ref->{'campaignid'}|accountid=$ref->{'accountid'}|recordid=$ref->{'id'}|dialednumber=$ref->{'dialnumber'}";
		&ami_sendcommand('Originate',%param);

		# UPDATE trytime
		my $query = "UPDATE diallist SET trytime = trytime + 1 WHERE id = $ref->{'id'}";
		&executeQuery($query);
	}
	#exit;
	sleep(50);
}

unlink $pidFile;
exit;

sub connection_test{
	my $result = 1;

	&debug("Connecting to $dbInfo{'dbtype'} database on $dbInfo{'dbhost'}:");
	my $dbh = &connect_mysql(%dbInfo);
	if( !$dbh ){
		&debug("Database connection unsuccessful. Please check your login credentials. ".$DBI::errstr);
		$result = 0;
	}else{
		&debug("Database connection successful.");
	}
	&debug("Connecting to asterisk on $astInfo{'asterisk'} port $astInfo{'asteriskport'}:");
	$SOCK = &connect_sock(%astInfo);
	if( !$SOCK ){
		&debug("Asterisk connection unsuccessful. Please check your connect parameter.");
		$result = 0;
	}else{
		my $msg = <$SOCK>;
		if ($msg !~ /Asterisk Call Manager/) {
			&debug("Asterisk connection failed!");
			$result = 0;
		}else{
			&debug("Asterisk socket connection successful.");
			# check username and password
			&debug("Check asterisk username & secret:");
			send($SOCK, "ACTION: LOGIN\r\nUSERNAME: $astInfo{'asteriskuser'}\r\nSECRET: $astInfo{'asterisksecret'}\r\n\r\n", 0);
			my $msg = <$SOCK>;
			if ($msg =~ /Response: Success/) {
				&debug("Success");
			}else{
				&debug("Failed");
				$result = 0;
			}
		}
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

	if ($queryType eq '') {
			my $affect = $dbh->do($query) or die $dbh->errstr;
			return $affect;
	}elsif ($queryType eq 'rows'){
			my $rows = $dbh->prepare($query);
			$rows->execute() or die $dbh->errstr;
			return $rows;
	}
}

sub connect_sock{
	my	%info = @_;

	#CONNECT
	my	($SOCK,$host,$addr,$msg);
	$host = inet_aton($info{'asterisk'});
	socket($SOCK, AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	$addr = sockaddr_in(@info{'asteriskport'},$host);

	my $test = connect($SOCK,$addr);
	if ($test) {
		return $SOCK;
	}

	return 0;
}

sub connect_ami{
	my	%info = @_;

	#CONNECT
	my	($SOCK,$host,$addr,$msg);
	$host = inet_aton($info{'asterisk'});
	socket($SOCK, AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	$addr = sockaddr_in(@info{'asteriskport'},$host);

	connect($SOCK,$addr) or die "Can't Connect to Asterisk Manager Port : $!";


	$msg = <$SOCK>;
	if ($msg !~ /Asterisk Call Manager/) {
		die "connection failed!";exit;
	}

	#LOGIN IN
	send($SOCK, "ACTION: LOGIN\r\nUSERNAME: $info{'asteriskuser'}\r\nSECRET: $info{'asterisksecret'}\r\nEVENTS: ON\r\n\r\n", 0);

	return($SOCK);
}

sub connect_mysql
{
	my	%info = @_;
#	my	$dbh = DBI->connect("DBI:mysql:database=$info{'dbname'};host=$info{'dbhost'};port=$info{'dbport'}",$info{'dbuser'},$info{'dbpasswd'}) or die "Can't Connect Database Server: $!";
	my	$dbh = DBI->connect("DBI:mysql:database=$info{'dbname'};host=$info{'dbhost'};port=$info{'dbport'}",$info{'dbuser'},$info{'dbpasswd'});
	return($dbh);
}

sub debug{
	my $message = shift;
	my $time=scalar localtime;
	if ($debug > 0) {
		if ($ARGV[0] eq '-d'){		# output to file
			open (HDW,">>$Bin/astercrm-dialer-log.txt");
			print HDW $time," ",$message,"\n";
			close HDW;
		}else{
			print $time," ",$message,"\n";
		}
	}
}

sub ami_sendcommand{
	my $command = shift;
	my %param = @_;
	$SOCK = &connect_sock(%astInfo);
	if( !$SOCK ){
		&debug("Asterisk connection unsuccessful. Please check your connect parameter.");
	}else{
		my $msg = <$SOCK>;
		if ($msg !~ /Asterisk Call Manager/) {
			&debug("Asterisk connection failed!");
		}else{
			&debug("Asterisk socket connection successful.");
			# check username and password
			&debug("Check asterisk username & secret:");
			my $send = "ACTION: LOGIN\r\n".
				"USERNAME: $astInfo{'asteriskuser'}\r\n".
				"SECRET: $astInfo{'asterisksecret'}\r\n\r\n";		
			send($SOCK, 	$send, 0);
			my $msg = <$SOCK>;
			if ($msg =~ /Response: Success/) {
				&debug("Command Success");
				$send = "ACTION: $command\r\n";
				my $i = 0;
				foreach  $key (keys %param) {
					$send .= "$key: $param{$key}\r\n";
				}
				$send .=	"\r\n";
				print $send;
				send($SOCK, 	$send, 0);
			}else{
				&debug("Command Failed");
			}
		}
	}
}

sub ami_getActiveChannel{
	my $pop = new Net::Telnet (Telnetmode => 0);
	$pop->open(Host => $astInfo{'asterisk'},
		   Port => $astInfo{'asteriskport'});

	## Read connection message.
	my $line = $pop->getline;
	die $line unless $line =~ /^Asterisk/;

	## Send user name.
	$pop->print("Action: login");
	$pop->print("Username: $astInfo{'asteriskuser'}");
	$pop->print("Secret: $astInfo{'asterisksecret'}");
	$pop->print("Events: off");
	$pop->print("");

	$pop->print("Action: command");
	$pop->print("Command: show channels");
	$pop->print("");
	
	my @fields;
	while (($line = $pop->getline) and ($line !~ /active SIP channel/o))
	{
		print $line;
		if ($line =~ /(.*) active calls$/) {
			return $1;
			last;
		}
	}

	$pop->print("Action: logoff");
	$pop->print("");
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
