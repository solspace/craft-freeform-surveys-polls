<?php

namespace Solspace\SurveysPolls\migrations;

use Solspace\Commons\Migrations\ForeignKey;
use Solspace\Commons\Migrations\StreamlinedInstallMigration;
use Solspace\Commons\Migrations\Table;

/**
 * Install migration.
 */
class Install extends StreamlinedInstallMigration
{
    protected function defineTableData(): array
    {
        return [
            (new Table('freeform_surveys_view_settings'))
                ->addField('id', $this->primaryKey())
                ->addField('userId', $this->integer())
                ->addField('formId', $this->integer()->notNull())
                ->addField('fieldId', $this->integer()->notNull())
                ->addField('chartType', $this->string(200)->notNull())
                ->addForeignKey('userId', 'users', 'id', ForeignKey::CASCADE)
                ->addForeignKey('formId', 'freeform_forms', 'id', ForeignKey::CASCADE)
                ->addForeignKey('fieldId', 'freeform_fields', 'id', ForeignKey::CASCADE),
        ];
    }
}
