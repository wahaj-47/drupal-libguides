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
            '#owner_first_name' => $row->owner_first_name,
            '#owner_last_name' => $row->owner_last_name,
            '#owner_email' => $row->owner_email,
            '#url' => $row->url,
            '#friendly_url' => $row->friendly_url,
            '#tags' => $row->tags,
            '#subjects' => $row->subjects,
            '#search_term' => $row->search_term,
            '#view' => $this->view,
        ];
    }
}
