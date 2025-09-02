<?php

namespace Drupal\libguides\Plugin\views\query;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;

/**
 * Libguides views query plugin which wraps calls to the Libguides API in order to
 * expose the results to views.
 *
 * @ViewsQuery(
 *   id = "libguides_query",
 *   title = @Translation("Libguides Query"),
 *   help = @Translation("Query against the Libguides API.")
 * )
 */
class LibGuidesQuery extends QueryPluginBase
{
    use LoggerTrait;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * The logging channel to use.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * Search terms
     * 
     * @var string|null
     */
    public $search_terms;

    /**
     * Search operator
     * 
     * @var string|null
     */
    public $search_match;

    /**
     * Sort by
     * 
     * @var string|null
     */
    public $sort_by;

    /**
     * Status
     * 
     * @var string|null
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $client);
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('http_client'),
            $container->get('logger.channel.libguides')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ViewExecutable $view)
    {
        $view->initPager();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ViewExecutable $view)
    {
        try {
            $url = Url::fromUri('internal:/api-proxy/libguides_api_proxy', [
                'query' => [
                    '_api_proxy_uri' => '/guides' .
                        '?sort_by=' . $this->sort_by .
                        '&search_terms=' . $this->search_terms .
                        '&search_match=' . $this->search_match .
                        '&status=' . $this->status .
                        '&expand=owner,subjects,tags',
                ],
                'absolute' => TRUE,
            ])->toString(TRUE)->getGeneratedUrl();

            $response = $this->client->request(
                'GET',
                $url,
            );

            $data = json_decode($response->getBody()->getContents(), TRUE);
            $view->pager->total_items = count($data);

            $offset = $view->getOffset();
            $items_per_page = $view->getItemsPerPage();
            $current_page = $view->getCurrentPage();

            $data = array_slice($data, $offset + ($current_page * $items_per_page), $items_per_page);

            $index = 0;
            foreach ($data as $guide) {
                $row['id'] = $guide['id'];
                $row['type_id'] = $guide['type_id'];
                $row['site_id'] = $guide['site_id'];
                $row['owner_id'] = $guide['owner_id'];
                $row['owner_first_name'] = $guide['owner']['first_name'];
                $row['owner_last_name'] = $guide['owner']['last_name'];
                $row['owner_email'] = $guide['owner']['email'];
                $row['name'] = $guide['name'];
                $row['description'] = $guide['description'];
                $row['status'] = $guide['status'];
                $row['published'] = $guide['published'];
                $row['created'] = $guide['created'];
                $row['updated'] = $guide['updated'];
                $row['count_hit'] = $guide['count_hit'];
                $row['group_id'] = $guide['group_id'];
                $row['url'] = $guide['url'];
                $row['friendly_url'] = $guide['friendly_url'];
                $row['tags'] = isset($guide['tags']) ? implode(', ', array_column($guide['tags'], 'text')) : "";
                $row['subjects'] = isset($guide['subjects']) ? implode(', ', array_column($guide['subjects'], 'name')) : "";
                $row['search_term'] = $this->search_terms;

                $row['index'] = $index++;

                $view->result[] = new ResultRow($row);
            }

            $view->pager->updatePageInfo();
            $view->pager->postExecute($view->result);
            $view->total_rows = $view->pager->getTotalItems();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    /**
     * Views core assume an SQL-query backend. 
     * To mitigate that, we need to implement two methods which will, in a sense, ignore core Views as a way to work around this limitation.
     */
    public function ensureTable($table, $relationship = NULL)
    {
        return '';
    }

    public function addField($table, $field, $alias = '', $params = array())
    {
        return $field;
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
