#!d:\apps\perl\bin\perl.exe -w

# WARNING:  This script will not work on a Unix platform

use strict;


# Set default values for customizable settings
my $agents_ini = "d:\\apps\\bmc\\pconfmgr\\ini\\agents.ini";
my $sql_script_override = "";
#my $maxchildren = 10;
my $maxchildren = 5;
my $numagents = 0;
my $get_pconfigs = 0;
my $generate_sql = 0;
my $push_sql = 0;
my $show_agent_list = 0;
my $debug = 0;
my $leave_temp_files = 0;
my $leave_old_rows = 0;
my $show_runtime = 0;
#my $DB_TYPE = "ORACLE";
my $DB_TYPE = "MYSQL";


# Initialize global variables
my $starttime = time();
my (@PROCESSLIST,@SQLSCRIPTS);



############################################################
# sub Usage
#
# Description:  Displays usage instructions with definitions
# of command line options

sub Usage () {
    print <<EOF;


gather_pconfig.pl                                  Copyright BMC Software 2004

This utility will gather pconfig data from all hosts defined in
the agents.ini file created by PATROL Configuration Manager.
The data will then optionally be inserted into an oracle table.

Command line arguments:

    +get                Gather pconfig data from all agents and save in .cfg
                        files

    -gensql             Generate SQL script based on .cfg files in the current
                        directory

    +push <cfg_file>    Connect to the database and run the SQL script to
                        insert pconfig data (Default = all scripts)

    +pushsql <script>   Connect to the database and run the SQL script to
                        insert pconfig data (Default = all scripts)

    +all                Gather pconfig data, create the SQL script, connect to
                        the database, and run the script.

    -listagents         Show the list of agents in the agents.ini file

    -nocleanup          Do not remove temporary files

    -nocleandb          Do not remove old database entries

    -ini <ini_file>     Override default path to agents.ini
                        (Default = $agents_ini)

    -maxthreads <n>     Set the maximum number of threads for parallel
                        processing (Default = $maxchildren)

    -debug [n]          Enable debug output & set debug level (Default = 0)

    -showruntime        Display the total execution time in seconds

    -h or -help         Display this screen



EOF
    exit;
}


############################################################
# sub get_agent_list
#
# Description:  Parses the agents.ini file for list of hosts

