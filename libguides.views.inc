<?php

/**
 * Implements hook_views_data().
 */
function libguides_views_data()
{
    $data = [];

    // Base data.
    $data['libguides_guides']['table']['group'] = t('LibGuide');
    $data['libguides_guides']['table']['base'] = [
        'title' => t('Libguides guide'),
        'help' => t('Libguides guide data provided by the Libguides API\'s guides endpoint.'),
        'query_id' => 'libguides_query',
    ];

    $data['libguides_guides']['libguides_search'] = [
        'group' => t('Search'),
        'title' => t('LibGuides Search'),
        'help' => t('Filter by searching guide name and description. When sort_by=relevance is specified, the full-text index is searched.'),
        'filter' => [
            'id' => 'libguides_search_terms',
        ],
    ];

    $data['libguides_guides']['libguides_sort_by'] = [
        'title' => t('LibGuides Sort By'),
        'help' => t('Sorting of returned guides.'),
        'sort' => [
            'id' => 'libguides_sort_by',
        ],
    ];

    // Fields
    $data['libguides_guides']['id'] = [
        'title' => t('ID'),
        'help' => t('Guide ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['type_id'] = [
        'title' => t('Type ID'),
        'help' => t('Guide type ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['site_id'] = [
        'title' => t('Site ID'),
        'help' => t('Guide site ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    // Owner
    $data['libguides_guides']['owner_id'] = [
        'title' => t('Owner ID'),
        'help' => t('Owner ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['owner_first_name'] = [
        'title' => t('Owner First Name'),
        'help' => t('Owner First Name'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['owner_last_name'] = [
        'title' => t('Owner Last Name'),
        'help' => t('Owner Last Name'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['owner_email'] = [
        'title' => t('Owner Email'),
        'help' => t('Owner Email'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    $data['libguides_guides']['name'] = [
        'title' => t('Name'),
        'help' => t('Guide name.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['description'] = [
        'title' => t('Description'),
        'help' => t('Guide description.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['status'] = [
        'title' => t('Status'),
        'help' => t('Guide status'),
        'field' => [
            'id' => 'standard',
            'value' => 1
        ],
        'filter' => [
            'id' => 'libguides_status',
        ]
    ];
    $data['libguides_guides']['published'] = [
        'title' => t('Published'),
        'help' => t('Guide published date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['created'] = [
        'title' => t('Created'),
        'help' => t('Guide created date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['updated'] = [
        'title' => t('Updated'),
        'help' => t('Guide updated date.'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['count_hit'] = [
        'title' => t('Hits'),
        'help' => t('Guide hit count'),
        'field' => [
            'id' => 'numeric',
        ],
    ];
    $data['libguides_guides']['group_id'] = [
        'title' => t('Group ID'),
        'help' => t('Guide group ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['url'] = [
        'title' => t('URL'),
        'help' => t('Guide URL'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['friendly_url'] = [
        'title' => t('Friendly URL'),
        'help' => t('Guide friendly URL'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    // Metadata
    $data['libguides_guides']['tags'] = [
        'title' => t('Tags'),
        'help' => t('Tags'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['libguides_guides']['subjects'] = [
        'title' => t('Subjects'),
        'help' => t('Subjects'),
        'field' => [
            'id' => 'standard',
        ],
    ];

    return $data;
}
