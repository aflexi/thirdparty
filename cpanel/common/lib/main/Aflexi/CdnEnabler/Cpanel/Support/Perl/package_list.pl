#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Whostmgr::Packages;

my $m;
my %packages;
my %feature_descriptions;
my %feature_lists;
my %feature_list_disabled;
my %feature_list_default;
my @feature_names;

$m = new main();

# All packages, a hash reference.
# ------------------------------------------------------------------------------
%packages = %{ Whostmgr::Packages::fetch_package_list('all') };
$packages{'default'} = { 'FEATURELIST' => 'default' };

# Feature Descriptions, a hash reference.
# ------------------------------------------------------------------------------
%feature_descriptions = ();


foreach(Cpanel::Features::load_addon_feature_descs()){
    # Each element is an array reference. So we have to de-reference it.
    $feature_descriptions{@$_[0]} = @$_[1];
}

foreach(Cpanel::Features::load_feature_descs()){
    # Each element is an array reference. So we have to de-reference it.
    $feature_descriptions{@$_[0]} = @$_[1];
}
# Feature Settings.
# ------------------------------------------------------------------------------
# A feature set contains a set of features.
%feature_lists = ();
# A feature name is the unique name of the feature, e.g. 'cdn'.
@feature_names = keys(%feature_descriptions);
# The 'disabled' feature-set, used in evaluating default behaviour later.
%feature_list_disabled = Cpanel::Features::load_featurelist('disabled');
# The 'default' feature-set, ditto.
%feature_list_default = Cpanel::Features::load_featurelist('default');

# TODO [yclian 20100613] Shall rename it to package_get.pl and support both get 
# and list. Refer to user_get.pl.

foreach(Cpanel::Features::get_feature_lists()){

    my $feature_list_name;
    my %feature_list;
    
    $feature_list_name = $_;
    
    # Not returning the 'disabled' feature-set.
    if($feature_list_name eq 'disabled'){
        next;
    }
    
    # This hash is feature_name => 0 or 1
    %feature_list = Cpanel::Features::load_featurelist($feature_list_name);
        
    # Here, we for loop the features, and process the hash.
    foreach(@feature_names){
    
        my $feature_name = $_;
        
        # If the feature is disabled in the 'disabled' feature-set, it's defi-
        # nitely disabled.
        if(exists($feature_list_disabled{$feature_name}) && !$feature_list_disabled{$feature_name}){
            $feature_list{$feature_name} = 0;
            next;
        }
        
        # Filling up the features disabled by 'default'. Only if:
        #  - The feature doesn't exist in the feature-set yet. (Means it *can* be enabled)
        #  - The feature exists and is disabled in 'default'.
        if($feature_list_name ne 'default'){
            if( !exists($feature_list{$feature_name}) && 
                (exists($feature_list_default{$feature_name}) && !$feature_list_default{$feature_name})
              ){
                $feature_list{$feature_name} = 0;
                next;
            }
        }
        
        # Otherwise, as the feature is STILL not in the hash, is considered 
        # enabled.
        unless(exists($feature_list{$feature_name})){
            $feature_list{$feature_name} = 1;
        }
    }
    
    $feature_lists{$feature_list_name} = \%feature_list;
}

# JSON
# ------------------------------------------------------------------------------
$m->write({
    packages => \%packages,
    feature_descriptions => \%feature_descriptions,
    feature_lists => \%feature_lists
});

__END__

