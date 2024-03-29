<?php

declare(strict_types=1);

return [
    'log_forbidden_403'                       => 'Insufficient privileges alert! Logged user attempted to access a resource for which they do not have access.',
    'log_forbidden_known_action_403'          => 'Insufficient privileges alert! Logged user attempted to %s',
    'log_known_user_profile_update_success'   => 'User %s successfully updated their profile.',
    'log_known_user_profile_update_failure'   => 'User %s profile update failed.',
    'log_login_success'                       => 'Successful log in.',
    'log_login_failure'                       => 'Failed login attempt.',
    'log_logout_success'                      => 'User %s successfully logged out.',
    'log_logout_failure'                      => 'System failed to log the user out.',
    'log_logout_non_loggedin_attempt'         => 'Attempt to log out when not logged in.',
    'log_account_activation_success'          => 'Successful account activation.',
    'log_account_activation_failure'          => 'Failed account activation!',
    'log_account_staff_activation_success'    => 'Logged staff member successfully activated %s account.',
    'log_account_staff_activation_failure'    => 'Failed to activate %s account.',
    'log_account_staff_deactivation_success'  => 'Logged staff member successfully deactivated %s account.',
    'log_account_staff_deactivation_failure'  => 'Logged staff member failed to deactivate %s account.',
    'log_account_deletion_success'            => '%s\'s account was successfully deleted.',
    'log_account_deletion_failure'            => 'Failed account deletion!',
    'log_account_update_success'              => 'Account update successful.',
    'log_account_update_failure'              => 'Account update failed.',
    'log_password_update_request'             => 'User requested a password reset.',
    'log_password_update_email_success'       => 'Password reset email sent.',
    'log_password_update_reset_email_failure' => 'Failed to send password reset email.',
    'log_password_update_success'             => 'Password reset successful.',
    'log_password_update_failure'             => 'Password reset failed.',
    'log_profile_update_success'              => 'Profile update successful.',
    'log_profile_social_update_success'       => 'Profile social media update successful.',
    'log_profile_social_update_failure'       => 'Profile social media update failed.',
    'log_profile_update_failure'              => 'Profile update failure!',
    'log_profile_not_found_404'               => 'The requested Profile could not be found!',
    'log_exception_user_not_found'            => 'The queried account could not be found.',
    'log_known_user_not_found'                => 'User with userName: %s could not be found.',
];
