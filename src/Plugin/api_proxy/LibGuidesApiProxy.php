<?php

namespace Drupal\libguides\Plugin\api_proxy;

use Drupal\api_proxy\Plugin\api_proxy\HttpApiCommonConfigs;
use Drupal\api_proxy\Plugin\HttpApiPluginBase;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\SubformStateInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * The LibGuides API.
 *
 * @HttpApi(
 *   id = "libguides_api_proxy",
 *   label = @Translation("LibGuides API"),
 *   description = @Translation("Proxies requests to the LibGuides API."),
 *   serviceUrl = "https://lgapi-us.libapps.com/1.2",
 * )
 */
final class LibGuidesApiProxy extends HttpApiPluginBase
{

    use HttpApiCommonConfigs;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Translates between Symfony and PRS objects.
     *
     * @var \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface
     */
    private $foundationFactory;

    /**
     * Cache
     * 
     * @var \Drupal\Core\Cache\CacheBackendInterface
     */
    private $cache;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client, HttpFoundationFactoryInterface $foundation_factory, CacheBackendInterface $cache)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $client, $foundation_factory);
        $this->client = $client;
        $this->foundationFactory = $foundation_factory;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self
    {
        $settings = $container->get('config.factory')
            ->get('api_proxy.settings')
            ->get('api_proxies');
        $plugin_settings = empty($settings[$plugin_id]) ? [] : $settings[$plugin_id];
        $configuration = array_merge($plugin_settings, $configuration);

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('http_client'),
            $container->get('psr7.http_foundation_factory'),
            $container->get('cache.api_proxy')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addMoreConfigurationFormElements(array $form, SubformStateInterface $form_state): array
    {
        $form['client_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Client ID'),
            '#default_value' => $this->configuration['client_id'] ?? "",
        ];
        $form['client_secret'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Client Secret'),
            '#default_value' => $this->configuration['client_secret'] ?? "",
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    protected function calculateHeaders(array $headers): array
    {
        $default_headers = parent::calculateHeaders($headers);
        $access_token = $this->getAccessToken();

        return array_merge(
            $default_headers,
            [
                'authorization' => ['Bearer ' . $access_token],
                'accept' => ['application/json'],
                'content-type' => ['application/json'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postprocessOutgoing(Response $response): Response
    {
        // Modify the response from the API.
        // A common problem is to remove the Transfer-Encoding header.
        $response->headers->remove('transfer-encoding');
        return $response;
    }

    /**
     * Fetch access token
     */
    private function getAccessToken(): string
    {
        $cid = 'libguides.access_token';
        if ($cache = $this->cache->get($cid)) {
            return $cache->data;
        }

        $client_id = $this->getConfiguration()['client_id'];
        $client_secret = $this->getConfiguration()['client_secret'];
        $endpoint = rtrim($this->getBaseUrl(), '/') . '/oauth/token';

        $psr7_response = $this->client->request(
            'post',
            $endpoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => "client_credentials",
                ]
            ]
        );

        $response = $this->foundationFactory->createResponse($psr7_response);
        $data = json_decode($response->getContent(), true);

        if (!isset($data['access_token'])) {
            \Drupal::logger('libguides')->error('Access token missing in response: @resp', [
                '@resp' => print_r($data, true),
            ]);
            throw new \RuntimeException('Failed to retrieve access token');
        }

        $access_token = $data['access_token'];
        $expires_in = $data['expires_in'] ?? 3600;

        $this->cache->set($cid, $access_token, time() + $expires_in);

        return $access_token;
    }
}
