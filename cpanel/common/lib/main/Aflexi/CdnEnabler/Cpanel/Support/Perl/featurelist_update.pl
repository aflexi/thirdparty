#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Features;
use Cpanel::Lite::CpanelExtUtils;

my $m;
my %inputs;
my $feature_list_name;
my %feature_list;
my $use_merge;

$m = new main();

%inputs = %{ $m->{inputs} };

$feature_list_name = $inputs{feature_list_name};
%feature_list = %{ $inputs{feature_list} };
# If $use_merge is enabled, we will treat the given $feature_list as change set
# than to replace its entire value (which originally yields 0 for undefined 
# values in $feature_list)
#
#$use_merge = exists($inputs{use_merge}) ? $inputs{use_merge} : 0;

#if($use_merge){
#    my %orig_feature_list = Cpanel::Lite::CpanelExtUtils::get_feature_list($feature_list_name);
#    while(my ($k, $v) = each(%feature_list)){
#        $orig_feature_list{$k} = $v;
#    }
#    %feature_list = %orig_feature_list;
#}
#Cpanel::Features::save_featurelist($feature_list_name, \%feature_list);
my ($feature, $enabled) = each(%feature_list);
Cpanel::Features::modify_feature('feature' => $feature, 'value' => $enabled, 'list' => $feature_list_name ); #, 'verbose' => 1);



# JSON
# ------------------------------------------------------------------------------
$m->write({
    result => 1
});

__END__

