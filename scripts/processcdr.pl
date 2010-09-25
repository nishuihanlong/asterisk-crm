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
	print "can't find the config file\n";
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
	print "processmonitors version 0.011-100510\n";
	print "copyright \@2009-2010\n";
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
	#print "    -i parse all queue logs in the log file\n";
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

#获取所有座席帐户信息
my $query="SELECT * FROM astercrm_account ORDER BY id DESC";
my $rows = &executeQuery($query,'rows');
my %accountinfo;
while(my $ref = $rows->fetchrow_hashref() ) {
	
	$accountinfo{$ref->{'extension'}}{'id'} = $ref->{'id'};
	$accountinfo{$ref->{'extension'}}{'groupid'} = $ref->{'groupid'};
	if($ref->{'channel'} ne ''){
		$accountinfo{$ref->{'channel'}}{'id'} = $ref->{'id'};
		$accountinfo{$ref->{'channel'}}{'groupid'} = $ref->{'groupid'};
	}
	if($ref->{'agent'} ne ''){
		$accountinfo{"agent/$ref->{'agent'}"}{'id'} = $ref->{'id'};
		$accountinfo{"agent/$ref->{'agent'}"}{'groupid'} = $ref->{'groupid'};
	}
}
#print Dumper(\%accountinfo);
#exit;
my %cdrprocessed;
my $query = "SELECT * FROM mycdr WHERE processed = '0'  ORDER BY calldate ASC ";
my $rows = &executeQuery($query,'rows');

while ( my $ref = $rows->fetchrow_hashref() ) {
	if($cdrprocessed{$ref->{'id'}} > 0){
		next;
	}

	if($ref->{'src'} eq '' || $ref->{'dst'} eq '' || $ref->{'channel'} eq '' || $ref->{'dstchannel'} eq '' ){
		if($ref->{'queue'}){
			$query = "SELECT * FROM campaign WHERE queuename = '$ref->{'queue'}' AND enable = '1' order by id desc limit 1";
			my $campaign_rows = &executeQuery($query,'rows');
			if(my $campaign_ref = $campaign_rows->fetchrow_hashref()){
				$query = "UPDATE mycdr set  processed='1',astercrm_groupid='$campaign_ref->{'groupid'}' WHERE id='$ref->{'id'}'";
				&executeQuery($query,'');
			}else{
				$query = "UPDATE mycdr set  processed='1' WHERE id='$ref->{'id'}'";
				&executeQuery($query,'');
			}
		}else{
			$query = "UPDATE mycdr set  processed='1' WHERE id='$ref->{'id'}'";
			&executeQuery($query,'');
		}
		next;
	}

	my $joinrecords = '';
	my $pflag = 0;
	#检查当前有没有和本条cdr相关的且没有结束的通话
	$query = "SELECT * FROM curcdr WHERE srcchan='$ref->{'channel'}' OR srcchan='$ref->{'dstchannel'}'  OR dstchan='$ref->{'channel'}' OR dstchan='$ref->{'dstchannel'}' ";
	my $curcdr_rows = &executeQuery($query,'rows');
	if(my $curcdr_ref = $curcdr_rows->fetchrow_hashref()){
		$pflag = 1;
	}
	
	my @chan_tmp = split(/\-/,$ref->{'channel'});
	my $srcchan = $chan_tmp['0'];
	my @dstchan_tmp = split(/\-/,$ref->{'dstchannel'});
	my $dstchan = $dstchan_tmp['0'];

	my %accounts;
	if($accountinfo{$ref->{'src'}}{'id'} > 0 ){
		$accounts{'id'} = $accountinfo{$ref->{'src'}}{'id'};
		$accounts{'groupid'} = $accountinfo{$ref->{'src'}}{'groupid'};
	}elsif($accountinfo{$ref->{'dst'}}{'id'} > 0){
		$accounts{'id'} = $accountinfo{$ref->{'dst'}}{'id'};
		$accounts{'groupid'} = $accountinfo{$ref->{'dst'}}{'groupid'};
	}elsif($accountinfo{$srcchan}{'id'} > 0){
		$accounts{'id'} = $accountinfo{$srcchan}{'id'};
		$accounts{'groupid'} = $accountinfo{$srcchan}{'groupid'};
	}elsif($accountinfo{$dstchan}{'id'} > 0){
		$accounts{'id'} = $accountinfo{$dstchan}{'id'};
		$accounts{'groupid'} = $accountinfo{$dstchan}{'groupid'};
	}

	#print Dumper(\%accounts);
	#next;
	my $children = '';
	$query = "SELECT * FROM mycdr WHERE id > $ref->{'id'} AND (channel='$ref->{'channel'}' OR channel='$ref->{'dstchannel'}' OR dstchannel='$ref->{'channel'}' OR dstchannel='$ref->{'dstchannel'}') ORDER BY calldate ASC ";
	my $clild_rows = &executeQuery($query,'rows');
	while ( my $clild_ref = $clild_rows->fetchrow_hashref() ) {
		$cdrprocessed{$clild_ref->{'id'}} = $clild_ref->{'id'};
		$children .= "$clild_ref->{'id'},";
		$query = "UPDATE mycdr set ischild = 'yes', processed='1',accountid='$accounts{'id'}',astercrm_groupid='$accounts{'groupid'}' WHERE id='$clild_ref->{'id'}'";
		&executeQuery($query,'');
	}

	$query = "UPDATE mycdr set children = '$children', processed='1',accountid='$accounts{'id'}',astercrm_groupid='$accounts{'groupid'}' WHERE id='$ref->{'id'}'";
	&executeQuery($query,'');
}

my %campaigndata;
my $query = "SELECT * FROM campaigndialedlist WHERE processed = 'no' ORDER BY dialedtime ASC ";
my $rows = &executeQuery($query,'rows');
while ( my $ref = $rows->fetchrow_hashref() ) {
	%campaigndata->{$ref->{'campaignid'}}{'billsec'} += $ref->{'billsec'};
	%campaigndata->{$ref->{'campaignid'}}{'billsec_leg_a'} += $ref->{'billsec_leg_a'};
	if($ref->{'billsec'} > 0){
		%campaigndata->{$ref->{'campaignid'}}{'duration_answered'} += $ref->{'duration'};
		%campaigndata->{$ref->{'campaignid'}}{'answered'} += 1;
	}else{
		%campaigndata->{$ref->{'campaignid'}}{'duration_noanswer'} += $ref->{'duration'};
	}
	%campaigndata->{$ref->{'campaignid'}}{'dialed'} += 1;

	$query = "UPDATE campaigndialedlist SET processed='yes' WHERE id='$ref->{'id'}' ";
	&executeQuery($query,'');
}

foreach my $curcampaignid (sort keys %campaigndata) {
	if($curcampaignid > 0){
		my $curdata = %campaigndata->{$curcampaignid};

		my $query = "UPDATE campaign SET billsec = billsec + '$curdata->{'billsec'}' ,billsec_leg_a = billsec_leg_a + '$curdata->{'billsec_leg_a'}' ,duration_answered = duration_answered + '$curdata->{'duration_answered'}', duration_noanswer = duration_noanswer + '$curdata->{'duration_noanswer'}', answered = answered + '$curdata->{'answered'}', dialed = dialed + '$curdata->{'dialed'}' WHERE id='$curcampaignid'";
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