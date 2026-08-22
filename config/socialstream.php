<?php

use JoelButcher\Socialstream\Providers;

return [
    'middleware' => ['web'],
    'prompt' => 'Or Login Via',
    // Only expose OAuth providers that actually have credentials configured;
    // unconfigured providers would 500 on /oauth/{provider} redirect.
    'providers' => array_values(array_filter([
        env('BITBUCKET_CLIENT_ID') ? Providers::bitbucket() : null,
        env('FACEBOOK_CLIENT_ID') ? Providers::facebook() : null,
        env('GITHUB_CLIENT_ID') ? Providers::github() : null,
        env('GITLAB_CLIENT_ID') ? Providers::gitlab() : null,
        env('GOOGLE_CLIENT_ID') ? Providers::google() : null,
        env('LINKEDIN_CLIENT_ID') ? Providers::linkedin() : null,
        env('LINKEDIN_OPENID_CLIENT_ID') ? Providers::linkedinOpenId() : null,
        env('SLACK_CLIENT_ID') ? Providers::slack() : null,
        env('TWITTER_CLIENT_ID') ? Providers::twitterOAuth2() : null,
    ])),
    'component' => 'socialstream::components.socialstream',
    'show_provider_ids' => env('SOCIALSTREAM_SHOW_PROVIDER_IDS', false),
    'features' => [
        // \JoelButcher\Socialstream\Features::createAccountOnFirstLogin(),
        // \JoelButcher\Socialstream\Features::generateMissingEmails(),
        // \JoelButcher\Socialstream\Features::loginOnRegistration(),
        // \JoelButcher\Socialstream\Features::rememberSession(),
    ],
];
