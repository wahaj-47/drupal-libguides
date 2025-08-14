<?php

namespace Drupal\libguides\Plugin\views\row;

use Drupal\views\Plugin\views\row\RowPluginBase;

/**
 * Renders API data in a custom Twig template.
 *
 * @ViewsRow(
 *   id = "libguide",
 *   title = @Translation("Libguide"),
 *   help = @Translation("Render Libguide API results using a Twig template.")
 * )
 */
class LibguidesRow extends RowPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function render($row)
    {
        return [
            '#theme' => 'libguide',
            '#name' => $row->name,
            '#description' => $row->description,
            '#url' => $row->url,
            '#friendly_url' => $row->friendly_url,
            '#search_term' => $row->search_term,
            '#view' => $this->view,
        ];
    }
}
