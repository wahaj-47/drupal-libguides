<?php

namespace Drupal\libguides\Plugin\views;

use Drupal\libguides\Plugin\views\query\LibGuidesQuery;

trait LibGuidesHandlerTrait
{

    /**
     * Retrieves the query plugin.
     *
     * @return \Drupal\libguides\Plugin\views\query\LibGuidesQuery|null
     *   The query plugin, or NULL if there is no query or it is no Search API
     *   query.
     */
    public function getQuery()
    {
        $query = $this->query ?? $this->view->query ?? NULL;
        return $query instanceof LibGuidesQuery ? $query : NULL;
    }
}
