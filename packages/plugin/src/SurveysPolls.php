<?php
/**
 * Freeform Surveys & Polls plugin for Craft CMS 3.x.
 *
 * @see       https://solspace.com
 *
 * @copyright Copyright (c) 2022 Solspace
 */

namespace Solspace\SurveysPolls;

use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use Solspace\Commons\Helpers\PermissionHelper;
use Solspace\Freeform\Events\Fields\FetchFieldTypes;
use Solspace\Freeform\Events\Forms\Types\RegisterFormTypeEvent;
use Solspace\Freeform\Events\Freeform\RegisterCpSubnavItemsEvent;
use Solspace\Freeform\Events\Freeform\RegisterSettingsNavigationEvent;
use Solspace\Freeform\Fields\Pro\OpinionScaleField;
use Solspace\Freeform\Fields\Pro\RatingField;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Services\FieldsService;
use Solspace\Freeform\Services\FormTypesService;
use Solspace\Freeform\Services\SettingsService;
use Solspace\SurveysPolls\FormTypes\Survey;
use Solspace\SurveysPolls\models\Settings;
use Solspace\SurveysPolls\services\SurveyService;
use yii\base\Event;

/**
 * @property SurveyService $surveys
 */
class SurveysPolls extends Plugin
{
    public const PERMISSION_SURVEYS_ACCESS = 'freeform-surveys-access';
    public const PERMISSION_REPORTS_MANAGE = 'freeform-reports-manage';

    /** @var SurveysPolls */
    public static $plugin;

    /** @var string */
    public $schemaVersion = '1.0.0';

    /** @var bool */
    public $hasCpSettings = true;

    /** @var bool */
    public $hasCpSection = false;

    public static function t(string $message, array $params = [], string $language = null): string
    {
        return \Craft::t('freeform-surveys-polls', $message, $params, $language);
    }

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        if (!class_exists('Solspace\Freeform\Services\FormTypesService')) {
            return;
        }

        $this->setComponents(['surveys' => SurveyService::class]);

        Event::on(
            FormTypesService::class,
            FormTypesService::EVENT_REGISTER_FORM_TYPES,
            function (RegisterFormTypeEvent $event) {
                $event->addType(Survey::class);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge($event->rules, [
                    'freeform/surveys-and-polls' => 'freeform-surveys-polls/surveys',
                    'freeform/surveys-and-polls/export/pdf' => 'freeform-surveys-polls/export/pdf',
                    'freeform/surveys-and-polls/export/images' => 'freeform-surveys-polls/export/images',
                    'freeform/surveys-and-polls/<handle:[a-zA-Z0-9\-_]+>' => 'freeform-surveys-polls/surveys/results',
                    'freeform/surveys-and-polls/<handle:[a-zA-Z0-9\-_]+>/settings' => 'freeform-surveys-polls/surveys/settings',
                    'freeform/surveys-and-polls/<handle:[a-zA-Z0-9\-_]+>/response-data' => 'freeform-surveys-polls/surveys/response-data',
                    'freeform/settings/form-types/surveys-and-polls' => 'freeform-surveys-polls/settings',
                ]);
            }
        );

        if (PermissionHelper::checkPermission(self::PERMISSION_SURVEYS_ACCESS)) {
            Event::on(
                Freeform::class,
                Freeform::EVENT_REGISTER_SUBNAV_ITEMS,
                function (RegisterCpSubnavItemsEvent $event) {
                    $event->addSubnavItem(
                        'surveys-and-polls',
                        'Surveys & Polls',
                        UrlHelper::cpUrl('freeform/surveys-and-polls'),
                        'forms'
                    );
                }
            );

            Event::on(
                SettingsService::class,
                SettingsService::EVENT_REGISTER_SETTINGS_NAVIGATION,
                function (RegisterSettingsNavigationEvent $event) {
                    $event->addHeader('form-types', Freeform::t('Form Types'), 'demo-templates');
                    $event->addNavigationItem('form-types/surveys-and-polls', 'Surveys & Polls', 'form-types');
                }
            );
        }

        Event::on(
            FieldsService::class,
            FieldsService::EVENT_FETCH_TYPES,
            function (FetchFieldTypes $event) {
                $event
                    ->addType(OpinionScaleField::class)
                    ->addType(RatingField::class)
                ;
            }
        );

        if (\Craft::$app->getEdition() >= \Craft::Pro) {
            Event::on(
                UserPermissions::class,
                UserPermissions::EVENT_REGISTER_PERMISSIONS,
                function (RegisterUserPermissionsEvent $event) {
                    $permissions = [
                        self::PERMISSION_SURVEYS_ACCESS => [
                            'label' => self::t('Access Surveys & Polls'),
                            'info' => self::t('Allow access to the Surveys & Polls dashboard and reports pages. Users will still need valid form permissions to administrate forms and valid submission permissions to view individual survey responses.'),
                            'nested' => [
                                self::PERMISSION_REPORTS_MANAGE => [
                                    'label' => self::t('Manage Reports'),
                                    'info' => self::t('Allow changes to the chart style for each question in Surveys & Polls reports.'),
                                ],
                            ],
                        ],
                    ];

                    if (!isset($event->permissions[Freeform::PERMISSION_NAMESPACE])) {
                        $event->permissions[Freeform::PERMISSION_NAMESPACE] = [];
                    }

                    $event->permissions[Freeform::PERMISSION_NAMESPACE] = array_merge(
                        $event->permissions[Freeform::PERMISSION_NAMESPACE],
                        $permissions
                    );
                }
            );
        }
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        return \Craft::$app->getView()->renderTemplate('freeform-surveys-polls/settings/_redirect');
    }
}
