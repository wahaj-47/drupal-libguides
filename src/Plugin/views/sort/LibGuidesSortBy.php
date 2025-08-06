<?php

namespace Drupal\libguides\Plugin\views\sort;

use Drupal\libguides\Plugin\views\LibGuidesHandlerTrait;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Provides a sort plugin for LibGuides views.
 *
 * @ViewsSort("libguides_sort_by")
 */
class LibGuidesSortBy extends SortPluginBase
{

    use LibGuidesHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['order']['default'] = 'relevance';

        return $options;
    }

    /**
     * Provide a list of options for the default sort form.
     *
     * Should be overridden by classes that don't override sort_form
     */
    protected function sortOptions()
    {
        return [
            'name' => $this->t('Sort by guide name'),
            'count_hit' => $this->t('Sort by guide hits since the beginning of the year'),
            'published' => $this->t('Sort by published date'),
            'relevance' => $this->t('Sort by relevance'),
        ];
    }

    /**
     * Display whether or not the sort order is ascending or descending.
     */
    public function adminSummary()
    {
        if (!empty($this->options['exposed'])) {
            return $this->t('Exposed');
        }
        switch ($this->options['order']) {
            case 'name';
                return $this->t('Name');

            case 'count_hit';
                return $this->t('Hits');

            case 'published';
                return $this->t('Published date');

            case 'relevance':
            default:
                return $this->t('Relevance');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $this->getQuery()->sort_by = $this->options['order'];
    }
}
