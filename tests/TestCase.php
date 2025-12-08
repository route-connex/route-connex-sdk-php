<?php

namespace RouteConnex\RouteConnexSdkPhp\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RouteConnex\RouteConnexSdkPhp\ApiVersion;
use RouteConnex\RouteConnexSdkPhp\ErClient;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(__DIR__.'/..');
        $dotenv->load();
        $dotenv->required([
            'BASE_URL',
            'TEST_FILE_PATH',
            'TEST_LARGE_FILE_PATH',

            'HPE_CM_CLIENT_ID',
            'HPE_CM_CLIENT_SECRET',
            'HPE_CM_RUN_ID',
            'HPE_CM_RECORD_TYPE',
            'HPE_CM_CONTAINER_ID',
            'HPE_CM_AUTHOR_ID',
            'HPE_CM_AUTHOR_EMAIL',

            'MS_SP_CLIENT_ID',
            'MS_SP_CLIENT_SECRET',
            'MS_SP_RUN_ID',
            'MS_SP_SITE_NAME',
            'MS_SP_CHANNEL_NAME',
        ])->notEmpty();
    }

    protected function makeErClient(ApiVersion $version = ApiVersion::V1): ErClient
    {
        return $this->makeErClientForMicrosoftSharepoint($version);
    }

    protected function makeErClientForMicrosoftSharepoint(ApiVersion $version = ApiVersion::V1): ErClient
    {
        return ErClient::make(
            clientId: $_ENV['MS_SP_CLIENT_ID'],
            clientSecret: $_ENV['MS_SP_CLIENT_SECRET'],
            version: $version,
        )->setBaseUrl($_ENV['BASE_URL']);
    }

    protected function makeErClientForHpeContentManager(ApiVersion $version = ApiVersion::V1): ErClient
    {
        return ErClient::make(
            clientId: $_ENV['HPE_CM_CLIENT_ID'],
            clientSecret: $_ENV['HPE_CM_CLIENT_SECRET'],
            version: $version,
        )->setBaseUrl($_ENV['BASE_URL']);
    }

    protected function getMicrosoftSharepointTestConfig(): array
    {
        return [
            'site_name' => $_ENV['MS_SP_SITE_NAME'],
            'channel_name' => $_ENV['MS_SP_CHANNEL_NAME'],
            'run_id' => $_ENV['MS_SP_RUN_ID'],
            'file_path' => $_ENV['TEST_FILE_PATH'],
            'large_file_path' => $_ENV['TEST_LARGE_FILE_PATH'],
        ];
    }

    protected function getHpeContentManagerTestConfig(): array
    {
        return [
            'record_type' => $_ENV['HPE_CM_RECORD_TYPE'],
            'container_id' => $_ENV['HPE_CM_CONTAINER_ID'],
            'author_id' => $_ENV['HPE_CM_AUTHOR_ID'],
            'author_email' => $_ENV['HPE_CM_AUTHOR_EMAIL'],

            'run_id' => $_ENV['HPE_CM_RUN_ID'],
            'file_path' => $_ENV['TEST_FILE_PATH'],
            'large_file_path' => $_ENV['TEST_LARGE_FILE_PATH'],
        ];
    }
}
