<?php

namespace Drupal\libguides\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\libguides\Plugin\views\LibGuidesHandlerTrait;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter by searching guide name and description. When sort_by=relevance is specified, the full-text index is searched.
 * 
 * @ViewsFilter("libguides_search_terms")
 */
class LibGuidesSearchTerms extends FilterPluginBase
{
    use LibGuidesHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function showOperatorForm(&$form, FormStateInterface $form_state)
    {
        parent::showOperatorForm($form, $form_state);

        if (!empty($form['operator'])) {
            $form['operator']['#description'] = $this->t('Only applies when searching by name and descriptions. Sorting by relevance searches the full text index and disables this operator');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function operatorOptions($which = 'title')
    {
        $options = [];
        foreach ($this->operators() as $id => $info) {
            $options[$id] = $info[$which];
        }

        return $options;
    }

    /**
     * Returns information about the available operators for this filter.
     *
     * @return array[]
     *   An associative array mapping operator identifiers to their information.
     *   The operator information itself is an associative array with the
     *   following keys:
     *   - title: The translated title for the operator.
     *   - short: The translated short title for the operator.
     *   - values: The number of values the operator requires as input.
     */
    public function operators()
    {
        return [
            'and' => [
                'title' => $this->t('Contains all of these words'),
                'short' => $this->t('and'),
                'values' => 1,
            ],
            'or' => [
                'title' => $this->t('Contains any of these words'),
                'short' => $this->t('or'),
                'values' => 1,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['operator']['default'] = 'and';

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function valueForm(&$form, FormStateInterface $form_state)
    {
        parent::valueForm($form, $form_state);

        $form['value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Value'),
            '#default_value' => $this->value,
        ];
    }

    public function query()
    {
        $query = $this->getQuery();

        // Pass any data you need to your query plugin.
        $query->search_terms = reset($this->value);
        $query->search_match = $this->operator;
    }
}
