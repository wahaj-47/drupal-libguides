<?php

namespace Drupal\libguides\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\libguides\Plugin\views\LibGuidesHandlerTrait;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Filter by guide statuses.
 * 
 * @ViewsFilter("libguides_status")
 */
class LibGuidesStatus extends InOperator
{

    use LibGuidesHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        $this->valueOptions = [
            $this->t('Unpublished'),
            $this->t('Published'),
            $this->t("Private"),
        ];

        return $this->valueOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function operators()
    {
        $operators = [
            'in' => [
                'title' => $this->t('Is one of'),
                'short' => $this->t('in'),
                'short_single' => $this->t('='),
                'method' => 'opSimple',
                'values' => 1,
            ],
        ];
        return $operators;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = $this->getQuery();
        if (isset($this->value['all'])) unset($this->value['all']);
        $query->status = implode(',', $this->value);
    }
}
