<?php

return [
    'summary' => [
        'title' => 'Equipment Inspection SaaS',
        'description' => 'Temporary internal tracker for the admin surface while the product structure is still settling.',
    ],
    'focus' => [
        'Keep the split between super admin and workplace client surfaces clean.',
        'Stabilize inspections, QR flows, and workplace-scoped access rules.',
        'Avoid feature creep until the client portal and inspection flow are reliable.',
    ],
    'phases' => [
        [
            'title' => 'Core Platform',
            'status' => 'done',
            'items' => [
                'Custom authentication',
                'Super admin dashboard',
                'Workplace registry',
                'Equipment registry',
            ],
        ],
        [
            'title' => 'Client Portal',
            'status' => 'in_progress',
            'items' => [
                'Separate workplace layout',
                'Workplace dashboard',
                'Workplace equipment views',
                'Public registration and onboarding',
            ],
        ],
        [
            'title' => 'Inspection Operations',
            'status' => 'in_progress',
            'items' => [
                'Inspection templates',
                'Inspection submission flow',
                'Inspection result history',
            ],
        ],
        [
            'title' => 'Field Workflow',
            'status' => 'planned',
            'items' => [
                'Public or client-safe scan landing page',
                'QR regeneration workflow',
                'Mobile-friendly inspection flow',
            ],
        ],
    ],
    'features' => [
        ['label' => 'Custom session authentication', 'status' => 'done'],
        ['label' => 'Public-facing website', 'status' => 'done'],
        ['label' => 'Self-serve workplace registration', 'status' => 'done'],
        ['label' => 'Super admin dashboard shell', 'status' => 'done'],
        ['label' => 'Workplace CRUD', 'status' => 'done'],
        ['label' => 'Equipment CRUD', 'status' => 'done'],
        ['label' => 'Workplace client layout', 'status' => 'in_progress'],
        ['label' => 'Workplace dashboard', 'status' => 'in_progress'],
        ['label' => 'Client equipment views', 'status' => 'in_progress'],
        ['label' => 'Inspection templates', 'status' => 'done'],
        ['label' => 'Inspection submission flow', 'status' => 'done'],
        ['label' => 'QR code generation', 'status' => 'done'],
        ['label' => 'QR scanner page', 'status' => 'done'],
        ['label' => 'Client-safe scan destination', 'status' => 'planned'],
        ['label' => 'Workplace user management UI', 'status' => 'planned'],
        ['label' => 'Reports and analytics', 'status' => 'planned'],
    ],
];
