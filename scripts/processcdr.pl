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

my $pidFile = "/var/run/processcdr.pid";

$| =1 ;

if ($ARGV[0] eq '-v'){		# print version
	print "processcdr version 0.011-100928\n";
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
			print "processcdr process: $line is killed. \n";
		}else{
			print "$res \n";
			print "cant kill processcdr process. \n";
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
			print "processcdr status: [start]\n";
		}else{
			print "processcdr status: [stop]\n";
		}
    }else{
		print "cant find $pidFile, processcdr may not start \n";
	}
	exit;
}elsif  ($ARGV[0] eq '-h'){
	print "********* processcdr parameters *********\n";
	print "    -h show help message\n";
	#print "    -i parse all queue logs in the log file\n";
	print "    -d start as a daemon\n";
	print "    -s show processcdr status\n";
	print "    -k stop processcdr\n";
	print "    -v show processcdr version \n";
	exit;
}


if (-e $pidFile){
    if (open(MYFILE, $pidFile)) {
		my $line = <MYFILE>;
		my $res;
		my $res = `ps  --pid=$line 2>&1`; 
		if ($res =~ /\n(.*)\n/) {
			print "processcdr daemon is still running. Please stop first.\n"; #If no please del $pidFile \n";
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
my $query = "SELECT * FROM mycdr WHERE processed = '0' AND calldate > (now()-INTERVAL 3000 SECOND)  ORDER BY calldate ASC ";

my $query = "SELECT * FROM mycdr WHERE processed = '0'  ORDER BY calldate ASC ";
my $rows = &executeQuery($query,'rows');

while ( my $ref = $rows->fetchrow_hashref() ) {
	#print Dumper $ref;next;
	if($cdrprocessed{$ref->{'id'}} > 0){
		next;
	}

	my $pflag = 0;
	#检查当前有没有和本条cdr相关的且没有结束的通话
	$query = "SELECT * FROM curcdr WHERE srcchan='$ref->{'channel'}' OR srcchan='$ref->{'dstchannel'}'  OR dstchan='$ref->{'channel'}' OR dstchan='$ref->{'dstchannel'}' ";
	my $curcdr_rows = &executeQuery($query,'rows');
	if(my $curcdr_ref = $curcdr_rows->fetchrow_hashref()){
		$pflag = 1;
	}

	my %droprecords;
	my $children = '';
	my $relate_count = 0;
	my %relates;
	my %childrens;
	my $answerflag = 0;

	$query = "SELECT * FROM mycdr WHERE (((channel='$ref->{'channel'}' OR channel='$ref->{'dstchannel'}') AND channel != '') OR ((dstchannel='$ref->{'channel'}' OR dstchannel='$ref->{'dstchannel'}') AND dstchannel != '')) ORDER BY calldate ASC ";
	my $relate_rows = &executeQuery($query,'rows');


	while ( my $relate_ref = $relate_rows->fetchrow_hashref() ) {
		$relate_count++;
		
		$cdrprocessed{$relate_ref->{'id'}} = $relate_ref->{'id'};
		if($relate_ref->{'billsec'} > 0 && !$answerflag){
			$query = "SELECT * FROM mycdr WHERE id > $relate_ref->{'id'} AND (channel='$relate_ref->{'channel'}' OR channel='$relate_ref->{'dstchannel'}' OR dstchannel='$relate_ref->{'channel'}' OR dstchannel='$relate_ref->{'dstchannel'}') ORDER BY calldate ASC ";
			my $clild_rows = &executeQuery($query,'rows');
			while ( my $clild_ref = $clild_rows->fetchrow_hashref() ) {
				$cdrprocessed{$clild_ref->{'id'}} = $clild_ref->{'id'};
				if($clild_ref->{'billsec'} == 0 && $clild_ref->{'queue'} eq $relate_ref->{'queue'}){
					next;
				}
				$childrens{$clild_ref->{'id'}} = $clild_ref;				
			}
			$childrens{'main'} = $relate_ref;
			$answerflag = 1;
		}else{
			$relates{$relate_ref->{'id'}} = $relate_ref;
		}
		
#		print Dumper $clild_ref;next;
#		$cdrprocessed{$clild_ref->{'id'}} = $clild_ref->{'id'};
#		$children .= "$clild_ref->{'id'},";
#		$query = "UPDATE mycdr set ischild = 'yes', processed='1',accountid='$accounts{'id'}',astercrm_groupid='$accounts{'groupid'}' WHERE id='$clild_ref->{'id'}'";
#		&executeQuery($query,'');
	}

	#if($relate_count > 1){
		my %mainaccounts;
		$mainaccounts{'id'} = 0;
		$mainaccounts{'groupid'} = 0;
		if(exists $childrens{'main'}){
			my @chan_tmp = split(/\-/,$childrens{'main'}->{'channel'});
			my $srcchan = $chan_tmp['0'];
			my @dstchan_tmp = split(/\-/,$childrens{'main'}->{'dstchannel'});
			my $dstchan = $dstchan_tmp['0'];

			if($accountinfo{$dstchan}{'id'} > 0){
				$mainaccounts{'id'} = $accountinfo{$dstchan}{'id'};
				$mainaccounts{'groupid'} = $accountinfo{$dstchan}{'groupid'};
			}elsif($accountinfo{$srcchan}{'id'} > 0){
				$mainaccounts{'id'} = $accountinfo{$srcchan}{'id'};
				$mainaccounts{'groupid'} = $accountinfo{$srcchan}{'groupid'};
			}elsif($accountinfo{$childrens{'main'}->{'dst'}}{'id'} > 0){
				$mainaccounts{'id'} = $accountinfo{$childrens{'main'}->{'dst'}}{'id'};
				$mainaccounts{'groupid'} = $accountinfo{$childrens{'main'}->{'dst'}}{'groupid'};
			}elsif($accountinfo{$childrens{'main'}->{'src'}}{'id'} > 0 ){
				$mainaccounts{'id'} = $accountinfo{$childrens{'main'}->{'src'}}{'id'};
				$mainaccounts{'groupid'} = $accountinfo{$childrens{'main'}->{'src'}}{'groupid'};
			}
		}

		foreach my $curid (sort keys %childrens) {
			if($curid eq 'main'){
				next;
			}
			$children .= "$curid,";	
			$query = "UPDATE mycdr set ischild = 'yes', processed='1',accountid='$mainaccounts{'id'}',astercrm_groupid='$mainaccounts{'groupid'}' WHERE id='$curid'";
			#print $query."\n";
			&executeQuery($query,'');
		}

		if(exists $childrens{'main'}){
			$query = "UPDATE mycdr set children = '$children', processed='1',accountid='$mainaccounts{'id'}',astercrm_groupid='$mainaccounts{'groupid'}' WHERE id='$childrens{'main'}->{'id'}'";
			#print $query."\n";
			&executeQuery($query,'');
		}
		
		foreach my $curid (sort keys %relates) {
			if(exists $childrens{$curid}){
				next;		
			}

			if((($relates{$curid}->{'dstchannel'} eq '' || $relates{$curid}->{'channel'} =~ /^local\//) && $relate_count > 1) || ($relates{$curid}->{'dstchannel'} eq '' && $relates{$curid}->{'channel'} =~ /^local\// && $relate_count == 1)){
				$droprecords{$curid} = $relates{$curid};
				next;
			}

			my @chan_tmp = split(/\-/,$relates{$curid}->{'channel'});
			my $srcchan = $chan_tmp['0'];
			my @dstchan_tmp = split(/\-/,$relates{$curid}->{'dstchannel'});
			my $dstchan = $dstchan_tmp['0'];

			my $accountid = 0;
			my $astercrm_groupid = 0;

			if($accountinfo{$dstchan}{'id'} > 0){
				$accountid = $accountinfo{$dstchan}{'id'};
				$astercrm_groupid = $accountinfo{$dstchan}{'groupid'};
			}elsif($accountinfo{$srcchan}{'id'} > 0){
				$accountid = $accountinfo{$srcchan}{'id'};
				$astercrm_groupid = $accountinfo{$srcchan}{'groupid'};
			}elsif($accountinfo{$relates{$curid}->{'dst'}}{'id'} > 0){
				$accountid = $accountinfo{$relates{$curid}->{'dst'}}{'id'};
				$astercrm_groupid = $accountinfo{$relates{$curid}->{'dst'}}{'groupid'};
			}elsif($accountinfo{$relates{$curid}->{'src'}}{'id'} > 0 ){
				$accountid = $accountinfo{$relates{$curid}->{'src'}}{'id'};
				$astercrm_groupid = $accountinfo{$relates{$curid}->{'src'}}{'groupid'};
			}
			
			$query = "UPDATE mycdr set processed='1',accountid='$accountid',astercrm_groupid='$astercrm_groupid' WHERE id='$curid'";
			#print $query."\n";
			&executeQuery($query,'');
		}

		foreach my $curid (sort keys %droprecords) {
			$query = "UPDATE mycdr set processed='-1' WHERE id='$curid'";
			#print $query."\n";
			&executeQuery($query,'');
		}

		print "curidchlid:$children\n";
	#}
	#print Dumper \%relates;
	#print Dumper \%childrens;
	#print Dumper \%droprecords;
	#exit;
	
}exit;

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
			open (HDW,">>$Bin/processcdrlog.txt");
			print HDW $time," ",$message,"\n";
			close HDW;
		}else{
			print $time," ",$message,"\n";
		}
	}
}