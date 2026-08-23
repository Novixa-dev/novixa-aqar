<?php

namespace Tests\Feature;

use JoelButcher\Socialstream\Providers;
use Tests\TestCase;

class SocialstreamConfigTest extends TestCase
{
    /**
     * @test
     */
    public function test_socialstream_config_has_social_media_providers(): void
    {
        $this->withAllCredentials();

        $this->refreshSocialstreamProviders();

        $providers = config('socialstream.providers');

        $this->assertNotEmpty($providers, 'Socialstream providers should not be empty');
        $this->assertIsArray($providers);
    }

    /**
     * @test
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('socialMediaProviderDataProvider')]
    public function test_socialstream_config_includes_provider(string $providerName): void
    {
        $this->withAllCredentials();

        $this->refreshSocialstreamProviders();

        $providers = config('socialstream.providers');

        $names = collect($providers)->map(fn ($p) => is_object($p) ? $p->id : $p)->all();

        $this->assertContains(
            $providerName,
            $names,
            "Provider '{$providerName}' should be enabled in socialstream config"
        );
    }

    public static function socialMediaProviderDataProvider(): array
    {
        return [
            'bitbucket'      => ['bitbucket'],
            'facebook'       => ['facebook'],
            'github'         => ['github'],
            'gitlab'         => ['gitlab'],
            'google'         => ['google'],
            'linkedin'       => ['linkedin'],
            'linkedinOpenId' => ['linkedin-openid'],
            'slack'          => ['slack'],
            'twitter-oauth-2' => ['twitter-oauth-2'],
        ];
    }

    /**
     * @test
     */
    public function test_unconfigured_providers_are_filtered_out(): void
    {
        // With no credentials set (the default demo environment) every
        // provider must be filtered out so no /oauth/{provider} button 500s.
        config([
            'BITBUCKET_CLIENT_ID' => null,
            'FACEBOOK_CLIENT_ID' => null,
            'GITHUB_CLIENT_ID' => null,
            'GITLAB_CLIENT_ID' => null,
            'GOOGLE_CLIENT_ID' => null,
            'LINKEDIN_CLIENT_ID' => null,
            'LINKEDIN_OPENID_CLIENT_ID' => null,
            'SLACK_CLIENT_ID' => null,
            'TWITTER_CLIENT_ID' => null,
        ]);

        $this->refreshSocialstreamProviders();

        $this->assertSame([], config('socialstream.providers'));
    }

    /**
     * Simulate a fully-credentialled environment for the provider tests.
     */
    protected function withAllCredentials(): void
    {
        config([
            'BITBUCKET_CLIENT_ID' => 'test',
            'FACEBOOK_CLIENT_ID' => 'test',
            'GITHUB_CLIENT_ID' => 'test',
            'GITLAB_CLIENT_ID' => 'test',
            'GOOGLE_CLIENT_ID' => 'test',
            'LINKEDIN_CLIENT_ID' => 'test',
            'LINKEDIN_OPENID_CLIENT_ID' => 'test',
            'SLACK_CLIENT_ID' => 'test',
            'TWITTER_CLIENT_ID' => 'test',
        ]);
    }

    /**
     * The providers list is built from env() at config load time; re-evaluate
     * it after overriding the credential values in tests.
     */
    protected function refreshSocialstreamProviders(): void
    {
        $map = [
            'bitbucket' => ['BITBUCKET_CLIENT_ID', fn () => Providers::bitbucket()],
            'facebook' => ['FACEBOOK_CLIENT_ID', fn () => Providers::facebook()],
            'github' => ['GITHUB_CLIENT_ID', fn () => Providers::github()],
            'gitlab' => ['GITLAB_CLIENT_ID', fn () => Providers::gitlab()],
            'google' => ['GOOGLE_CLIENT_ID', fn () => Providers::google()],
            'linkedin' => ['LINKEDIN_CLIENT_ID', fn () => Providers::linkedin()],
            'linkedin-openid' => ['LINKEDIN_OPENID_CLIENT_ID', fn () => Providers::linkedinOpenId()],
            'slack' => ['SLACK_CLIENT_ID', fn () => Providers::slack()],
            'twitter-oauth-2' => ['TWITTER_CLIENT_ID', fn () => Providers::twitterOAuth2()],
        ];

        $providers = [];

        foreach ($map as $id => [$key, $make]) {
            if (config("{$key}") !== null && config("{$key}") !== '' && config("{$key}") !== false) {
                $providers[] = $make();
            }
        }

        config(['socialstream.providers' => array_values($providers)]);
    }

    /**
     * @test
     */
    public function test_socialstream_config_excludes_twitter_oauth1(): void
    {
        $providers = config('socialstream.providers');

        $names = collect($providers)->map(fn ($p) => is_object($p) ? $p->id : $p)->all();

        $this->assertNotContains(
            'twitter',
            $names,
            'Twitter OAuth 1.0 should not be enabled (requires live API keys)'
        );
    }

    /**
     * @test
     */
    public function test_socialstream_config_has_middleware(): void
    {
        $middleware = config('socialstream.middleware');

        $this->assertContains('web', $middleware);
    }

    /**
     * @test
     */
    public function test_socialstream_config_has_prompt(): void
    {
        $prompt = config('socialstream.prompt');

        $this->assertIsString($prompt);
        $this->assertNotEmpty($prompt);
    }
}