sub get_agent_list () {
    # Ini file currently looks like this

    # port=3181
    # protocol=TCP
    # [AUSBMCPCM01_US_W2K3_BMCPCM]
    # parent=MANAGED_SERVERS.AMERICAS.WINDOWS.BMC_INFRASTRUCTURE_SERVERS
    # host=ausbmcpco01
    # [AUSBMCRT04_US_LX_BMCRT]
    # parent=MANAGED_SERVERS.AMERICAS.UNIX_AND_LINUX.BMC_INFRASTRUCTURE_SERVERS
    # host=AUSBMCRT04
    # [AUSCORPTAX01_US_W2K_SQL]
    # parent=MANAGED_SERVERS.AMERICAS.NOTIFICATION_SERVER_POINTERS.NS1
    # parent=MANAGED_SERVERS.AMERICAS.RT_SERVER_POINTERS.AUSBMCRT02
    # parent=MANAGED_SERVERS.AMERICAS.WINDOWS.SQL
    # host=AUSCORPTAX01

    my (@AGENTS,$buf);
    my $j = 0;
    my (@HOST,@PORT);
    my @HOST_PORT;

    my $mode = @_[0];

    if ($mode == "push") {
        

        opendir(DIR, ".");
        @files = grep(/\.cfg$/,readdir(DIR));
        closedir(DIR);
        
        foreach $file (@files) {

            $file =~ /(.*)-([0-9]*).cfg/;
            my $hostname = $1;
            my $portnum = $2;
            
            push @HOST_PORT, [$host, $port];
            
            
        } # foreach $file (@files)

    } # if ($mode == "push")

    if ($mode == "pull") {

        open (INIFILE, "<$agents_ini") or die "Unable to read ini file $agents_ini";

        # Read the entire file into memory
        while (<INIFILE>) {
            $buf .= $_;
        }

        close (INIFILE);

        $_ = $buf;

        # Strip out newlines
        s/\n/ /g;

        # Put back some of the newlines
        s/ \[/\n\[/g;

        $buf = $_;
        @AGENTS = split (/\n/,$buf);

        # Ignore non-production servers, infrastructure servers, and non-AMERICAS regions
        @AGENTS = grep (/MANAGED_SERVERS.AMERICAS/,@AGENTS);

        # Buffer now looks like this:

        # port=3181 protocol=TCP
        # [AUSBMCPCM01_US_W2K3_BMCPCM] parent=MANAGED_SERVERS.AMERICAS.WINDOWS.BMC_INFRASTRUCTURE_SERVERS host=ausbmcpco01
        # [AUSBMCPCO01_US_W2K3_BMC] parent=MANAGED_SERVERS.AMERICAS.WINDOWS.BMC_INFRASTRUCTURE_SERVERS host=AUSBMCPCO01_US_W2K3_BMC
        # [AUSBMCRT01_US_LX_BMCRT] parent=MANAGED_SERVERS.AMERICAS.UNIX_AND_LINUX.BMC_INFRASTRUCTURE_SERVERS host=ausbmcrt01
        # [AUSBMCRT02_US_LX_BMCRT] parent=MANAGED_SERVERS.AMERICAS.UNIX_AND_LINUX.BMC_INFRASTRUCTURE_SERVERS host=ausbmcrt02
        # [AUSBMCRT03_US_LX_BMCRT] parent=MANAGED_SERVERS.AMERICAS.UNIX_AND_LINUX.BMC_INFRASTRUCTURE_SERVERS host=AUSBMCRT03
        # [AUSBMCRT04_US_LX_BMCRT] parent=MANAGED_SERVERS.AMERICAS.UNIX_AND_LINUX.BMC_INFRASTRUCTURE_SERVERS host=AUSBMCRT04
        # [AUSCORPTAX01_US_W2K_SQL] parent=MANAGED_SERVERS.AMERICAS.NOTIFICATION_SERVER_POINTERS.NS1 parent=MANAGED_SERVERS.AMERICAS.RT_SERVER_POINTERS.AUSBMCRT02 parent=MANAGED_SERVERS.AMERICAS.WINDOWS.SQL host=AUSCORPTAX01

        # Top line contains the default port and protocol, so we'll shift it off
        # No longer necessary because of the grep for AMERICAS agents
        #shift @AGENTS;

        /port=([0-9]*)/;
        my $defaultPort = $1;

        $debug && print "Default port: $defaultPort\n";

        foreach (@AGENTS) {
            my ($host,$port,$display_name) = "";

            # Parse out the hostname
            /host=([a-zA-Z0-9_.-]*)/;
            $host = $1;

            # Convert to uppercase
            $host = uc $host;

            # Parse out the port (if it exists)
            if (/port=([0-9]*)/) {
                $port = $1;
            } else {
                $port = $defaultPort;
            }

            # Parse out the display name
            /\[([a-zA-Z0-9_.-]*)\]/;
            $display_name = $1;

            # remove FQDN
            $host =~ s/\..*//;

            if ($debug) {
                print "Host = $host\tPort = $port\n";
            }

            push @HOST_PORT, [$host, $port];
        }
    } # end if ($mode == "pull")


    my %count;

    # sort the list
    @HOST_PORT = sort {$a->[0] cmp $b->[0]} @HOST_PORT;

    # Unique hosts only
    @HOST_PORT = grep { ++$count{$_} < 2 } @HOST_PORT;

    $numagents = scalar (@HOST_PORT);

    # Print out the list of hosts, if requested
    if ($show_agent_list) {
        my $maxidx = $numagents;
        my $j = 0;
        print <<EOF;
 Hostname Port
 -------- ----
EOF
        for ($j=0;$j < $maxidx;$j++) {
            print " $HOST_PORT[$j][0] $HOST_PORT[$j][1]\n";
        }
    }

    return @HOST_PORT;
}

############################################################
# sub pull_pconfig_data
#
# Description:  Connects to each agent in the list and pulls
# down the pconfig data, saving it in a temp file.

sub pull_pconfig_data {
    use Win32::Process;
    use Win32;
    sub ErrorReport{
        print Win32::FormatMessage( Win32::GetLastError() );
    }

    my (@cmdlist,$ProcessObj);
    #my $stopwatch = time();
    my $j = 0;
    my @HOST_PORT = @_;
    my $numagents = scalar (@HOST_PORT)/2;
    my $systemRoot = $ENV{SystemRoot};
    my $patrolHome = $ENV{PATROL_HOME};
    my $pconfigBinary = "$patrolHome\\bin\\pconfig.exe";
    my $cmdExeBinary = "$systemRoot\\system32\\cmd.exe";

    if ($debug>1) {
        print "DEBUG: pull_pconfig_data(): First argument = >$_[0]<\n";
        print "DEBUG: pull_pconfig_data(): Second argument = >$_[1]<\n";
        print "DEBUG: pull_pconfig_data(): Number of agents to process = >$numagents<\n";
        print "DEBUG: pull_pconfig_data(): Start time = >$starttime<\n";
    }

    for ($j=0;$j < $numagents;$j++) {
        my $hostname = $HOST_PORT[0];
        my $port = $HOST_PORT[1];
        my $cmd = "$pconfigBinary +get -host $hostname -p $port > ${hostname}-${port}-${starttime}.cfg";
        #if ($debug) { print "DEBUG: pull_pconfig_data(): Adding command to get pconfig for $hostname port $port\n"; }
        #push @cmdlist, "$pconfigBinary +get -host $hostname -p $port > ${hostname}-${port}-${starttime}.cfg";

        if ($debug) { print "Pulling pconfig data for $hostname.\n"; }

        Win32::Process::Create($ProcessObj,
                                    $cmdExeBinary,
                                    "cmd /c \"$cmd\"",
                                    0,
                                    NORMAL_PRIORITY_CLASS,
                                    ".")|| die ErrorReport();
        $PROCESSLIST[$j] = $ProcessObj;
    }
#    foreach (@cmdlist) {
#       if ($debug) { print "Executing $_\n";}
#
#       `$_`;
#       sleep 1;
#    }
#    $debug=1;
#    if ($debug) {
#       print "Completed gathering pconfig for $hostname in ".int (time() - $starttime)." seconds.\n";
#    }


    if ($debug) { print "Waiting for pconfig to complete\n"; }
    my $start = time();
    for (@PROCESSLIST) {
        $_->Wait(121000);
        $_->Kill(1);
    }
    my $finished = time() - $start;
    if ($debug) { print "Finished after waiting $finished seconds.\n";}
}

############################################################
# sub push_pconfig_data
#
# Description:  Connects to each agent in the list and pushes
# the pconfig data from a temp file.

sub push_pconfig_data {
    use Win32::Process;
    use Win32;
    sub ErrorReport{
        print Win32::FormatMessage( Win32::GetLastError() );
    }

    my (@cmdlist,$ProcessObj);
    #my $stopwatch = time();
    my $j = 0;
    my @HOST_PORT = @_;
    my $numagents = scalar (@HOST_PORT)/2;
    my $systemRoot = $ENV{SystemRoot};
    my $patrolHome = $ENV{PATROL_HOME};
    my $pconfigBinary = "$patrolHome\\bin\\pconfig.exe";
    my $cmdExeBinary = "$systemRoot\\system32\\cmd.exe";

    if ($debug>1) {
        print "DEBUG: pull_pconfig_data(): First argument = >$_[0]<\n";
        print "DEBUG: pull_pconfig_data(): Second argument = >$_[1]<\n";
        print "DEBUG: pull_pconfig_data(): Number of agents to process = >$numagents<\n";
        print "DEBUG: pull_pconfig_data(): Start time = >$starttime<\n";
    }

    for ($j=0;$j < $numagents;$j++) {
        my $hostname = $HOST_PORT[0];
        my $port = $HOST_PORT[1];
        my $cmd = "$pconfigBinary -host $hostname -p $port ${hostname}-${port}.cfg";
        #if ($debug) { print "DEBUG: push_pconfig_data(): Adding command to get pconfig for $hostname port $port\n"; }
        #push @cmdlist, "$cmd";

        if ($debug) { print "Pushing pconfig data for $hostname.\n"; }

        Win32::Process::Create($ProcessObj,
                                    $cmdExeBinary,
                                    "cmd /c \"$cmd\"",
                                    0,
                                    NORMAL_PRIORITY_CLASS,
                                    ".")|| die ErrorReport();
        $PROCESSLIST[$j] = $ProcessObj;
    }
#    foreach (@cmdlist) {
#       if ($debug) { print "Executing $_\n";}
#
#       `$_`;
#       sleep 1;
#    }
#    $debug=1;
#    if ($debug) {
#       print "Completed gathering pconfig for $hostname in ".int (time() - $starttime)." seconds.\n";
#    }


    if ($debug) { print "Waiting for pconfig to complete\n"; }
    my $start = time();
    for (@PROCESSLIST) {
        $_->Wait(121000);
        $_->Kill(1);
    }
    my $finished = time() - $start;
    if ($debug) { print "Finished after waiting $finished seconds.\n";}
}

############################################################
# sub create_sql_input_file
#
# Description:  Parses all the pconfig temp files generated
# by pull_pconfig_data(), and reformats them as SQL files
# ready to be pushed into an oracle table.

sub create_sql_input_file () {
    ###
    ###  Create SQL input file
    ###

    my $file;
    my @files;
    my @SQL;
    #my $rndstr = int (rand(time()));
    #my $sql_script = "pconfig_data_${rndstr}.sql";\
    #my $debug = 1;

    opendir(DIR, ".");
    @files = grep(/\.cfg$/,readdir(DIR));
    closedir(DIR);

    #print "Files: >$files[0]<\n";

    my $k = 0;

    foreach $file (@files) {
        open (INPUT, "<$file") or die "Unable to read config file $file.";
        my @CONTENTS = <INPUT>;
        close INPUT;

        $file =~ /(.*)-([0-9]*)-([0-9]*).cfg/;
        my $hostname = $1;
        my $portnum = $2;
        my $timestamp = $3;

        my $sql_script = "${hostname}-${portnum}-${timestamp}.sql";

        my $top_row = shift (@CONTENTS);
        next unless ($top_row =~ /^PATROL_CONFIG$/);

        $_ = join "\n", @CONTENTS;
        # First, convert multi-line values for ease of variable matching
        s/\n\n/\n/g;
        s/\n/-_NEWline-_/g;
        s/},/},\n/g;

        # Next, replace ' marks with '' for SQL compatibility.
        s/\'/\'\'/g;


        @CONTENTS = split /\n/;

        foreach (@CONTENTS) {

            #print "\r$k++";


            # Finally, pull out the variable name and value
            if (/\"(\/.*?)\" = \{ REPLACE = \"(.*?)\" \},*/) {
                if ($debug) {
                    print ("Matched pconfig variable: $1\n");
                    print ("Value:                    $2\n");
                }
            } else {
                if ($debug) {
                    print ("Pconfig variable doesn't match normal pattern: $_\n");
                }
                next;
            }

            my $pconfig_var = $1;
            my $pconfig_value = $2;
            $pconfig_value =~ s/-_NEWline-_/\n/g;


            if ($debug>1) {
                print "Pushing the following settings on to SQL stack:\n";
                print "timestamp: >$timestamp<\nHostname: >$hostname<\nport: >$portnum<\n";
                print "variable: >$pconfig_var<\nvalue: >$pconfig_value<\n";
            }

            # Push the resulting value on to the stack we'll use to generate the sql

            if ($DB_TYPE == "ORACLE") {
                push (@SQL, "insert into actual_config values (".
                          "\'$timestamp\',".
                          "(select sysdate from dual),".
                          "\'$hostname\',".
                          "\'$portnum\',".
                          "\'$pconfig_var\',".
                          "\'$pconfig_value\');");
            } elsif ($DB_TYPE == "MYSQL") {
                push (@SQL, "
                    INSERT INTO actual_config (
                        date_gathered,
                        date_entered,
                        agentid,
                        variable,
                        value)
                    SELECT
                        \'$timestamp\' date_gathered,
                        now() date_entered,
                        agents.id agentid,
                        \'$pconfig_var\' variable,
                        \'$pconfig_value\' value
                    FROM agents
                    WHERE 
                        agents.hostname = \'$hostname\'
                        AND agents.port = \'$portnum\';
                ");
                      
            }
            
            
        }


        unless ($leave_old_rows) {
            if ($DB_TYPE == "ORACLE") {
                push @SQL, "COMMIT;";
                push @SQL, "DELETE FROM actual_config WHERE ".
                           "server_name = \'$hostname\' ".
                           "AND port = \'$portnum\';";
            } elsif ($DB_TYPE == "MYSQL") {
                push @SQL, "
                    DELETE FROM actual_config 
                    WHERE agentid IN (
                            SELECT id agentid 
                            FROM agents
                            WHERE hostname = \'$hostname\'
                            AND port = \'$portnum\')
                    ;                        
                ";
            }
            
        }

        # Write SQL script to disk

        if ($debug) { print "Writing sql script $sql_script\n"; }

        open (SQLSCRIPT, ">$sql_script") or die "Unable to open sql script $sql_script.";
        while ($_ = pop @SQL) {
            print SQLSCRIPT "$_\n";
        }
        if ($DB_TYPE == "ORACLE") {
            print SQLSCRIPT "commit;\nexit;\n";
        } elsif ($DB_TYPE == "MYSQL") {
            print SQLSCRIPT "exit;\n";
        }
        
        close SQLSCRIPT;

        push @SQLSCRIPTS, $sql_script;
    }

    print "\n";
    unless ($leave_temp_files) {
        my $numFilesDeleted = unlink @files;
        if ($numFilesDeleted == scalar @files) {
            if ($debug) { print "Cleanup: Removed all pconfig temp files.\n";}
        } else {
            print "Cleanup: Removed $numFilesDeleted out of ".scalar (@files)." pconfig temp files.  $!\n";
        }
    }
}

############################################################
# sub execute_sql_script
#
# Description:  Connects to Oracle using sqlplus, and
# executes an SQL script, which must be passed as an argument

sub execute_sql_script {

    if ($DB_TYPE == "ORACLE") {
        my $dbname = "dmon1";
        my $usr = "patrol_monitor";
        my $pasw = "m0n1t0r";
        my $cmd = "sqlplus -s ${usr}/${pasw}\@${dbname}";
        my $binary = "sqlplus";
    } elsif ($DB_TYPE == "MYSQL") {
        my $dbname = "patrol_report";
        my $dbserver = "localhost";
        my $usr = "webpatrol";
        my $pasw = "patrol";
        my $cmd = "mysql -u ${usr} -p ${pasw} -host ${dbserver}";
        my $binary = "mysql";
    }

    my $sql_script = $_[0];

    # Execute SQL Script

    if (! -r $sql_script ) { die "Unable to execute SQL script $sql_script: $!"; }


    open  DB, "| $cmd" or die "Can't pipe to $binary : $!" or print "Error: Unable to start $binary for $sql_script:  $!";

    if ($DB_TYPE == "MYSQL") {
        print DB "use ${dbname};\n";
    }

    print DB "\@$sql_script\n";
    close DB;

}


############################################################
# sub main
#
# Description:  Execution starts here

unless ($ARGV[0]) { Usage (); }

# Get command line switches
SWITCH: while ($_ = shift @ARGV) {
    /^\+get$/      && do {
                           $get_pconfigs = 1;
                           next SWITCH;
                      };

    /^\+push$/      && do {
                           $push_pconfigs = 1;
                           $pconfig_override = shift @ARGV;
                           if (! -r $pconfig_override) {
                               print "Unable to read pconfig input file $pconfig_override\n\n";
                               Usage();
                           }
                           next SWITCH;
                       };
    /^-gensql$/      && do {
                           $generate_sql = 1;
                           next SWITCH;
                      };

    /^\+pushsql$/      && do {
                           $push_sql = 1;
                           $sql_script_override = shift @ARGV;
                           if (! -r $sql_script_override) {
                               print "Unable to read sql script $sql_script_override\n\n";
                               Usage();
                           }
                           next SWITCH;
                       };
    /^-ini$/      && do {
                           $agents_ini = shift @ARGV;
                           if (! -r $agents_ini) {
                               print "Unable to read ini file $agents_ini\n\n";
                               Usage();
                           }
                           next SWITCH;
                       };
    /^\+all$/      && do {
                           $get_pconfigs = 1;
                           $generate_sql = 1;
                           $push_sql = 1;
                           #$show_agent_list = 1;

                           next SWITCH;
                       };
    /^-listagents$/      && do {
                           $show_agent_list = 1;
                           next SWITCH;
                       };

    /^-nocleanup$/      && do {
                           $leave_temp_files = 1;
                           next SWITCH;
                       };

    /^-nocleandb$/      && do {
                           $leave_old_rows = 1;
                           next SWITCH;
                       };

    /^-showruntime$/      && do {
                           $show_runtime = 1;
                           next SWITCH;
                       };
    /^-debug$/      && do {
                           my $tmp = shift @ARGV;
                           if ($tmp =~ /^[0-9]*$/) {
                               $debug = $tmp;
                           } else {
                                $debug = 1;
                                unshift @ARGV, $tmp;
                           }
                           next SWITCH;
                       };
    /^-maxthreads$/      && do {
                           $maxchildren = shift @ARGV;
                           unless ($maxchildren =~ /^[0-9]*$/) {
                               print "Invalid value for max threads: >$maxchildren<\n";
                               Usage ();
                           }
                           next SWITCH;
                       };
    /^-h$/       && do {
                           Usage();
                       };

    /^-help$/       && do {
                           Usage();
                       };

    Usage ();
}


# Make sure some action was specified from the command line.
unless ($get_pconfigs or $generate_sql or $push_sql or $show_agent_list or $push_pconfigs) {
    print "Nothing to do.\n\n";
    Usage;
}

# If we're supposed to get pconfigs or just show the
# list of agents, we need to run get_agent_list ()
my @AGENT_LIST = (["",""]);
if ($get_pconfigs || $show_agent_list) {
    @AGENT_LIST = get_agent_list("pull");
} elsif ($push_pconfigs) {
    @AGENT_LIST = get_agent_list("push");
}


# Now, let's connect to each agent with pconfig.  To speed this process up,
# we'll be using multiple pconfig commands, running in parallel.

if ($get_pconfigs || $push_pconfigs) {
    my $numchildren = 0;
    my $numrows = scalar (@AGENT_LIST);
    my $k = 0;
    my @AGENT;
    my $stopwatch = time();

    if ($debug) { print "Processing $numrows agents\n"; }

    for ($k=0; $k<$numrows;$k++) {
        @AGENT = ($AGENT_LIST[$k][0],$AGENT_LIST[$k][1]);

        if ($debug>2) { print "DEBUG: main(): Is $numchildren child procs < max ($maxchildren)?\n"; }

        if ($numchildren < $maxchildren) {
            my $pid = fork();
            die "Unable to fork()" unless defined $pid;
            if ($pid) {
                # parent
                $numchildren++;
                if ($debug>2) { print "Parent: Forked child to handle $AGENT[0].  $numchildren child processes now running.\n";}
            } else {
                #child
                if ($debug>2) { print "Child: Processing >$AGENT[0]<\n"; }
                if ($get_configs) {
                    pull_pconfig_data (@AGENT);
                }
                if ($push_configs) {
                    push_pconfig_data (@AGENT);
                }
                exit;
            }
        } else {
            if ($debug>2) { print "                             Parent: Waiting for next available slot\n"; }
            wait();
            $numchildren--;
            my $pid = fork();
            die "Unable to fork()" unless defined $pid;
            if ($pid) {
                # parent
                if ($debug>2) { print "Parent: Forked child to handle $AGENT[0].\n";}
                $numchildren++;
            } else {
                #child
                if ($debug>2) { print "Child: Processing >$AGENT[0]<\n"; }
                if ($get_configs) {
                    pull_pconfig_data (@AGENT);
                }
                if ($push_configs) {
                    push_pconfig_data (@AGENT);
                }


                exit;
            }

        }
    }

    while ($numchildren > 0) {
            if ($debug>2) { print "                             Parent: Waiting for all children to exit: $numchildren remaining\n";}
            wait();
            $numchildren--;
    }

    my $finished = time() - $stopwatch;
    if ($numchildren == 0) {
        if ($debug>2) { print "All children have exited.  Finished in $finished seconds\n";}
    }
}

# Now, create the SQL input file, if required.
my $sql;
if ($generate_sql) {
    $sql = create_sql_input_file ();
}

# Finally, execute the file against the database.
if ($push_sql) {
    if ("$sql_script_override" ne "") {
        execute_sql_script ($sql_script_override);
    } elsif ("$SQLSCRIPTS[0]" ne "") {
        my $numchildren = 0;

        foreach (@SQLSCRIPTS) {


            if ($debug>2) { print "DEBUG: main(): Is $numchildren child procs < max ($maxchildren)?\n"; }

            if ($numchildren < $maxchildren) {
                my $pid = fork();
                die "Unable to fork()" unless defined $pid;
                if ($pid) {
                    # parent
                    $numchildren++;
                    if ($debug>2) { print "Parent: Forked child to handle $_.  $numchildren child processes now running.\n";}
                } else {
                    #child
                    if ($debug>2) { print "Child: Processing >$_<\n"; }
                        execute_sql_script ($_);


                    exit;
                }
            } else {
                if ($debug>2) { print "                             Parent: Waiting for next available slot\n"; }
                wait();
                $numchildren--;
                my $pid = fork();
                die "Unable to fork()" unless defined $pid;
                if ($pid) {
                    # parent
                    if ($debug>2) { print "Parent: Forked child to handle $_.\n";}
                    $numchildren++;
                } else {
                    #child
                    if ($debug>2) { print "Child: Processing >$_<\n"; }
                    execute_sql_script ($_);

                    exit;
                }
            }
        }

        while ($numchildren > 0) {
                if ($debug>2) { print "                             Parent: Waiting for all children to exit: $numchildren remaining\n";}
                wait();
                $numchildren--;
        }


        unless ($leave_temp_files) {
            my $numFilesDeleted = unlink @SQLSCRIPTS;
            if ($numFilesDeleted == scalar @SQLSCRIPTS) {
                if ($debug) { print "Cleanup: Removed all SQL script files.\n";}
            } else {
                print "Cleanup: Removed $numFilesDeleted out of ".scalar (@SQLSCRIPTS)." SQL script files. $!\n";
            }
        }
    }
}

# Print the total run time for the script, if desired.
if ($show_runtime or $debug) {
    my $runtime = time() - $starttime;
    my $timestamp = localtime();
    print "\n$timestamp - $0 Completed execution in $runtime seconds.\n";
}

exit(0);