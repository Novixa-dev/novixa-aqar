<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class DocumentTemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'type' => 'yemen_residential_lease_agreement',
                'name' => 'Yemeni Residential Lease Agreement (Demo Template)',
                'description' => 'Illustrative Yemen-appropriate residential lease agreement template — demo only, not legal advice',
                'view_path' => 'document_templates.yemen_residential_lease_agreement',
            ],
            [
                'type' => 'lease_breach_notice',
                'name' => 'Lease Breach Notice (Demo Template)',
                'description' => 'Illustrative notice for a tenant breach of lease terms — demo only, not legal advice',
                'view_path' => 'document_templates.lease_breach_notice',
            ],
            [
                'type' => 'end_of_lease_notice',
                'name' => 'End of Lease Notice (Demo Template)',
                'description' => 'Illustrative notice for end-of-term lease vacation — demo only, not legal advice',
                'view_path' => 'document_templates.end_of_lease_notice',
            ],
            // Add more templates here as needed
        ];

        foreach ($templates as $template) {
            DocumentTemplate::findOrCreateTemplate(
                $template['type'],
                $template['name'],
                $template['description'],
                $template['view_path']
            );
        }
    }
}
