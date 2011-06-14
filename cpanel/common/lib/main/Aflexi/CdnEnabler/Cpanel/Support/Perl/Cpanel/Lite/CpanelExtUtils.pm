#!/usr/local/bin/perl

# Reusable functions, improved on top of existing cPanel codebase.
# 
# Documentation at the __END__

package Cpanel::Lite::CpanelExtUtils;

use Cpanel::Features;

sub get_dir_cpanel_home{
    if(exists($ENV{'CPANEL_HOME'}) && $ENV{'CPANEL_HOME'}){
        return $ENV{'CPANEL_HOME'};
    } else{
        return '/usr/local/cpanel';
    }
}

sub get_dir_cpanel_data{
    if(exists($ENV{'CPANEL_DATA'}) && $ENV{'CPANEL_DATA'}){
        return $ENV{'CPANEL_DATA'};
    } else{
        return '/var/cpanel';
    }
}

sub get_feature_list{

    my $feature_list_name;
    
    my %feature_list;
    # The 'disabled' feature-set, used in evaluating default behaviour later.
    my %feature_list_disabled;
    # The 'default' feature-set, ditto.
    my %feature_list_default;
    
    $feature_list_name = shift @_;
    
    %feature_list = Cpanel::Features::load_featurelist($feature_list_name);
    %feature_list_disabled = Cpanel::Features::load_featurelist('disabled');
    %feature_list_default = Cpanel::Features::load_featurelist('default');
    
    # Here, we for loop the features, and process the hash.
    foreach(Cpanel::Features::load_feature_names()){

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
    
    return %feature_list;
}

sub get_user_names{

    my @user_names = ();

    opendir(my $dh, get_dir_cpanel_data().'/users');
    foreach (sort(readdir($dh))){
        next if(-d || $_ =~ /^[.]/);
        push(@user_names, $_)
    } 
    closedir($dh);
    
    return @user_names;
}

1;

__END__

=head1 NAME

Reusable functions, improved on top of existing cPanel codebase.

=head1 METHODS

=item Cpanel::Lite::CpanelExtUtils::get_feature_list($feature_list_name)

Given the name of a feature set (officially called feature list), evaluate over 
'disabled' and 'default' feature lists then return it. 

This is different with Cpanel::Features::load_featurelist that doesn't perform 
the said evaluation and feature values are 0 (1 is correct!) by default if not
defined in the file.

=item Cpanel::Lite::CpanelExtUtils::get_user_names()

Get the user names.

=head1 AUTHOR

Written and maintained by Yuen-Chi Lian <yc@aflexi.net>.

=head1 VERSION

Version 2.5 (2010-06-11), since 2.5.

=head1 COPYRIGHT AND LICENSE

Copyright (c) 2010 Aflexi Sdn. Bhd. All rights reserved.

This program is free software. Refer to the LICENSE.txt shipped with it for
further legal (technical) details.

